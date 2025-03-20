_<?php
    $host = 'localhost'; // Ganti dengan host Anda
    $user = 'root'; // Ganti dengan username Anda
    $password = ''; // Ganti dengan password Anda
    $dbname = 'penilaian_siswa_smk'; // Nama database

    // Membuat koneksi
    $conn = new mysqli($host, $user, $password, $dbname);

    // Cek koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    ?>