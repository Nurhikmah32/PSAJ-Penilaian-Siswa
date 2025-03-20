<?php
session_start();

// Hapus semua sesi
$_SESSION = [];
session_destroy();

// Redirect ke login dengan alert
header("Location: login.php?message=logout_success");
exit;
