<?php
/**
 * membership_actions.php
 * -----------------------
 * JSON API for the Membership tab of admin/index.html.
 *
 *   GET  ?action=list                    -> { ok: true, rows: [...] }
 *   POST action=create&fullName=...&...  -> { ok: true } or { ok: false, message }
 *   POST action=delete&memberNo=X        -> { ok: true } or { ok: false, message }
 *
 * Table: `cilgusers` — columns confirmed via phpMyAdmin: id (auto),
 * fullName, memberNo, memberGrade, programme, special, regDate, exDate.
 */

header('Content-Type: application/json');
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
    error_log("membership_actions.php PHP error: $errstr in $errfile:$errline");
    json_fail(500, 'Something went wrong on our end. Please try again shortly.');
});
set_exception_handler(function (Throwable $e) {
    error_log('membership_actions.php uncaught exception: ' . $e->getMessage());
    json_fail(500, 'Something went wrong on our end. Please try again shortly.');
});

$config = require __DIR__ . '/config.php';
$link = @mysqli_connect($config['host'], $config['user'], $config['pass'], $config['name']);
if (!$link) {
    json_fail(500, 'There was a problem connecting to the server. Please try again later.');
}
mysqli_set_charset($link, 'utf8mb4');

$action = $_REQUEST['action'] ?? '';

// ---- List all members ----------------------------------------------------
if ($action === 'list' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = mysqli_query(
        $link,
        "SELECT memberNo, fullName, memberGrade, programme, special, regDate, exDate
           FROM `cilgusers`
          ORDER BY fullName ASC"
    );
    if (!$result) {
        error_log('membership_actions.php list query failed: ' . mysqli_error($link));
        json_fail(500, 'Could not load membership records.');
    }
    echo json_encode(['ok' => true, 'rows' => mysqli_fetch_all($result, MYSQLI_ASSOC)]);
    exit;
}

// ---- Create a new member ---------------------------------------------------
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName    = trim($_POST['fullName'] ?? '');
    $memberNo    = trim($_POST['memberNo'] ?? '');
    $memberGrade = trim($_POST['memberGrade'] ?? '');
    $programme   = trim($_POST['programme'] ?? '');
    $special     = trim($_POST['special'] ?? '');
    $regDate     = trim($_POST['regDate'] ?? '');
    $exDate      = trim($_POST['exDate'] ?? '');

    if ($fullName === '' || $memberNo === '') {
        json_fail(400, 'Full name and member number are required.');
    }

    // Prevent duplicate member numbers.
    $check = mysqli_prepare($link, "SELECT id FROM `cilgusers` WHERE memberNo = ? LIMIT 1");
    mysqli_stmt_bind_param($check, 's', $memberNo);
    mysqli_stmt_execute($check);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($check)) !== null;
    mysqli_stmt_close($check);
    if ($exists) {
        json_fail(409, 'A member with that member number already exists.');
    }

    $stmt = mysqli_prepare(
        $link,
        "INSERT INTO `cilgusers` (fullName, memberNo, memberGrade, programme, special, regDate, exDate)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    if (!$stmt) {
        error_log('membership_actions.php prepare (insert) failed: ' . mysqli_error($link));
        json_fail(500, 'Something went wrong on our end. Please try again shortly.');
    }
    mysqli_stmt_bind_param($stmt, 'sssssss', $fullName, $memberNo, $memberGrade, $programme, $special, $regDate, $exDate);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$success) {
        error_log('membership_actions.php insert failed: ' . mysqli_error($link));
        json_fail(500, 'Could not save the new member.');
    }

    echo json_encode(['ok' => true]);
    exit;
}

// ---- Delete a member -------------------------------------------------------
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberNo = trim($_POST['memberNo'] ?? '');
    if ($memberNo === '') {
        json_fail(400, 'Missing member number.');
    }

    $stmt = mysqli_prepare($link, "DELETE FROM `cilgusers` WHERE memberNo = ? LIMIT 1");
    if (!$stmt) {
        error_log('membership_actions.php prepare (delete) failed: ' . mysqli_error($link));
        json_fail(500, 'Something went wrong on our end. Please try again shortly.');
    }
    mysqli_stmt_bind_param($stmt, 's', $memberNo);
    mysqli_stmt_execute($stmt);
    $deleted = mysqli_stmt_affected_rows($stmt) > 0;
    mysqli_stmt_close($stmt);

    if (!$deleted) {
        json_fail(404, 'No member found with that number — it may already be deleted.');
    }

    echo json_encode(['ok' => true]);
    exit;
}

json_fail(400, 'Unknown or unsupported request.');
