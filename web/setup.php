<?php
/**
 * First-run setup — creates the initial admin account.
 * Delete or block this file after first use.
 */
require_once 'includes/config.php';
require_once 'includes/auth.php';

$existing = load_json(USERS_FILE);
if (!empty($existing)) {
    header('Location: login.php');
    exit;
}

$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $password2) {
        $error = 'Passwords do not match.';
    } else {
        $admin = [
            'id'            => generate_id(),
            'username'      => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role'          => 'admin',
            'created_at'    => date('c'),
        ];
        if (save_json(USERS_FILE, [$admin])) {
            $success = true;
        } else {
            $error = 'Could not write users file. Check permissions on data/.';
        }
    }
}

$cfg = get_config();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($cfg['app_name']) ?> — Setup</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-page">
  <div class="login-box">
    <div class="login-logo">&#9889; First-run Setup</div>
    <div class="login-sub">Create your admin account</div>

    <?php if ($success): ?>
      <div class="alert alert-success">
        Admin account created! <a href="login.php" style="color:inherit;font-weight:600">Sign in</a>
      </div>
      <p style="font-size:.8rem;color:var(--muted);margin-top:.5rem">
        For security, delete or deny access to <code>setup.php</code> now.
      </p>
    <?php else: ?>
      <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username"
                 value="<?= e($_POST['username'] ?? 'admin') ?>"
                 autocomplete="username" autofocus required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password"
                 autocomplete="new-password" required>
          <div class="hint">Minimum 8 characters</div>
        </div>
        <div class="form-group">
          <label for="password2">Confirm password</label>
          <input type="password" id="password2" name="password2"
                 autocomplete="new-password" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
          Create admin account
        </button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
