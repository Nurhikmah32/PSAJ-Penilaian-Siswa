<?php
session_start();

if (!isset($_SESSION['auth']) || !$_SESSION['auth']['logged_in']) {
    header('Location: login.php');
    exit;
}

// Pastikan hanya siswa yang dapat mengakses halaman
if ($_SESSION['auth']['role'] !== 'siswa') {
    header('Location: login.php');
    exit;
}

// Ambil data siswa dari sesi
$siswa_id = $_SESSION['auth']['id'];
$siswa_nis = $_SESSION['auth']['nis'];
$siswa_username = $_SESSION['auth']['username'];
