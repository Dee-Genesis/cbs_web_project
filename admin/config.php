<?php
/**
 * config.php
 * ----------
 * Database credentials, kept out of verify.php on purpose.
 *
 * IMPORTANT:
 * - Rotate the DB password before using this — the old one was pasted
 *   into a chat conversation and should be treated as compromised.
 * - Ideally move this file ONE LEVEL ABOVE your public web root
 *   (e.g. /home/aietglob/config.php instead of
 *   /home/aietglob/cbsedu.us/cbs/config.php) so it can never be
 *   downloaded directly even if PHP misconfigures and serves source.
 *   If you do that, update the require path in verify.php to match
 *   (e.g. require dirname(__DIR__) . '/config.php';).
 */

return [
    'host' => 'localhost',
    'user' => 'aietglob_cbs',
    'pass' => '=9HfS$WRHZ&_b.b8',
    'name' => 'aietglob_cbs',
];
