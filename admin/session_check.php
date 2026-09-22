<?php
/**
 * session_check.php
 * -------------------
 * Lets a static admin.html ask "am I logged in?" without exposing any
 * data. Returns { loggedIn: true } or { loggedIn: false }.
 */

header('Content-Type: application/json');
require __DIR__ . '/admin_auth.php';

echo json_encode(['loggedIn' => is_admin_logged_in()]);
