<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';

$currentPage = 'dashboard';
$pageTitle   = 'Dashboard';

$pdo = db();
$stats = [
    'paket'  => $pdo->query('SELECT COUNT(*) FROM packages  WHERE is_active=1')->fetchColumn(),
    'galeri' => $pdo->query('SELECT COUNT(*) FROM galleries WHERE is_active=1')->fetchColumn(),
    'member' => $pdo->query('SELECT COUNT(*) FROM members')->fetchColumn(),
    'aktif'  => $pdo->query("SELECT COUNT(*) FROM members WHERE status='active' AND (end_date IS NULL OR end_date >= CURDATE())")->fetchColumn(),
    'pesan'  => $pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read=0')->fetchColumn(),
];
$recentMembers = $pdo->query('SELECT name, package_name, status, created_at FROM members ORDER BY created_at DESC LIMIT 5')->fetchAll();
$recentPesan   = $pdo->query('SELECT name, message, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 3')->fetchAll();

require_once '../../includes/header.php';
?>
<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-num"><?= $stats['paket'] ?></div>
    <div class="stat-label">Paket Aktif</div>
  </div>
  <div class="stat-card">
    <div class="stat-num"><?= $stats['galeri'] ?></div>
    <div class="stat-label">Foto Galeri</div>
  </div>
  <div class="stat-card">
    <div class="stat-num"><?= $stats['member'] ?></div>
    <div class="stat-label">Total Member</div>
  </div>
  <div class="stat-card accent">
    <div class="stat-num"><?= $stats['aktif'] ?></div>
    <div class="stat-label">Member Aktif</div>
  </div>
  <div class="stat-card">
    <div class="stat-num"><?= $stats['pesan'] ?></div>
    <div class="stat-label">Pesan Belum Dibaca</div>
  </div>
</div>

<div class="dash-grid">
  <div class="dash-block">
    <div class="block-header">
      <h3>Member Terbaru</h3>
      <a href="member.php" class="block-link">Lihat semua</a>
    </div>
    <?php if ($recentMembers): ?>
    <table class="data-table">
      <thead><tr><th>Nama</th><th>Paket</th><th>Status</th><th>Daftar</th></tr></thead>
      <tbody>
      <?php foreach ($recentMembers as $m): $s = calcMemberStatus($m); ?>
        <tr>
          <td><?= e($m['name']) ?></td>
          <td><?= e($m['package_name'] ?? '—') ?></td>
          <td><span class="badge badge-<?= $s ?>"><?= ['active'=>'Aktif','expired'=>'Kadaluarsa','suspended'=>'Tangguhkan'][$s] ?? $s ?></span></td>
          <td><?= fmtDate($m['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <p class="empty-note">Belum ada member.</p>
    <?php endif; ?>
  </div>

  <div class="dash-block">
    <div class="block-header">
      <h3>Pesan Masuk</h3>
      <a href="pesan.php" class="block-link">Lihat semua</a>
    </div>
    <?php if ($recentPesan): ?>
    <?php foreach ($recentPesan as $p): ?>
    <div class="pesan-preview">
      <strong><?= e($p['name']) ?></strong>
      <span class="pesan-date"><?= fmtDate($p['created_at']) ?></span>
      <p><?= e(mb_substr($p['message'], 0, 100)) ?>…</p>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
    <p class="empty-note">Belum ada pesan masuk.</p>
    <?php endif; ?>
  </div>
</div>
<?php require_once '../../includes/footer.php'; ?>
