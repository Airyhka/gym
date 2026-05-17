<?php
session_start();
session_destroy();
header('Location: /gym/admin/login.php');
exit;
