<?php
include '../koneksi/db.php';
session_start();

// Cek apakah pengguna berhasil login
$login_successful = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Ambil data statistik
$total_siswa = $conn->query("SELECT COUNT(*) FROM siswa")->fetch_row()[0];
$rata_nilai = $conn->query("SELECT AVG(nilai) FROM penilaian")->fetch_row()[0] ?? 0; // Rata-rata dari tabel penilaian
$total_mapel = $conn->query("SELECT COUNT(*) FROM mata_pelajaran")->fetch_row()[0];

// Query untuk menampilkan data siswa dan nilai
$query = "
SELECT siswa.nama AS nama_siswa, siswa.kelas, siswa.jurusan, mata_pelajaran.nama_mapel AS mata_pelajaran, penilaian.nilai 
FROM siswa 
LEFT JOIN penilaian ON siswa.id_siswa = penilaian.id_siswa 
LEFT JOIN mata_pelajaran ON penilaian.id_mapel = mata_pelajaran.id_mapel";

// Eksekusi query
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penilaian Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #3B82F6;
            --secondary: #60A5FA;
            --accent: #F59E0B;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: 100%;
            top: 0;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-collapsed {
            transform: translateX(-100%);
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: 8px;
            transition: background 0.3s, transform 0.2s;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        main {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }

        header {
            height: 60px;
        }

        @media (max-width: 768px) {
            main {
                margin-left: 0;
            }
        }

        .notification {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            z-index: 1000;
        }
    </style>
</head>

<body class="bg-gray-50">
    <aside class="sidebar">
        <div class="p-4">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-graduation-cap mr-2"></i>SISWA
            </h2>
            <nav class="space-y-2">
                <a href="../siswa/dashboard_siswa.php" class="nav-link text-white bg-blue-500/20">
                    <i class="fas fa-home mr-3"></i> Dashboard
                </a>
                <a href="http://localhost/penilaian_siswa_smk/login.php" class="nav-link text-white bg-blue-500/20" onclick="return confirmLogout();">
                    <i class="fas fa-sign-out-alt mr-3"></i> Logout
                </a>
            </nav>
        </div>
    </aside>

    <main id="mainContent">
        <header class="bg-white shadow-sm sticky top-0 z-40">
            <div class="flex items-center justify-between p-4 h-full">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-blue-500 text-xl">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </header>

        <div class="p-6">
            <div class="notification" id="notification">Login Berhasil!</div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="stat-card p-6 bg-white rounded-xl shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500">Total Siswa</p>
                            <p class="text-3xl font-bold"><?= number_format($total_siswa) ?></p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-users text-blue-500 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card p-6 bg-white rounded-xl shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500">Rata-rata Nilai</p>
                            <p class="text-3xl font-bold"><?= number_format($rata_nilai, 2) ?></p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-chart-line text-green-500 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card p-6 bg-white rounded-xl shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500">Mata Pelajaran</p>
                            <p class="text-3xl font-bold"><?= number_format($total_mapel) ?></p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full">
                            <i class="fas fa-book text-purple-500 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-4">Data Siswa dan Nilai</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="pb-3">Nama Siswa</th>
                                <th class="pb-3">Kelas</th>
                                <th class="pb-3">Jurusan</th>
                                <th class="pb-3">Mata Pelajaran</th>
                                <th class="pb-3">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2"><?= $row['nama_siswa'] ?></td>
                                    <td><?= $row['kelas'] ?></td>
                                    <td><?= $row['jurusan'] ?></td>
                                    <td><?= $row['mata_pelajaran'] ?></td>
                                    <td><?= $row['nilai'] ?? 'N/A' ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('#mainContent');
            sidebar.classList.toggle('sidebar-collapsed');
            mainContent.style.marginLeft = sidebar.classList.contains('sidebar-collapsed') ? '0' : '250px';
        }

        function showNotification() {
            const notification = document.getElementById('notification');
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
                window.location.href = 'dashboard_siswa.php'; // Arahkan ke dashboard setelah 1 detik
            }, 1000);
        }

        function confirmLogout() {
            if (confirm("Apakah Anda yakin ingin logout?")) {
                window.location.href = 'http://localhost/penilaian_siswa_smk/login.php'; // Arahkan ke halaman login
            }
            return false; // Mencegah tautan untuk diikuti jika tidak yakin
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                document.querySelector('.sidebar').classList.remove('sidebar-collapsed');
                document.querySelector('#mainContent').style.marginLeft = '250px';
            }
        });

        // Tampilkan notifikasi hanya jika login berhasil
        <?php if ($login_successful): ?>
            showNotification();
        <?php endif; ?>
    </script>
</body>

</html>