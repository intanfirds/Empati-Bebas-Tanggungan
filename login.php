<?php
session_start();
require_once 'koneksi.php'; // Menghubungkan ke database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username']; // Misalnya NIM atau username
    $password = $_POST['password']; // Kata sandi

    // Query untuk mengambil data admin berdasarkan username
    $query = "SELECT nama, nip, role, password FROM admin WHERE username = ?";
    $params = array($username);
    $stmt = sqlsrv_prepare($conn, $query, $params);

    // Menjalankan query
    if (sqlsrv_execute($stmt)) {
        // Jika data ditemukan
        if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            // Verifikasi password menggunakan password_verify
            if (password_verify($password, $row['password'])) {
                // Simpan informasi ke session
                $_SESSION['nama_admin'] = $row['nama'];
                $_SESSION['nip_admin'] = $row['nip'];
                $_SESSION['role'] = $row['role'];

                // Arahkan berdasarkan role
                switch ($row['role']) {
                    case 'Jurusan':
                        header("Location: /Empati-Bebas-Tanggungan/jurusan/index.php");
                        break;
                    case 'Admin TI':
                        header("Location: /Empati-Bebas-Tanggungan/bestang/bestang ti/index.php");
                        break;
                    case 'Admin SIB':
                        header("Location: /Empati-Bebas-Tanggungan/bestang/bestang sib/index.php");
                        break;
                    case 'Akademik':
                        header("Location: /Empati-Bebas-Tanggungan/akademik/index.php");
                        break;
                    case 'Perpustakaan':
                        header("Location: /Empati-Bebas-Tanggungan/perpustakaan/index.php");
                        break;
                    default:
                        echo "<script>alert('Role tidak dikenali!'); window.location.href='index-admin.html';</script>";
                        break;
                }
            } else {
                // Password salah
                echo "<script>alert('Password salah!'); window.location.href='index-admin.html';</script>";
                exit;
            }
        } else {
            // Username tidak ditemukan
            echo "<script>alert('Username tidak ditemukan!'); window.location.href='index-admin.html';</script>";
            exit;
        }
    } else {
        echo "Query gagal dieksekusi!";
    }
}
?>