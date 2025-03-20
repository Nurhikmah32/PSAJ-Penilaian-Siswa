<?php
include '../koneksi/db.php'; // Include database connection

// Ambil data statistik
$total_siswa = $conn->query("SELECT COUNT(*) FROM siswa")->fetch_row()[0];
$rata_nilai = $conn->query("SELECT AVG(nilai) FROM penilaian")->fetch_row()[0] ?? 0;
$total_mapel = $conn->query("SELECT COUNT(*) FROM mata_pelajaran")->fetch_row()[0];

// Ambil data jurusan
$jurusan_result = $conn->query("SELECT * FROM jurusan");

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $nama = $_POST['nama'];
        $kelas = $_POST['kelas'];
        $jurusan = $_POST['jurusan'];
        $tanggal_lahir = $_POST['tanggal_lahir'];

        // Validasi input
        $sql = "INSERT INTO siswa (nama, kelas, jurusan, tanggal_lahir) VALUES ('$nama', '$kelas', '$jurusan', '$tanggal_lahir')";
        $conn->query($sql);
    }

    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $kelas = $_POST['kelas'];
        $jurusan = $_POST['jurusan'];
        $tanggal_lahir = $_POST['tanggal_lahir'];

        // Validasi input
        $sql = "UPDATE siswa SET nama='$nama', kelas='$kelas', jurusan='$jurusan', tanggal_lahir='$tanggal_lahir' WHERE id_siswa='$id'";
        $conn->query($sql);
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM siswa WHERE id_siswa='$id'";
        $conn->query($sql);
    }
}

// Pagination
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM siswa LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Total entries for pagination
$total_sql = "SELECT COUNT(*) FROM siswa";
$total_result = $conn->query($total_sql);
$total_entries = $total_result->fetch_row()[0];
$total_pages = ceil($total_entries / $limit);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
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
            <h1 class="text-2xl font-bold mb-4">Data Siswa</h1>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Siswa</button>

            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-4">Daftar Siswa</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="pb-3">ID</th>
                                <th class="pb-3">Nama</th>
                                <th class="pb-3">Kelas</th>
                                <th class="pb-3">Jurusan</th>
                                <th class="pb-3">Tanggal Lahir</th>
                                <th class="pb-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2"><?= $row['id_siswa'] ?></td>
                                    <td><?= $row['nama'] ?></td>
                                    <td><?= $row['kelas'] ?></td>
                                    <td><?= $row['jurusan'] ?></td>
                                    <td><?= $row['tanggal_lahir'] ?></td>
                                    <td>
                                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal"
                                            data-id="<?= $row['id_siswa'] ?>" data-nama="<?= $row['nama'] ?>"
                                            data-kelas="<?= $row['kelas'] ?>" data-jurusan="<?= $row['jurusan'] ?>"
                                            data-tanggal_lahir="<?= $row['tanggal_lahir'] ?>">Edit</button>
                                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-id="<?= $row['id_siswa'] ?>">Delete</button>
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
                            <h5 class="modal-title" id="addModalLabel">Tambah Siswa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input type="text" class="form-control" name="nama" required>
                                </div>
                                <div class="mb-3">
                                    <label for="kelas" class="form-label">Kelas</label>
                                    <select class="form-control" name="kelas" required>
                                    <option value="pilih kelas">pilih kelas</option>
                                        <option value="X PPLG 1">X PPLG 1</option>
                                        <option value="X PPLG 2">X PPLG 2</option>
                                        <option value="X TM 1">X TM 1</option>
                                        <option value="X TM 2">X TM 2</option>
                                        <option value="X TE 1">X TE 1</option>
                                        <option value="X TE 2">X TE 2</option>
                                        <option value="X TE 3">X TE 3</option>
                                        <option value="X TE 4">X TE 4</option>
                                        <option value="X TO 1">X TO 1</option>
                                        <option value="X TO 2">X TO 2</option>
                                        <option value="X TO 3">X TO 3</option>
                                        <option value="X TO 4">X TO 4</option>
                                        <option value="X TO 5">X TO 5</option>
                                        <option value="X TKL 1">X TKL 1</option>
                                        <option value="X TKL 2">X TKL 2</option>
                                        <option value="XI RPL 1">XI RPL 1</option>
                                        <option value="XI RPL 2">XI RPL 2</option>
                                        <option value="XI TP 1">XI TP 1</option>
                                        <option value="XI TP 2">XI TP 2</option>
                                        <option value="XI TAV 1">XI TAV 1</option>
                                        <option value="XI TAV 2">XI TAV 2</option>
                                        <option value="XI TEI 1">XI TEI 1</option>
                                        <option value="XI TEI 2">XI TEI 2</option>
                                        <option value="XI TKR 1">XI TKR 1</option>
                                        <option value="XI TKR 2">XI TKR 2</option>
                                        <option value="XI TKR 3">XI TKR 3</option>
                                        <option value="XI TSM 1">XI TSM 1</option>
                                        <option value="XI TSM 2">XI TSM 2</option>
                                        <option value="XI TITL 1">XI TITL 1</option>
                                        <option value="XI TITL 2">XI TITL 2</option>
                                        <option value="XII RPL 1">XII RPL 1</option>
                                        <option value="XII RPL 2">XII RPL 2</option>
                                        <option value="XII TP 1">XII TP 1</option>
                                        <option value="XII TP 2">XII TP 2</option>
                                        <option value="XII TAV 1">XII TAV 1</option>
                                        <option value="XII TAV 2">XII TAV 2</option>
                                        <option value="XII TEI 1">XII TEI 1</option>
                                        <option value="XII TEI 2">XII TEI 2</option>
                                        <option value="XII TKR 1">XII TKR 1</option>
                                        <option value="XII TKR 2">XII TKR 2</option>
                                        <option value="XII TKR 3">XII TKR 3</option>
                                        <option value="XII TSM 1">XII TSM 1</option>
                                        <option value="XII TSM 2">XII TSM 2</option>
                                        <option value="XII TITL 1">XII TITL 1</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="jurusan" class="form-label">Jurusan</label>
                                    <select class="form-control" name="jurusan" required>
                                        <option value="">Pilih Jurusan</option>
                                        <?php
                                        $jurusan_result->data_seek(0);
                                        while ($jurusan = $jurusan_result->fetch_assoc()): ?>
                                            <option value="<?= $jurusan['nama_jurusan'] ?>"><?= $jurusan['nama_jurusan'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" name="tanggal_lahir" required>
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
                            <h5 class="modal-title" id="editModalLabel">Edit Siswa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <input type="hidden" name="id" id="edit-id">
                                <div class="mb-3">
                                    <label for="edit-nama" class="form-label">Nama</label>
                                    <input type="text" class="form-control" name="nama" id="edit-nama" required>
                                </div>
                                <div class="mb-3">
                                    <label for="kelas" class="form-label">Kelas</label>
                                    <select class="form-control" name="kelas" required>
                                        <option value="pilih kelas">pilih kelas</option>
                                        <option value="X PPLG 1">X PPLG 1</option>
                                        <option value="X PPLG 2">X PPLG 2</option>
                                        <option value="X TM 1">X TM 1</option>
                                        <option value="X TM 2">X TM 2</option>
                                        <option value="X TE 1">X TE 1</option>
                                        <option value="X TE 2">X TE 2</option>
                                        <option value="X TE 3">X TE 3</option>
                                        <option value="X TE 4">X TE 4</option>
                                        <option value="X TO 1">X TO 1</option>
                                        <option value="X TO 2">X TO 2</option>
                                        <option value="X TO 3">X TO 3</option>
                                        <option value="X TO 4">X TO 4</option>
                                        <option value="X TO 5">X TO 5</option>
                                        <option value="X TKL 1">X TKL 1</option>
                                        <option value="X TKL 2">X TKL 2</option>
                                        <option value="XI RPL 1">XI RPL 1</option>
                                        <option value="XI RPL 2">XI RPL 2</option>
                                        <option value="XI TP 1">XI TP 1</option>
                                        <option value="XI TP 2">XI TP 2</option>
                                        <option value="XI TAV 1">XI TAV 1</option>
                                        <option value="XI TAV 2">XI TAV 2</option>
                                        <option value="XI TEI 1">XI TEI 1</option>
                                        <option value="XI TEI 2">XI TEI 2</option>
                                        <option value="XI TKR 1">XI TKR 1</option>
                                        <option value="XI TKR 2">XI TKR 2</option>
                                        <option value="XI TKR 3">XI TKR 3</option>
                                        <option value="XI TSM 1">XI TSM 1</option>
                                        <option value="XI TSM 2">XI TSM 2</option>
                                        <option value="XI TITL 1">XI TITL 1</option>
                                        <option value="XI TITL 2">XI TITL 2</option>
                                        <option value="XII RPL 1">XII RPL 1</option>
                                        <option value="XII RPL 2">XII RPL 2</option>
                                        <option value="XII TP 1">XII TP 1</option>
                                        <option value="XII TP 2">XII TP 2</option>
                                        <option value="XII TAV 1">XII TAV 1</option>
                                        <option value="XII TAV 2">XII TAV 2</option>
                                        <option value="XII TEI 1">XII TEI 1</option>
                                        <option value="XII TEI 2">XII TEI 2</option>
                                        <option value="XII TKR 1">XII TKR 1</option>
                                        <option value="XII TKR 2">XII TKR 2</option>
                                        <option value="XII TKR 3">XII TKR 3</option>
                                        <option value="XII TSM 1">XII TSM 1</option>
                                        <option value="XII TSM 2">XII TSM 2</option>
                                        <option value="XII TITL 1">XII TITL 1</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="jurusan" class="form-label">Jurusan</label>
                                    <select class="form-control" name="jurusan" required>
                                        <option value="">Pilih Jurusan</option>
                                        <?php
                                        $jurusan_result->data_seek(0);
                                        while ($jurusan = $jurusan_result->fetch_assoc()): ?>
                                            <option value="<?= $jurusan['nama_jurusan'] ?>"><?= $jurusan['nama_jurusan'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit-tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" name="tanggal_lahir" id="edit-tanggal_lahir" required>
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
                            <h5 class="modal-title" id="deleteModalLabel">Hapus Siswa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <input type="hidden" name="id" id="delete-id">
                                <p>Apakah Anda yakin ingin menghapus siswa ini?</p>
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
            var nama = button.data('nama');
            var kelas = button.data('kelas');
            var jurusan = button.data('jurusan');
            var tanggal_lahir = button.data('tanggal_lahir');

            var modal = $(this);
            modal.find('#edit-id').val(id);
            modal.find('#edit-nama').val(nama);
            modal.find('select[name="kelas"]').val(kelas);
            modal.find('select[name="jurusan"]').val(jurusan);
            modal.find('#edit-tanggal_lahir').val(tanggal_lahir);
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
$conn->close();
?>