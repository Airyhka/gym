<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';

$currentPage = 'paket';
$pageTitle   = 'Paket Member';
$pdo = db();
$action = $_GET['action'] ?? '';
$id     = (int)($_GET['id'] ?? 0);

// Handle POST (save)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $duration = trim($_POST['duration'] ?? '');
    $price    = (float)($_POST['price'] ?? 0);
    $desc     = trim($_POST['description'] ?? '');
    $benefits = trim($_POST['benefits'] ?? '');
    $order    = (int)($_POST['display_order'] ?? 0);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $active   = isset($_POST['is_active']) ? 1 : 0;
    $bJson    = json_encode(array_values(array_filter(array_map('trim', explode("\n", $benefits)))));

    if ($id) {
        $pdo->prepare('UPDATE packages SET name=?,duration=?,price=?,description=?,benefits=?,display_order=?,is_active=? WHERE id=?')
            ->execute([$name,$duration,$price,$desc,$bJson,$order,$active,$id]);
    } else {
        $pdo->prepare('INSERT INTO packages (name,duration,price,description,benefits,display_order,is_active,created_by) VALUES (?,?,?,?,?,?,?,?)')
            ->execute([$name,$duration,$price,$desc,$bJson,$order,$active,$_SESSION['admin_id']]);
    }
    flash('success', 'Paket berhasil disimpan.');
    header('Location: paket.php');
    exit;
}

// Handle delete
if ($action === 'delete' && $id) {
    $pdo->prepare('DELETE FROM packages WHERE id=?')->execute([$id]);
    flash('success', 'Paket dihapus.');
    header('Location: paket.php');
    exit;
}

// Load edit data
$edit = null;
if (($action === 'edit') && $id) {
    $edit = $pdo->prepare('SELECT * FROM packages WHERE id=?');
    $edit->execute([$id]);
    $edit = $edit->fetch();
}

$packages = $pdo->query('SELECT * FROM packages ORDER BY display_order, id')->fetchAll();

require_once '../../includes/header.php';
?>
<div class="page-actions">
  <a href="?action=add" class="btn-primary">+ Tambah Paket</a>
</div>

<?php if ($action === 'add' || $action === 'edit'): ?>
<?php $b = $edit ? implode("\n", json_decode($edit['benefits'] ?? '[]', true) ?: []) : ''; ?>
<div class="form-card">
  <h3><?= $edit ? 'Edit Paket' : 'Paket Baru' ?></h3>
  <form method="POST" action="?<?= $edit ? 'action=edit&id='.$edit['id'] : 'action=add' ?>">
    <div class="field-row cols-3">
      <div class="field">
        <label>Nama Paket</label>
        <input type="text" name="name" value="<?= e($edit['name'] ?? '') ?>" required>
      </div>
      <div class="field">
        <label>Durasi</label>
        <input type="text" name="duration" value="<?= e($edit['duration'] ?? '') ?>" placeholder="1 Bulan" required>
      </div>
      <div class="field">
        <label>Harga (Rp)</label>
        <input type="number" name="price" value="<?= $edit['price'] ?? 0 ?>" min="0" required>
      </div>
    </div>
    <div class="field">
      <label>Deskripsi</label>
      <input type="text" name="description" value="<?= e($edit['description'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Keuntungan <small>(satu per baris)</small></label>
      <textarea name="benefits" rows="4"><?= e($b) ?></textarea>
    </div>
    <div class="field-row cols-3">
      <div class="field">
        <label>Urutan Tampil</label>
        <input type="number" name="display_order" value="<?= $edit['display_order'] ?? 0 ?>">
      </div>
      <div class="field field-check">
        <label><input type="checkbox" name="is_active" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>> Aktif</label>
      </div>
      <div class="field field-check">
        <label><input type="checkbox" name="featured" <?= !empty($edit['featured']) ? 'checked' : '' ?>> Paling Populer</label>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn-primary">Simpan</button>
      <a href="paket.php" class="btn-ghost">Batal</a>
    </div>
  </form>
</div>
<?php endif; ?>

<table class="data-table">
  <thead>
    <tr><th>#</th><th>Nama</th><th>Durasi</th><th>Harga</th><th>Status</th><th>Aksi</th></tr>
  </thead>
  <tbody>
  <?php if ($packages): ?>
  <?php foreach ($packages as $p): ?>
    <tr>
      <td><?= (int)$p['display_order'] ?></td>
      <td><?= e($p['name']) ?></td>
      <td><?= e($p['duration']) ?></td>
      <td><?= fmtPrice((float)$p['price']) ?></td>
      <td><span class="badge badge-<?= $p['is_active'] ? 'active' : 'expired' ?>"><?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
      <td class="row-actions">
        <a href="?action=edit&id=<?= $p['id'] ?>" class="btn-edit">Edit</a>
        <a href="?action=delete&id=<?= $p['id'] ?>" class="btn-del" onclick="return confirm('Hapus paket ini?')">Hapus</a>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php else: ?>
    <tr><td colspan="6" class="empty-note">Belum ada paket.</td></tr>
  <?php endif; ?>
  </tbody>
</table>
<?php require_once '../../includes/footer.php'; ?>
