<?php
function sidebarIcon(string $n): string {
    $i = [
        'dashboard' => '<svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
        'profil'    => '<svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
        'paket'     => '<svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
        'galeri'    => '<svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>',
        'member'    => '<svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'pesan'     => '<svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
        'akun'      => '<svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
    ];
    return $i[$n] ?? '';
}
$menu = [
    'dashboard' => 'Dashboard',
    'profil'    => 'Profil Gym',
    'paket'     => 'Paket Member',
    'galeri'    => 'Galeri',
    'member'    => 'Data Member',
    'pesan'     => 'Pesan Masuk',
    'akun'      => 'Akun',
];
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? ucfirst($currentPage)) ?> — Warriors Gym Admin</title>
<link rel="stylesheet" href="<?= ADMIN_PATH ?>/admin.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-brand">
  <img src="/gym/img/logo.jpg" alt="Warriors Gym" class="sidebar-logo-img">
  <span>WARRIORS<span class="brand-accent">GYM</span></span>
</div>
    <nav class="sidebar-nav">
      <?php foreach ($menu as $key => $label): ?>
      <a href="<?= ADMIN_PATH ?>/pages/<?= $key ?>.php"
         class="nav-item <?= ($currentPage ?? '') === $key ? 'active' : '' ?>">
        <?= sidebarIcon($key) ?>
        <span><?= $label ?></span>
      </a>
      <?php endforeach; ?>
    </nav>
    <a href="<?= ADMIN_PATH ?>/logout.php" class="sidebar-logout">Logout</a>
  </aside>
  <div class="page-wrap">
    <header class="topbar">
      <h1 class="page-title"><?= e($pageTitle ?? ucfirst($currentPage)) ?></h1>
      <div class="topbar-right">
        <a href="<?= SITE_PATH ?>/" target="_blank" class="btn-site">Lihat Website</a>
        <span class="admin-name"><?= e($_SESSION['admin_name'] ?? 'Admin') ?></span>
      </div>
    </header>
    <?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div>
    <?php endif; ?>
    <main class="main-content">
