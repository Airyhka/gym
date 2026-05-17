<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';

$currentPage = 'galeri';
$pageTitle   = 'Galeri';
$pdo = db();
$action = $_GET['action'] ?? '';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $image    = trim($_POST['image'] ?? '');
    $caption  = trim($_POST['caption'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $order    = (int)($_POST['display_order'] ?? 0);
    $active   = isset($_POST['is_active']) ? 1 : 0;

    if ($id) {
        $pdo->prepare('UPDATE galleries SET title=?,image=?,caption=?,category=?,display_order=?,is_active=? WHERE id=?')
            ->execute([$title,$image,$caption,$category,$order,$active,$id]);
    } else {
        $pdo->prepare('INSERT INTO galleries (title,image,caption,category,display_order,is_active,created_by) VALUES (?,?,?,?,?,?,?)')
            ->execute([$title,$image,$caption,$category,$order,$active,$_SESSION['admin_id']]);
    }
    flash('success', 'Galeri berhasil disimpan.');
    header('Location: galeri.php');
    exit;
}

if ($action === 'delete' && $id) {
    $pdo->prepare('DELETE FROM galleries WHERE id=?')->execute([$id]);
    flash('success', 'Foto dihapus.');
    header('Location: galeri.php');
    exit;
}

$edit = null;
if ($action === 'edit' && $id) {
    $s = $pdo->prepare('SELECT * FROM galleries WHERE id=?');
    $s->execute([$id]);
    $edit = $s->fetch();
}

$galleries = $pdo->query('SELECT * FROM galleries ORDER BY display_order, id')->fetchAll();
require_once '../../includes/header.php';
?>
<div class="page-actions">
  <a href="?action=add" class="btn-primary">+ Tambah Foto</a>
</div>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="form-card">
  <h3><?= $edit ? 'Edit Foto' : 'Tambah Foto' ?></h3>
  <form method="POST" action="?<?= $edit ? 'action=edit&id='.$edit['id'] : 'action=add' ?>">
    <div class="field-row cols-2">
      <div class="field">
        <label>Judul</label>
        <input type="text" name="title" value="<?= e($edit['title'] ?? '') ?>">
      </div>
      <div class="field">
        <label>Kategori</label>
        <input type="text" name="category" value="<?= e($edit['category'] ?? '') ?>" placeholder="Ruang Utama">
      </div>
    </div>
    <div class="field">
      <label>Path / URL Gambar</label>
      <input type="text" name="image" value="<?= e($edit['image'] ?? '') ?>" placeholder="img/gallery1.jpg" required>
    </div>
    <div class="field-row cols-2">
      <div class="field">
        <label>Caption</label>
        <input type="text" name="caption" value="<?= e($edit['caption'] ?? '') ?>">
      </div>
      <div class="field">
        <label>Urutan</label>
        <input type="number" name="display_order" value="<?= $edit['display_order'] ?? 0 ?>">
      </div>
    </div>
    <div class="field field-check">
      <label><input type="checkbox" name="is_active" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>> Tampil di website</label>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn-primary">Simpan</button>
      <a href="galeri.php" class="btn-ghost">Batal</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="gallery-grid-admin">
<?php foreach ($galleries as $g): ?>
  <div class="gallery-card-admin <?= $g['is_active'] ? '' : 'inactive' ?>">
    <div class="gallery-thumb">
      <img src="<?= SITE_PATH ?>/<?= e($g['image']) ?>" alt="<?= e($g['title']) ?>" onerror="this.src='/gym/img/placeholder.png'">
    </div>
    <div class="gallery-info">
      <strong><?= e($g['title'] ?: '(tanpa judul)') ?></strong>
      <span><?= e($g['category'] ?? '—') ?></span>
    </div>
    <div class="gallery-actions">
      <a href="?action=edit&id=<?= $g['id'] ?>" class="btn-edit">Edit</a>
      <a href="?action=delete&id=<?= $g['id'] ?>" class="btn-del" onclick="return confirm('Hapus foto ini?')">Hapus</a>
    </div>
  </div>
<?php endforeach; ?>
<?php if (!$galleries): ?>
  <p class="empty-note">Belum ada foto di galeri.</p>
<?php endif; ?>
</div>
<?php require_once '../../includes/footer.php'; ?>
