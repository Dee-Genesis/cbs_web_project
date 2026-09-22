<?php
/**
 * admin_auth.php
 * ---------------
 * Shared login check. All files here (index.html via session_check.php,
 * membership_actions.php, certificates_actions.php) live together in
 * /admin, alongside login.php.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_admin_logged_in(): bool {
    return array_key_exists('admin', $_SESSION);
}

function require_admin_api(): void {
    if (!is_admin_logged_in()) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['ok' => false, 'message' => 'Not signed in.']);
        exit;
    }
}
