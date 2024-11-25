<?php
session_start();
require_once 'koneksi.php'; // Menghubungkan ke database

$username = $_POST['username']; // Misalnya NIM atau username
$password = $_POST['password']; // Kata sandi

$query = "SELECT nama, nip, role FROM admin WHERE username = ? AND password = ?";

// Menyiapkan statement
$params = array($username, $password);
$stmt = sqlsrv_prepare($conn, $query, $params);

// Menjalankan query
if (sqlsrv_execute($stmt)) {
    // Jika data ditemukan
    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Simpan nama mahasiswa di session
        $_SESSION['nama_admin'] = $row['nama'];
        $_SESSION['nip_admin'] = $row['nip'];
        $_SESSION['role'] = $row['role'];

        switch ($row['role']) {
            case 'Jurusan':
                header("Location: /Empati-Bebas-Tanggungan/jurusan/index.php");
                break;
            case 'Bebas Tanggungan':
                header("Location: /Empati-Bebas-Tanggungan/bestang/index.php");
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
        // Jika login gagal
        echo "<script>alert('NIM atau Password salah!'); window.location.href='index-admin.html';</script>";
        exit;
    }
} else {
    echo "Query gagal dieksekusi!";
}
?>