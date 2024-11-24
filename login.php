<?php
session_start();
require_once 'koneksi.php'; // Menghubungkan ke database

// // Mengecek apakah form login telah dikirim
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $username = trim($_POST['username']);
//     $password = trim($_POST['password']);

//     if (empty($username) || empty($password)) {
//         echo "<script>alert('Username dan password harus diisi!'); window.location.href='index.html';</script>";
//         exit;
//     }

//     // Mengecek di tabel admin
//     $sqlAdmin = "SELECT * FROM admin WHERE username = ? AND password = ?";
//     $paramsAdmin = array($username, $password);
//     $stmtAdmin = sqlsrv_query($conn, $sqlAdmin, $paramsAdmin);

//     if ($stmtAdmin && sqlsrv_has_rows($stmtAdmin)) {
//         $admin = sqlsrv_fetch_array($stmtAdmin, SQLSRV_FETCH_ASSOC);
//         $_SESSION['username'] = $admin['username'];
//         $_SESSION['role'] = $admin['role'];
//         $_SESSION['admin_id'] = $admin['id'];


//         // Redirect ke laman sesuai role admin
//         switch ($admin['role']) {
//             case 'Jurusan':
//                 header("Location: /Empati-Bebas-Tanggungan/jurusan/index.html");
//                 break;
//             case 'Bebas Tanggungan':
//                 header("Location: /Empati-Bebas-Tanggungan/bestang/index.html");
//                 break;
//             case 'Akademik':
//                 $row = sqlsrv_fecth_array($stmtAdmin, SQLSRV_FETCH_ASSOC);
//                 $_SESSION['nama_admin'] = $row['nama'];
//                 header("Location: /Empati-Bebas-Tanggungan/akademik/index.php");
//                 break;
//             case 'Perpustakaan':
//                 header("Location: /Empati-Bebas-Tanggungan/perpustakaan/index.html");
//                 break;
//             default:
//                 echo "<script>alert('Role tidak dikenali!'); window.location.href='index.html';</script>";
//                 break;
//         }
//         exit;
//     }

$username = $_POST['username']; // Misalnya NIM atau username
$password = $_POST['password']; // Kata sandi

$query = "SELECT nama, nip FROM admin WHERE username = ? AND password = ?";

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

        // Redirect ke halaman dashboard atau halaman lain setelah login sukses
        header("Location: /Empati-Bebas-Tanggungan/akademik/index.php");
        exit();
    } else {
        // Jika login gagal
        echo "<script>alert('NIM atau Password salah!'); window.location.href='index-mahasiswa.html';</script>";
        exit;
    }
} else {
    echo "Query gagal dieksekusi!";
}

//     // Mengecek di tabel mahasiswa
//     $sqlMahasiswa = "SELECT * FROM mahasiswa WHERE username = ? AND password = ?";
//     $paramsMahasiswa = array($username, $password);
//     $stmtMahasiswa = sqlsrv_query($conn, $sqlMahasiswa, $paramsMahasiswa);

//     if ($stmtMahasiswa && sqlsrv_has_rows($stmtMahasiswa)) {
//         $mahasiswa = sqlsrv_fetch_array($stmtMahasiswa, SQLSRV_FETCH_ASSOC);
//         $_SESSION['username'] = $mahasiswa['nim'];
//         $_SESSION['role'] = 'mahasiswa';
//         $_SESSION['mahasiswa_id'] = $mahasiswa['id'];

//         // Redirect ke laman mahasiswa
//         header("Location: /Empati-Bebas-Tanggungan/mahasiswa/index.php");
//         exit;
//     }

//     // Jika username/password salah
//     echo "<script>alert('Username atau password salah!'); window.location.href='index.html';</script>";
//     exit;
// }
?>