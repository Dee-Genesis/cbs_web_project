<?php
  session_start();
  if (array_key_exists("logout", $_GET)) {
    unset($_SESSION['admin']);
    session_destroy();
  } else if (array_key_exists("admin", $_SESSION)) {
    header("Location: index.html");
    exit;
  }

  $error = "";
  if (array_key_exists('username', $_POST) OR array_key_exists('password', $_POST)) {
    $config = require __DIR__ . '/config.php';
    $link = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['name']);
    if (mysqli_connect_error()) {
      die("There was an error connecting to the database");
    }

    if ($_POST['username'] == '') {
      $error = "Username is required.";
    } else if ($_POST['password'] == '') {
      $error = "Password is required.";
    } else {
      // NOTE: the `admin` table currently stores the password in plain
      // text (confirmed via phpMyAdmin), so this is a plain comparison,
      // not password_verify(). If you later hash it with password_hash(),
      // switch this to password_verify($_POST['password'], $row['password']).
      $stmt = mysqli_prepare($link, "SELECT `password` FROM `admin` WHERE username = ? LIMIT 1");
      mysqli_stmt_bind_param($stmt, 's', $_POST['username']);
      mysqli_stmt_execute($stmt);
      $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
      mysqli_stmt_close($stmt);

      if ($row && hash_equals((string) $row['password'], $_POST['password'])) {
        $_SESSION['admin'] = $_POST['username'];
        header("Location: index.html");
        exit;
      } else {
        $error = "Incorrect username or password.";
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Admin Sign In — CBS</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<style>
:root {
  --navy:   #0b1e3d;
  --gold:   #c9972e;
  --gold-lt:#f0c86a;
  --white:  #ffffff;
  --off:    #f5f5f0;
  --text:   #2d2d2d;
  --muted:  #6b7280;
  --border: #e0ddd6;
  --radius: 6px;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DM Sans', sans-serif; color: var(--text); background: var(--off); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
.login-card { background: var(--white); border-radius: 12px; box-shadow: 0 8px 32px rgba(11,30,61,0.08); max-width: 380px; width: 100%; padding: 2.5rem; }
.login-title { font-family: 'Playfair Display', serif; font-size: 1.6rem; color: var(--navy); text-align: center; margin-bottom: 0.4rem; }
.login-sub { color: var(--muted); font-size: 0.9rem; text-align: center; margin-bottom: 1.75rem; }
.field { margin-bottom: 1rem; }
.field label { display: block; font-size: 0.78rem; font-weight: 600; letter-spacing: 0.03em; text-transform: uppercase; color: var(--muted); margin-bottom: 0.5rem; }
.field input { width: 100%; font-family: 'DM Sans', sans-serif; font-size: 1rem; padding: 0.75rem 1rem; border: 1.5px solid var(--border); border-radius: var(--radius); background: var(--off); transition: border-color 0.2s; }
.field input:focus { outline: none; border-color: var(--gold); background: var(--white); }
.login-submit { width: 100%; background: var(--gold); color: var(--white); font-size: 0.9rem; font-weight: 600; padding: 0.85rem; border: none; border-radius: 30px; cursor: pointer; margin-top: 0.5rem; transition: background 0.2s; }
.login-submit:hover { background: var(--navy); }
.error-msg { background: #fbeae7; border-left: 3px solid #b3402c; color: #7a2c1d; font-size: 0.85rem; padding: 0.75rem 1rem; border-radius: 0 var(--radius) var(--radius) 0; margin-bottom: 1.25rem; }
</style>
</head>
<body>
  <div class="login-card">
    <h1 class="login-title">CBS Admin</h1>
    <p class="login-sub">Sign in to manage records</p>
    <?php if ($error !== ""): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="">
      <div class="field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" autocomplete="username" required>
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>
      </div>
      <button type="submit" class="login-submit">Sign In</button>
    </form>
  </div>
</body>
</html>
