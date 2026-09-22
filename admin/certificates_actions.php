<?php
/**
 * certificates_actions.php
 * --------------------------
 * JSON API for the Certification tab of admin/index.html.
 *
 *   GET  ?action=list                    -> { ok: true, rows: [...] }
 *   POST action=create&memberNo=...&...  -> { ok: true } or { ok: false, message }
 *   POST action=delete&memberNo=X        -> { ok: true } or { ok: false, message }
 *
 * Table: `certificates` — columns confirmed via phpMyAdmin: id (auto),
 * memberNo, certificate, recordType, award, dateIssued, expiryDate,
 * renewalFee. `certificate` is a plain text field (e.g. an award title) —
 * there is no file upload here, matching how vv.html's verification
 * portal never generates or handles an actual file.
 */

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
ini_set('display_errors', '0');
error_reporting(E_ALL);

require __DIR__ . '/admin_auth.php';
require_admin_api();

function json_fail(int $httpCode, string $message): void {
    http_response_code($httpCode);
    echo json_encode(['ok' => false, 'message' => $message]);
    exit;
}

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("certificates_actions.php PHP error: $errstr in $errfile:$errline");
    json_fail(500, 'Something went wrong on our end. Please try again shortly.');
});
set_exception_handler(function (Throwable $e) {
    error_log('certificates_actions.php uncaught exception: ' . $e->getMessage());
    json_fail(500, 'Something went wrong on our end. Please try again shortly.');
});

$config = require __DIR__ . '/config.php';
$link = @mysqli_connect($config['host'], $config['user'], $config['pass'], $config['name']);
if (!$link) {
    json_fail(500, 'There was a problem connecting to the server. Please try again later.');
}
mysqli_set_charset($link, 'utf8mb4');

$VALID_RECORD_TYPES = ['certification', 'degree', 'diploma'];

$action = $_REQUEST['action'] ?? '';

// ---- List all certificates -------------------------------------------------
if ($action === 'list' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = mysqli_query(
        $link,
        "SELECT memberNo, certificate, recordType, award, dateIssued, expiryDate, renewalFee
           FROM `certificates`
          ORDER BY id DESC"
    );
    if (!$result) {
        error_log('certificates_actions.php list query failed: ' . mysqli_error($link));
        json_fail(500, 'Could not load certificate records.');
    }
    echo json_encode(['ok' => true, 'rows' => mysqli_fetch_all($result, MYSQLI_ASSOC)]);
    exit;
}

// ---- Create a new certificate record ---------------------------------------
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberNo    = trim($_POST['memberNo'] ?? '');
    $certificate = trim($_POST['certificate'] ?? '');
    $recordType  = trim($_POST['recordType'] ?? '');
    $award       = trim($_POST['award'] ?? '');
    $dateIssued  = trim($_POST['dateIssued'] ?? '');
    $expiryDate  = trim($_POST['expiryDate'] ?? '');
    $renewalFee  = trim($_POST['renewalFee'] ?? '');

    if ($memberNo === '' || $certificate === '') {
        json_fail(400, 'Reference number and certificate/award name are required.');
    }
    if ($recordType !== '' && !in_array($recordType, $VALID_RECORD_TYPES, true)) {
        json_fail(400, 'Record type must be Certification, Degree, or Diploma.');
    }

    // Prevent duplicate reference numbers.
    $check = mysqli_prepare($link, "SELECT id FROM `certificates` WHERE memberNo = ? LIMIT 1");
    mysqli_stmt_bind_param($check, 's', $memberNo);
    mysqli_stmt_execute($check);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($check)) !== null;
    mysqli_stmt_close($check);
    if ($exists) {
        json_fail(409, 'A record with that reference number already exists.');
    }

    $stmt = mysqli_prepare(
        $link,
        "INSERT INTO `certificates` (memberNo, certificate, recordType, award, dateIssued, expiryDate, renewalFee)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    if (!$stmt) {
        error_log('certificates_actions.php prepare (insert) failed: ' . mysqli_error($link));
        json_fail(500, 'Something went wrong on our end. Please try again shortly.');
    }
    mysqli_stmt_bind_param($stmt, 'sssssss', $memberNo, $certificate, $recordType, $award, $dateIssued, $expiryDate, $renewalFee);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$success) {
        error_log('certificates_actions.php insert failed: ' . mysqli_error($link));
        json_fail(500, 'Could not save the new record.');
    }

    echo json_encode(['ok' => true]);
    exit;
}

// ---- Delete a certificate ----------------------------------------------------
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberNo = trim($_POST['memberNo'] ?? '');
    if ($memberNo === '') {
        json_fail(400, 'Missing member/reference number.');
    }

    $stmt = mysqli_prepare($link, "DELETE FROM `certificates` WHERE memberNo = ? LIMIT 1");
    if (!$stmt) {
        error_log('certificates_actions.php prepare (delete) failed: ' . mysqli_error($link));
        json_fail(500, 'Something went wrong on our end. Please try again shortly.');
    }
    mysqli_stmt_bind_param($stmt, 's', $memberNo);
    mysqli_stmt_execute($stmt);
    $deleted = mysqli_stmt_affected_rows($stmt) > 0;
    mysqli_stmt_close($stmt);

    if (!$deleted) {
        json_fail(404, 'No certificate found with that number — it may already be deleted.');
    }

    echo json_encode(['ok' => true]);
    exit;
}

json_fail(400, 'Unknown or unsupported request.');
