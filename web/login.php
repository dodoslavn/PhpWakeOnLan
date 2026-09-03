<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

session_init();
if (is_logged_in()) { header('Location: index.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } elseif (!attempt_login($username, $password)) {
        $error = 'Invalid username or password.';
    } else {
        header('Location: index.php');
        exit;
    }
}

$cfg     = get_config();
$users   = load_json(USERS_FILE);
$no_user = empty($users);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($cfg['app_name']) ?> — Login</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-page">
  <div class="login-box">
    <div class="login-logo">&#9889; <?= e($cfg['app_name']) ?></div>
    <div class="login-sub">Sign in to continue</div>

    <?php if ($no_user): ?>
      <div class="alert alert-info">
        No users found. <a href="setup.php" style="color:inherit;font-weight:600">Run setup</a> to create the first admin account.
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username"
               value="<?= e($_POST['username'] ?? '') ?>"
               autocomplete="username" autofocus required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password"
               autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
        Sign in
      </button>
    </form>
  </div>
</div>
</body>
</html>
