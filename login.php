<?php
session_start();
require 'koneksi/db.php';

function handle_auth_error($message)
{
    $_SESSION['login_error'] = $message;
    header('Location: login.php');
    exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['nis'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? '');

    if (empty($username) || empty($password) || empty($role)) {
        handle_auth_error('Harap isi Username/NIS dan Password!');
    }

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        handle_auth_error('Token CSRF tidak valid!');
    }

    // Cek kredensial
    if ($role === 'siswa') {
        // Cek kredensial siswa
        if ($username === 'Siswa' && $password === '123456') {
            $user = [
                'id' => 2,
                'username' => 'siswa',
                'nis' => '123456',
                'role' => 'siswa'
            ];
        } else {
            handle_auth_error('Username atau Password siswa salah!');
        }
    } elseif ($role === 'admin') {
        // Cek kredensial admin
        if ($username === 'Admin' && $password === 'adminsmk') {
            $user = [
                'id' => 1,
                'username' => 'admin',
                'nis' => null,
                'role' => 'admin'
            ];
        } else {
            handle_auth_error('Username atau Password admin salah!');
        }
    } else {
        handle_auth_error('Role tidak valid!');
    }

    // Login berhasil
    session_regenerate_id(true);
    $_SESSION['auth'] = [
        'id' => $user['id'],
        'username' => $user['username'],
        'nis' => $user['nis'],
        'role' => $user['role'],
        'logged_in' => true
    ];

    $_SESSION['login_success'] = true; // Set flag untuk login berhasil
    header('Location: login.php'); // Tetap di halaman login untuk menampilkan notifikasi
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgb(96, 100, 105);
        }

        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            width: 100%;
            max-width: 450px;
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 80px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .btn-primary {
            background: #2575fc;
            border: none;
        }

        img.logo {
            width: 100px;
            /* Sesuaikan ukuran logo */
            margin-bottom: 20px;
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

<body>
    <div class="auth-container">
        <div class="auth-card">
            <img src="logo.jpg" alt="Logo" class="logo"> <!-- Ganti logo.jpg dengan path yang benar -->
            <h2 class="mb-3">Selamat Datang</h2>

            <div class="notification" id="notification">
                <strong>✔️</strong> Login Berhasil!
            </div>

            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="notification" id="errorNotification">
                    <strong>❌</strong> <?= htmlspecialchars($_SESSION['login_error']) ?>
                </div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['login_success'])): ?>
                <script>
                    document.getElementById('notification').style.display = 'block';
                    setTimeout(() => {
                        document.getElementById('notification').style.display = 'none';
                        window.location.href = "<?php echo $_SESSION['auth']['role'] === 'admin' ? './admin/dashboard.php' : './siswa/dashboard_siswa.php'; ?>";
                    }, 3000); // Tampilkan notifikasi selama 3 detik
                </script>
                <?php unset($_SESSION['login_success']); ?>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="mb-3">
                    <label class="form-label">Username </label>
                    <input type="text" name="nis" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="siswa">Siswa</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">Masuk</button>
            </form>
        </div>
    </div>

    <script>
        // Tampilkan notifikasi jika ada error
        const errorNotification = document.getElementById('errorNotification');
        if (errorNotification) {
            errorNotification.style.display = 'block';
            setTimeout(() => {
                errorNotification.style.display = 'none';
            }, 1000); // Tampilkan error notifikasi selama 3 detik
        }
    </script>
</body>

</html>