<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'gym_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('APP_NAME', 'Warriors Gym');
define('ADMIN_PATH', '/gym/admin');
define('SITE_PATH', '/gym');

function db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;
    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    } catch (PDOException $e) {
        http_response_code(500);
        die('<div style="font-family:sans-serif;padding:2rem"><h2>Database Error</h2><p>' . htmlspecialchars($e->getMessage()) . '</p><p>Pastikan MySQL berjalan dan database <strong>' . DB_NAME . '</strong> sudah dibuat.</p></div>');
    }
    return $pdo;
}

function flash(string $type, string $msg): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = compact('type', 'msg');
}

function getFlash(): ?array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function fmtPrice(float $price): string {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

function fmtDate(?string $date): string {
    if (!$date || $date === '0000-00-00') return '—';
    return date('d M Y', strtotime($date));
}

function calcMemberStatus(array $m): string {
    if ($m['status'] === 'suspended') return 'suspended';
    if (empty($m['end_date']) || $m['end_date'] === '0000-00-00') return $m['status'] ?: 'active';
    return strtotime($m['end_date']) < time() ? 'expired' : 'active';
}
