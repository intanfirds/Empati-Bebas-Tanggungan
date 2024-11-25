<?php
include 'koneksi.php';
session_start();
$aksi = $_GET['aksi'];
$nama = $_POST['nama_admin'];
$nip = $_POST['nip_admin'];
if ($aksi == 'ubah') {
    if (isset($_POST['role'])) {
        $role = $_POST['role'];
        $query = "UPDATE admin SET nama='$nama', nip='$nip' WHERE role='$role'";
        if (sqlsrv_query($conn, $query)) {
            header("location: profile.php");
        }
    } else {
        echo "Gagal";
    }
}
?>