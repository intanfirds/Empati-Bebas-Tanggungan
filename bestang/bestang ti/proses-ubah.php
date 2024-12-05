<?php
include 'koneksi.php';
session_start();

// Validasi apakah aksi yang diterima adalah 'update'
if ($_GET['aksi'] !== 'update_admin') {
    die("Error: Aksi tidak valid.");
}

// Validasi data yang diterima melalui POST
if (
    !isset($_POST['role']) ||
    !isset($_POST['nama_admin']) ||
    !isset($_POST['nip_admin'])
) {
    die("Error: Data tidak lengkap. Pastikan semua field sudah diisi.");
}

// Ambil data dari form
$role = $_POST['role'];
$nama = $_POST['nama_admin'];
$nip = $_POST['nip_admin'];

// Query untuk update nama dan nip berdasarkan role
$sql = "UPDATE Admin SET nama = ?, nip = ? WHERE role = ?";
$params = [$nama, $nip, $role];
$stmt = sqlsrv_query($conn, $sql, $params);
sqlsrv_commit($conn);

// Cek apakah query berhasil
if ($stmt) {
    header("Location: profile.php");
} else {
    echo "Terjadi kesalahan: " . print_r(sqlsrv_errors(), true);
}
?>
