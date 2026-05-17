<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: ' . ADMIN_PATH . '/pages/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = db()->prepare('SELECT id, username, password_hash, name FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            header('Location: ' . ADMIN_PATH . '/pages/dashboard.php');
            exit;
        }
    }
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login — Warriors Gym Admin</title>
<link rel="stylesheet" href="<?= ADMIN_PATH ?>/admin.css">
</head>
<body class="login-body">
<div class="login-wrap">
  <div class="login-brand">WARRIORS<span>GYM</span></div>
  <p class="login-sub">Admin Panel</p>
  <?php if ($error): ?>
  <div class="flash flash-error"><?= e($error) ?></div>
  <?php endif; ?>
  <form method="POST" class="login-form">
    <div class="field">
      <label>Username</label>
      <input type="text" name="username" autocomplete="username" autofocus required>
    </div>
    <div class="field">
      <label>Password</label>
      <input type="password" name="password" autocomplete="current-password" required>
    </div>
    <button type="submit" class="btn-login">Masuk</button>
  </form>
</div>
</body>
</html>
