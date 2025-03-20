<?php
include '../koneksi/db.php'; // Include database connection

// Fetching data for reports
$sql = "SELECT s.nama, mp.nama_mapel, p.nilai 
        FROM penilaian p 
        JOIN siswa s ON p.id_siswa = s.id_siswa 
        JOIN mata_pelajaran mp ON p.id_mapel = mp.id_mapel";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penilaian</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
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
            /* Changed from 100vh to 100% */
            top: 0;
            /* Added to ensure it starts from the top */
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
    </style>
</head>

<body class="bg-gray-50">
    <aside class="sidebar">
        <div class="p-4">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-graduation-cap mr-2"></i>SMK DIGITAL
            </h2>
            <nav class="space-y-2">
                <a href="dashboard.php" class="nav-link text-white bg-blue-500/20">
                    <i class="fas fa-home mr-3"></i> Dashboard
                </a>
                <a href="data_siswa.php" class="nav-link text-white bg-blue-500/20">
                    <i class="fas fa-user-graduate mr-3"></i> Siswa
                </a>
                <a href="mapel.php" class="nav-link text-white bg-blue-500/20">
                    <i class="fas fa-book-open mr-3"></i> Mata Pelajaran
                </a>
                <a href="penilaian.php" class="nav-link text-white bg-blue-500/20">
                    <i class="fas fa-file-signature mr-3"></i> Penilaian
                </a>
                <a href="laporan.php" class="nav-link text-white bg-blue-500/20">
                    <i class="fas fa-file-alt mr-3"></i> Laporan
                </a>
                <a href="http://localhost/penilaian_siswa_smk/login.php" class="nav-link text-white bg-blue-500/20">
                    <i class="fas fa-sign-out-alt mr-3"></i> Logout
                </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main id="mainContent">
        <header class="bg-white shadow-sm sticky top-0 z-40">
            <div class="flex items-center justify-between p-4">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-blue-500 text-xl">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="flex items-center space-x-4">
                    <div class="relative">

                    </div>
                </div>
        </header>

        <div class="content p-6">
            <h1 class="text-2xl font-bold mb-4">Laporan Penilaian</h1>

            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-4">Daftar Penilaian</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="pb-3">Nama Siswa</th>
                                <th class="pb-3">Mata Pelajaran</th>
                                <th class="pb-3">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2"><?= $row['nama'] ?></td>
                                        <td><?= $row['nama_mapel'] ?></td>
                                        <td><?= $row['nilai'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            function toggleSidebar() {
                const sidebar = document.querySelector('.sidebar');
                const mainContent = document.querySelector('#mainContent');
                sidebar.classList.toggle('sidebar-collapsed');
                mainContent.style.marginLeft = sidebar.classList.contains('sidebar-collapsed') ? '0' : '250px';
            }
        </script>
    </main>
</body>

</html>

<?php
$conn->close(); // Close the connection at the end of the script
?>