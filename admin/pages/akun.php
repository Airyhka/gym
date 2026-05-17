<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';

$currentPage = 'akun';
$pageTitle   = 'Akun';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPass  = $_POST['old_password'] ?? '';
    $newPass  = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $newName  = trim($_POST['name'] ?? '');

    $admin = $pdo->prepare('SELECT password_hash FROM admins WHERE id=?');
    $admin->execute([$_SESSION['admin_id']]);
    $admin = $admin->fetch();

    if ($newPass) {
        if (!password_verify($oldPass, $admin['password_hash'])) {
            flash('error', 'Password lama tidak sesuai.');
            header('Location: akun.php'); exit;
        }
        if (strlen($newPass) < 6) {
            flash('error', 'Password baru minimal 6 karakter.');
            header('Location: akun.php'); exit;
        }
        if ($newPass !== $confirm) {
            flash('error', 'Konfirmasi password tidak cocok.');
            header('Location: akun.php'); exit;
        }
        $hash = password_hash($newPass, PASSWORD_BCRYPT);
        $pdo->prepare('UPDATE admins SET password_hash=? WHERE id=?')->execute([$hash, $_SESSION['admin_id']]);
    }

    if ($newName) {
        $pdo->prepare('UPDATE admins SET name=? WHERE id=?')->execute([$newName, $_SESSION['admin_id']]);
        $_SESSION['admin_name'] = $newName;
    }

    flash('success', 'Akun berhasil diperbarui.');
    header('Location: akun.php');
    exit;
}

$admin = $pdo->prepare('SELECT username, name FROM admins WHERE id=?');
$admin->execute([$_SESSION['admin_id']]);
$admin = $admin->fetch();

require_once '../../includes/header.php';
?>
<div class="form-card" style="max-width:520px">
  <h3>Informasi Akun</h3>
  <form method="POST">
    <div class="field">
      <label>Username</label>
      <input type="text" value="<?= e($admin['username']) ?>" disabled>
      <small>Username tidak dapat diubah.</small>
    </div>
    <div class="field">
      <label>Nama Tampil</label>
      <input type="text" name="name" value="<?= e($admin['name']) ?>">
    </div>

    <h3 style="margin-top:1.5rem">Ganti Password</h3>
    <div class="field">
      <label>Password Lama</label>
      <input type="password" name="old_password" autocomplete="current-password">
    </div>
    <div class="field">
      <label>Password Baru <small>(minimal 6 karakter)</small></label>
      <input type="password" name="new_password" autocomplete="new-password">
    </div>
    <div class="field">
      <label>Konfirmasi Password Baru</label>
      <input type="password" name="confirm_password" autocomplete="new-password">
    </div>
    <div class="form-actions">
      <button type="submit" class="btn-primary">Simpan Perubahan</button>
    </div>
  </form>
</div>
<?php require_once '../../includes/footer.php'; ?>
