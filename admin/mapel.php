<?php
include '../koneksi/db.php'; // Include database connection

// Ambil data mata pelajaran
$sql = "SELECT * FROM mata_pelajaran";
$result = $conn->query($sql);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $nama_mapel = $_POST['nama_mapel'];
    
        $stmt = $conn->prepare("INSERT INTO mata_pelajaran (nama_mapel) VALUES (?)");
        $stmt->bind_param("s", $nama_mapel);      
        $stmt->execute();
        $stmt->close();
        echo "<script>window.location.href='mapel.php';</script>";
        exit();
    }
    
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $nama_mapel = $_POST['nama_mapel'];
    
        $stmt = $conn->prepare("UPDATE mata_pelajaran SET nama_mapel=?, WHERE id_mapel=?");
        $stmt->bind_param("ssi", $nama_mapel, $id);
        $stmt->execute();
    
        echo "<script>window.location.href='mapel.php';</script>";
        exit();
    }
    
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM mata_pelajaran WHERE id_mapel=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        echo "<script>window.location.href='mapel.php';</script>";
        exit();
    }
    
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mata Pelajaran</title>
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
            </nav>
        </div>
    </aside>

    <main id="mainContent">
        <header class="bg-white shadow-sm sticky top-0 z-40">
            <div class="flex items-center justify-between p-4">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-blue-500 text-xl">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </header>

        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Data Mata Pelajaran</h1>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Mata Pelajaran</button>

            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-4">Daftar Mata Pelajaran</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="pb-3">ID</th>
                            <th class="pb-3">Nama Mata Pelajaran</th>
                            <th class="pb-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-2"><?= $row['id_mapel'] ?></td>
                                <td><?= $row['nama_mapel'] ?></td>
                                <td>
                                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal"
                                        data-id="<?= $row['id_mapel'] ?>" data-nama_mapel="<?= $row['nama_mapel'] ?>">
                                        Edit
                                    </button>
                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-id="<?= $row['id_mapel'] ?>">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Modal -->
            <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Tambah Mata Pelajaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <select class="form-select" name="nama_mapel" id="edit-nama_mapel" required>
                                        <option value="">Pilih Mata Pelajaran</option>
                                        <option value="Produktif">Produktif</option>
                                        <option value="Pendidikan Agama Budi Pekerti">Pendidikan Agama Budi Pekerti</option>
                                        <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                                        <option value="Matematika">Matematika</option>
                                        <option value="Pendidikan Pancasila">Pendidikan Pancasila</option>
                                        <option value="Bahasa Inggris">Bahasa Inggris</option>
                                        <option value="Bahasa Jawa">Bahasa Jawa</option>
                                        <option value="IPAS">IPAS</option>
                                        <option value="Sejarah">Sejarah</option>
                                        <option value="Informatika">Informatika</option>
                                        <option value="Seni Rupa">Seni Rupa</option>
                                        <option value="Pendidikan Jasmani Olahraga">Pendidikan Jasmani Olahraga</option>
                                        <option value="Projek Kreatif Kewirausahaan">Projek Kreatif Kewirausahaan</option>
                                        <option value="Bahasa Jepang">Bahasa Jepang</option>
                                    </select>
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
                            <h5 class="modal-title">Edit Mata Pelajaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <input type="hidden" name="id" id="edit-id">
                                <div class="mb-3">
                                    <select class="form-select" name="nama_mapel" id="edit-nama_mapel" required>
                                        <option value="">Pilih Mata Pelajaran</option>
                                        <option value="Produktif">Produktif</option>
                                        <option value="Pendidikan Agama Budi Pekerti">Pendidikan Agama Budi Pekerti</option>
                                        <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                                        <option value="Matematika">Matematika</option>
                                        <option value="Pendidikan Pancasila">Pendidikan Pancasila</option>
                                        <option value="Bahasa Inggris">Bahasa Inggris</option>
                                        <option value="Bahasa Jawa">Bahasa Jawa</option>
                                        <option value="IPAS">IPAS</option>
                                        <option value="Sejarah">Sejarah</option>
                                        <option value="Informatika">Informatika</option>
                                        <option value="Seni Rupa">Seni Rupa</option>
                                        <option value="Pendidikan Jasmani Olahraga">Pendidikan Jasmani Olahraga</option>
                                        <option value="Projek Kreatif Kewirausahaan">Projek Kreatif Kewirausahaan</option>
                                        <option value="Bahasa Jepang">Bahasa Jepang</option>
                                    </select>
                                </div>
                                    </select>
                                </div>
                                <button type="submit" name="edit" class="btn btn-warning">Simpan Perubahan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Hapus Mata Pelajaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <input type="hidden" name="id" id="delete-id">
                                <p>Apakah Anda yakin ingin menghapus mata pelajaran ini?</p>
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
            var nama_mapel = button.data('nama_mapel');
            

            var modal = $(this);
            modal.find('#edit-id').val(id);
            modal.find('#edit-nama_mapel').val(nama_mapel);
           
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