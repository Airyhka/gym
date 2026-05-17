<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';

$currentPage = 'member';
$pageTitle   = 'Data Member';
$pdo = db();
$action = $_GET['action'] ?? '';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $phone  = trim($_POST['phone'] ?? '');
    $pkgId  = (int)($_POST['package_id'] ?? 0);
    $pkgName = trim($_POST['package_name'] ?? '');
    $start  = $_POST['start_date'] ?: null;
    $end    = $_POST['end_date']   ?: null;
    $status = $_POST['status'] ?? 'active';
    $notes  = trim($_POST['notes'] ?? '');

    if (!$name) { flash('error', 'Nama tidak boleh kosong.'); header('Location: member.php?action=' . ($id ? "edit&id=$id" : 'add')); exit; }

    if ($id) {
        $pdo->prepare('UPDATE members SET name=?,email=?,phone=?,package_id=?,package_name=?,start_date=?,end_date=?,status=?,notes=? WHERE id=?')
            ->execute([$name,$email,$phone,$pkgId?:null,$pkgName,$start,$end,$status,$notes,$id]);
    } else {
        $pdo->prepare('INSERT INTO members (name,email,phone,package_id,package_name,start_date,end_date,status,notes) VALUES (?,?,?,?,?,?,?,?,?)')
            ->execute([$name,$email,$phone,$pkgId?:null,$pkgName,$start,$end,$status,$notes]);
    }
    flash('success', 'Data member berhasil disimpan.');
    header('Location: member.php');
    exit;
}

if ($action === 'delete' && $id) {
    $pdo->prepare('DELETE FROM members WHERE id=?')->execute([$id]);
    flash('success', 'Member dihapus.');
    header('Location: member.php');
    exit;
}

$edit = null;
if ($action === 'edit' && $id) {
    $s = $pdo->prepare('SELECT * FROM members WHERE id=?');
    $s->execute([$id]);
    $edit = $s->fetch();
}

// Filter + search
$q  = trim($_GET['q'] ?? '');
$sf = $_GET['status'] ?? '';
$where = 'WHERE 1=1';
$params = [];
if ($q) { $where .= ' AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)'; $params = array_merge($params, ["%$q%","%$q%","%$q%"]); }
if ($sf) { $where .= ' AND status = ?'; $params[] = $sf; }
$stmt = $pdo->prepare("SELECT * FROM members $where ORDER BY created_at DESC");
$stmt->execute($params);
$members = $stmt->fetchAll();

$packages = $pdo->query('SELECT id, name FROM packages WHERE is_active=1 ORDER BY display_order')->fetchAll();

require_once '../../includes/header.php';
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="form-card">
  <h3><?= $edit ? 'Edit Member' : 'Tambah Member' ?></h3>
  <form method="POST" action="?<?= $edit ? 'action=edit&id='.$edit['id'] : 'action=add' ?>">
    <div class="field-row cols-2">
      <div class="field">
        <label>Nama Lengkap</label>
        <input type="text" name="name" value="<?= e($edit['name'] ?? '') ?>" required>
      </div>
      <div class="field">
        <label>No. HP / WhatsApp</label>
        <input type="text" name="phone" value="<?= e($edit['phone'] ?? '') ?>">
      </div>
    </div>
    <div class="field-row cols-2">
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" value="<?= e($edit['email'] ?? '') ?>">
      </div>
      <div class="field">
        <label>Paket</label>
        <select name="package_id" onchange="document.getElementById('pkg-name').value=this.options[this.selectedIndex].text">
          <option value="">— Pilih Paket —</option>
          <?php foreach ($packages as $p): ?>
          <option value="<?= $p['id'] ?>" <?= ($edit['package_id'] ?? '') == $p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="hidden" name="package_name" id="pkg-name" value="<?= e($edit['package_name'] ?? '') ?>">
      </div>
    </div>
    <div class="field-row cols-3">
      <div class="field">
        <label>Tanggal Mulai</label>
        <input type="date" name="start_date" value="<?= e($edit['start_date'] ?? '') ?>">
      </div>
      <div class="field">
        <label>Tanggal Berakhir</label>
        <input type="date" name="end_date" value="<?= e($edit['end_date'] ?? '') ?>">
      </div>
      <div class="field">
        <label>Status</label>
        <select name="status">
          <?php foreach (['active'=>'Aktif','expired'=>'Kadaluarsa','suspended'=>'Ditangguhkan'] as $v => $l): ?>
          <option value="<?= $v ?>" <?= ($edit['status'] ?? 'active') === $v ? 'selected' : '' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="field">
      <label>Catatan</label>
      <input type="text" name="notes" value="<?= e($edit['notes'] ?? '') ?>">
    </div>
    <div class="form-actions">
      <button type="submit" class="btn-primary">Simpan</button>
      <a href="member.php" class="btn-ghost">Batal</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="toolbar">
  <form method="GET" class="search-form">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari nama, email, no. HP...">
    <select name="status">
      <option value="">Semua Status</option>
      <?php foreach (['active'=>'Aktif','expired'=>'Kadaluarsa','suspended'=>'Ditangguhkan'] as $v=>$l): ?>
      <option value="<?= $v ?>" <?= $sf===$v?'selected':'' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn-primary">Cari</button>
    <?php if ($q || $sf): ?><a href="member.php" class="btn-ghost">Reset</a><?php endif; ?>
  </form>
  <a href="?action=add" class="btn-primary">+ Tambah Member</a>
</div>

<table class="data-table">
  <thead>
    <tr><th>Nama</th><th>Email</th><th>No. HP</th><th>Paket</th><th>Mulai</th><th>Berakhir</th><th>Status</th><th>Aksi</th></tr>
  </thead>
  <tbody>
  <?php if ($members): ?>
  <?php foreach ($members as $m): $s = calcMemberStatus($m); ?>
    <tr>
      <td><?= e($m['name']) ?></td>
      <td><?= e($m['email'] ?? '—') ?></td>
      <td><?= e($m['phone'] ?? '—') ?></td>
      <td><?= e($m['package_name'] ?? '—') ?></td>
      <td><?= fmtDate($m['start_date']) ?></td>
      <td><?= fmtDate($m['end_date']) ?></td>
      <td><span class="badge badge-<?= $s ?>"><?= ['active'=>'Aktif','expired'=>'Kadaluarsa','suspended'=>'Tangguhkan'][$s] ?? $s ?></span></td>
      <td class="row-actions">
        <a href="?action=edit&id=<?= $m['id'] ?>" class="btn-edit">Edit</a>
        <a href="?action=delete&id=<?= $m['id'] ?>" class="btn-del" onclick="return confirm('Hapus member ini?')">Hapus</a>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php else: ?>
    <tr><td colspan="8" class="empty-note">Tidak ada data member<?= ($q||$sf) ? ' yang sesuai filter.' : '.' ?></td></tr>
  <?php endif; ?>
  </tbody>
</table>
<?php require_once '../../includes/footer.php'; ?>
