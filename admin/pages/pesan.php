<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';

$currentPage = 'pesan';
$pageTitle   = 'Pesan Masuk';
$pdo = db();
$action = $_GET['action'] ?? '';
$id     = (int)($_GET['id'] ?? 0);

if ($action === 'read' && $id) {
    $pdo->prepare('UPDATE contact_messages SET is_read=1 WHERE id=?')->execute([$id]);
    header('Location: pesan.php');
    exit;
}
if ($action === 'delete' && $id) {
    $pdo->prepare('DELETE FROM contact_messages WHERE id=?')->execute([$id]);
    flash('success', 'Pesan dihapus.');
    header('Location: pesan.php');
    exit;
}
if ($action === 'readall') {
    $pdo->exec('UPDATE contact_messages SET is_read=1');
    flash('success', 'Semua pesan ditandai sudah dibaca.');
    header('Location: pesan.php');
    exit;
}

$filter = $_GET['filter'] ?? '';
$where  = $filter === 'unread' ? 'WHERE is_read=0' : '';
$messages = $pdo->query("SELECT * FROM contact_messages $where ORDER BY created_at DESC")->fetchAll();
$unreadCount = $pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read=0')->fetchColumn();

require_once '../../includes/header.php';
?>
<div class="toolbar">
  <div class="filter-tabs">
    <a href="pesan.php" class="filter-tab <?= !$filter ? 'active' : '' ?>">Semua</a>
    <a href="?filter=unread" class="filter-tab <?= $filter==='unread' ? 'active' : '' ?>">
      Belum Dibaca <?php if ($unreadCount): ?><span class="badge-count"><?= $unreadCount ?></span><?php endif; ?>
    </a>
  </div>
  <?php if ($unreadCount): ?>
  <a href="?action=readall" class="btn-ghost" onclick="return confirm('Tandai semua pesan sudah dibaca?')">Tandai Semua Dibaca</a>
  <?php endif; ?>
</div>

<?php if ($messages): ?>
<div class="message-list">
<?php foreach ($messages as $m): ?>
<div class="message-item <?= $m['is_read'] ? '' : 'unread' ?>">
  <div class="message-header">
    <div class="message-from">
      <strong><?= e($m['name']) ?></strong>
      <span><?= e($m['email']) ?></span>
      <?php if ($m['phone']): ?><span><?= e($m['phone']) ?></span><?php endif; ?>
    </div>
    <div class="message-meta">
      <span class="message-date"><?= date('d M Y, H:i', strtotime($m['created_at'])) ?></span>
      <?php if (!$m['is_read']): ?>
      <a href="?action=read&id=<?= $m['id'] ?>" class="btn-edit">Tandai Dibaca</a>
      <?php endif; ?>
      <a href="?action=delete&id=<?= $m['id'] ?>" class="btn-del" onclick="return confirm('Hapus pesan ini?')">Hapus</a>
    </div>
  </div>
  <p class="message-body"><?= nl2br(e($m['message'])) ?></p>
</div>
<?php endforeach; ?>
</div>
<?php else: ?>
<p class="empty-note">Tidak ada pesan masuk.</p>
<?php endif; ?>
<?php require_once '../../includes/footer.php'; ?>
