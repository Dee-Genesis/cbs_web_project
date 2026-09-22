<?php
/**
 * verify.php
 * ----------
 * JSON backend for vv.html. Receives POST { type, number } and returns
 * { valid: bool, record?: {...}, limited?: bool, message?: string }.
 *
 * Confirmed via phpMyAdmin (2026-09-20): both `cilgusers` (plural) and
 * `certificates` live in the `aietglob_cbs` database — NOT
 * `aietglob_cimlgb` as the previous version of this file assumed, and
 * NOT `cilguser` singular. Both mistakes would have made every lookup
 * silently fail ("Not Valid") even for real numbers.
 *
 * `certificates` already has recordType / award / dateIssued /
 * expiryDate / renewalFee columns — no ALTER TABLE needed. They're just
 * NULL on all existing rows, so this file treats a NULL recordType as
 * "not yet categorized" (shows it regardless of tab) and a NULL
 * award/date/fee as "not on file yet" (omits that field and marks the
 * response `limited`). As you backfill recordType on real records,
 * cross-tab matches (e.g. a diploma number checked on the Degree tab)
 * will start being correctly rejected automatically — no code change
 * needed.
 */

// ---- 0. Always answer in JSON, even when something goes wrong ----------
header('Content-Type: application/json');
ini_set('display_errors', '0'); // never let a raw PHP warning leak HTML into a JSON response
error_reporting(E_ALL);

function json_fail(int $httpCode, string $message): void {
    http_response_code($httpCode);
    echo json_encode(['valid' => false, 'message' => $message]);
    exit;
}

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("verify.php PHP error: $errstr in $errfile:$errline");
    json_fail(500, 'Something went wrong on our end. Please try again shortly.');
});

set_exception_handler(function (Throwable $e) {
    error_log('verify.php uncaught exception: ' . $e->getMessage());
    json_fail(500, 'Something went wrong on our end. Please try again shortly.');
});

// ---- 1. Config + connection --------------------------------------------
// Adjust this path if you move config.php outside the web root (recommended).
$config = require __DIR__ . '/config.php';

$link = @mysqli_connect($config['host'], $config['user'], $config['pass'], $config['name']);
if (!$link) {
    json_fail(500, 'There was a problem connecting to the server. Please try again later.');
}
mysqli_set_charset($link, 'utf8mb4');

// Fixed annual-subscription-by-grade lookup — matches memberDetails.php.
// TODO: confirm these against the real DISTINCT memberGrade values in
// `cilgusers` — currently unverified. Unrecognised grades fall back to
// 'Not on record' rather than guessing.
$SUBSCRIPTION_BY_GRADE = [
    'Affiliate' => '$20',
    'Graduate'  => '$20',
    'Associate' => '$32',
    'Full'      => '$44',
    'Fellow'    => '$63',
];

// ---- 2. Read & validate input -------------------------------------------
$type   = $_POST['type']   ?? '';
$number = trim($_POST['number'] ?? '');

$validTypes = ['membership', 'certification', 'degree', 'diploma'];
if (!in_array($type, $validTypes, true)) {
    json_fail(400, 'Unknown verification type.');
}

if ($number === '') {
    json_fail(400, 'Please enter a reference number.');
}

if (mb_strlen($number) > 64) {
    json_fail(400, 'That reference number looks too long to be valid.');
}

// ---- 3. Membership: full detail from cilgusers ---------------------------
if ($type === 'membership') {
    $stmt = mysqli_prepare(
        $link,
        "SELECT fullName, memberGrade, regDate, exDate FROM `cilgusers` WHERE memberNo = ? LIMIT 1"
    );
    if (!$stmt) {
        error_log('verify.php prepare failed: ' . mysqli_error($link));
        json_fail(500, 'Something went wrong on our end. Please try again shortly.');
    }
    mysqli_stmt_bind_param($stmt, 's', $number);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$row) {
        echo json_encode([
            'valid'   => false,
            'message' => 'We could not find a membership record for that number. Please check it and try again.',
        ]);
        exit;
    }

    $grade = trim((string) ($row['memberGrade'] ?? ''));
    echo json_encode([
        'valid'  => true,
        'record' => [
            'fullName'           => $row['fullName'],
            'memberGrade'        => $grade,
            'dateIssued'         => $row['regDate'],
            'expiryDate'         => $row['exDate'],
            'annualSubscription' => $SUBSCRIPTION_BY_GRADE[$grade] ?? 'Not on record',
        ],
    ]);
    exit;
}

// ---- 4. Certification / Degree / Diploma: certificates table -------------
$stmt = mysqli_prepare(
    $link,
    "SELECT certificate, recordType, award, dateIssued, expiryDate, renewalFee
       FROM `certificates`
      WHERE memberNo = ?
      LIMIT 1"
);
if (!$stmt) {
    error_log('verify.php prepare failed: ' . mysqli_error($link));
    json_fail(500, 'Something went wrong on our end. Please try again shortly.');
}
mysqli_stmt_bind_param($stmt, 's', $number);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$row) {
    echo json_encode([
        'valid'   => false,
        'message' => 'We could not find a matching ' . $type . ' record for that number. Please check it and try again.',
    ]);
    exit;
}

// Once recordType is filled in on a row, enforce that it matches the tab
// the person searched from. While it's still NULL (true for all 146
// current rows), we can't tell tabs apart yet, so we show the record
// regardless of which tab was used.
if ($row['recordType'] !== null && $row['recordType'] !== $type) {
    echo json_encode([
        'valid'   => false,
        'message' => 'That number belongs to a different record type. Please try the correct tab.',
    ]);
    exit;
}

// Only include fields that actually have a value and are still shown on
// the frontend (Full Name, Program, Date Issued — Expiry Date and Renewal
// Fee were retired from the admin dashboard and verification result).
$record = ['certificateFile' => $row['certificate']];
$isLimited = false;
foreach (['award', 'dateIssued'] as $field) {
    if ($row[$field] !== null && $row[$field] !== '') {
        $record[$field] = $row[$field];
    } else {
        $isLimited = true;
    }
}

echo json_encode([
    'valid'   => true,
    'limited' => $isLimited,
    'record'  => $record,
]);
