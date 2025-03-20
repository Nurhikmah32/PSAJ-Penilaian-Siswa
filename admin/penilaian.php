<?php
include '../koneksi/db.php'; // Include database connection

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $id_siswa = $_POST['id_siswa'];
        $id_mapel = $_POST['id_mapel'];
        $nilai = $_POST['nilai'];

        $sql = "INSERT INTO penilaian (id_siswa, id_mapel, nilai) VALUES ('$id_siswa', '$id_mapel', '$nilai')";
        $conn->query($sql);
    }

    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $id_siswa = $_POST['id_siswa'];
        $id_mapel = $_POST['id_mapel'];
        $nilai = $_POST['nilai'];
    
        // Menggunakan prepared statement untuk keamanan
        $stmt = $conn->prepare("UPDATE penilaian SET id_siswa=?, id_mapel=?, nilai=? WHERE id_penilaian=?");
        $stmt->bind_param("iiii", $id_siswa, $id_mapel, $nilai, $id);
        $stmt->execute();
        $stmt->close(); // Tutup statement
    
        echo "<script>window.location.href='penilaian.php';</script>";
        exit();
    }
    

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM penilaian WHERE id_penilaian='$id'";
        $conn->query($sql);

        echo "<script>window.location.href='penilaian.php';</script>";
        exit();

    }
}

// Pagination
$limit = 10; // Number of entries to show per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Update query to include id_siswa and id_mapel
$sql = "SELECT p.id_penilaian, s.id_siswa, s.nama, m.id_mapel, m.nama_mapel, p.nilai 
        FROM penilaian p 
        JOIN siswa s ON p.id_siswa = s.id_siswa 
        JOIN mata_pelajaran m ON p.id_mapel = m.id_mapel 
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Total entries for pagination
$total_sql = "SELECT COUNT(*) FROM penilaian";
$total_result = $conn->query($total_sql);
$total_entries = $total_result->fetch_row()[0];
$total_pages = ceil($total_entries / $limit);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penilaian</title>
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

        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Data Penilaian</h1>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Tambah
                Penilaian</button>

            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-4">Daftar Penilaian</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="pb-3">ID</th>
                                <th class="pb-3">Nama Siswa</th>
                                <th class="pb-3">Mata Pelajaran</th>
                                <th class="pb-3">Nilai</th>
                                <th class="pb-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2"><?= $row['id_penilaian'] ?></td>
                                    <td><?= $row['nama'] ?></td>
                                    <td><?= $row['nama_mapel'] ?></td>
                                    <td><?= $row['nilai'] ?></td>
                                    <td>
                                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal"
                                            data-id="<?= $row['id_penilaian'] ?>" data-id_siswa="<?= $row['id_siswa'] ?>"
                                            data-id_mapel="<?= $row['id_mapel'] ?>"
                                            data-nilai="<?= $row['nilai'] ?>">Edit</button>
                                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-id="<?= $row['id_penilaian'] ?>">Delete</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <nav class="mt-4">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>

            <!-- Add Modal -->
            <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Tambah Penilaian</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label for="id_siswa" class="form-label">Siswa</label>
                                    <select class="form-select" name="id_siswa" required>
                                        <?php
                                        $siswa_query = "SELECT * FROM siswa";
                                        $siswa_result = $conn->query($siswa_query);
                                        while ($siswa = $siswa_result->fetch_assoc()) {
                                            echo "<option value='{$siswa['id_siswa']}'>{$siswa['nama']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="id_mapel" class="form-label">Mata Pelajaran</label>
                                    <select class="form-select" name="id_mapel" required>
                                        <?php
                                        $mapel_query = "SELECT * FROM mata_pelajaran";
                                        $mapel_result = $conn->query($mapel_query);
                                        while ($mapel = $mapel_result->fetch_assoc()) {
                                            echo "<option value='{$mapel['id_mapel']}'>{$mapel['nama_mapel']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="nilai" class="form-label">Nilai</label>
                                    <input type="number" class="form-control" name="nilai" required>
                                </div>
                                <button type="submit" name="add" class="btn btn-primary">Tambah</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Penilaian</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <input type="hidden" name="id" id="edit-id">
                                <div class="mb-3">
                                    <label for="edit-id_siswa" class="form-label">Siswa</label>
                                    <select class="form-select" name="id_siswa" id="edit-id_siswa" required>
                                        <?php
                                        $siswa_result = $conn->query($siswa_query);
                                        while ($siswa = $siswa_result->fetch_assoc()) {
                                            echo "<option value='{$siswa['id_siswa']}'>{$siswa['nama']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit-id_mapel" class="form-label">Mata Pelajaran</label>
                                    <select class="form-select" name="id_mapel" id="edit-id_mapel" required>
                                        <?php
                                        $mapel_result = $conn->query($mapel_query);
                                        while ($mapel = $mapel_result->fetch_assoc()) {
                                            echo "<option value='{$mapel['id_mapel']}'>{$mapel['nama_mapel']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit-nilai" class="form-label">Nilai</label>
                                    <input type="number" class="form-control" name="nilai" id="edit-nilai" required>
                                </div>
                                <button type="submit" name="edit" class="btn btn-warning">Simpan Perubahan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Hapus Penilaian</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <input type="hidden" name="id" id="delete-id">
                                <p>Apakah Anda yakin ingin menghapus penilaian ini?</p>
                                <button type="submit" name="delete" class="btn btn-danger">Hapus</button>
                            </form>
                        </div>
                    </div>
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

        // Populate edit modal
        $('#editModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var id_siswa = button.data('id_siswa');
            var id_mapel = button.data('id_mapel');
            var nilai = button.data('nilai');

            var modal = $(this);
            modal.find('#edit-id').val(id);
            modal.find('#edit-id_siswa').val(id_siswa);
            modal.find('#edit-id_mapel').val(id_mapel);
            modal.find('#edit-nilai').val(nilai);
        });

        // Populate delete modal
        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');

            var modal = $(this);
            modal.find('#delete-id').val(id);
        });
    </script>
</body>

</html>

<?php
$conn->close(); // Close the connection at the end of the script
?>