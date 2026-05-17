<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('ADMIN_PATH')) require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . ADMIN_PATH . '/login.php');
    exit;
}
