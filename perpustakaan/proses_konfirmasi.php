<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role'])) {
    die("Anda tidak memiliki akses.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_GET['nim'];
    $status = $_POST['status']; // Array status dari setiap file
    $komentar = $_POST['komentar'];
    $last_modified = date('Y-m-d H:i:s'); // Waktu terakhir dimodifikasi

    // Query untuk mendapatkan ID pengajuan berdasarkan NIM
    $query_pengajuan = "SELECT p.id FROM pengajuan_perpustakaan p JOIN Mahasiswa m ON m.id = p.id_mahasiswa WHERE m.nim = ?";
    $params_pengajuan = [$nim];
    $stmt_pengajuan = sqlsrv_query($conn, $query_pengajuan, $params_pengajuan);

    if ($stmt_pengajuan === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $data_pengajuan = sqlsrv_fetch_array($stmt_pengajuan, SQLSRV_FETCH_ASSOC);

    if ($data_pengajuan === null) {
        die("Data pengajuan tidak ditemukan.");
    }

    $id_pengajuan = $data_pengajuan['id'];

    // Cek jika mahasiswa baru pertama kali mengirim file, buatkan ID di tabel konfirmasi_akademik
    $query_cek_konfirmasi = "SELECT id FROM konfirmasi_perpus WHERE id_pengajuan = ?";
    $params_cek_konfirmasi = [$id_pengajuan];
    $stmt_cek_konfirmasi = sqlsrv_query($conn, $query_cek_konfirmasi, $params_cek_konfirmasi);

    if ($stmt_cek_konfirmasi === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $data_konfirmasi = sqlsrv_fetch_array($stmt_cek_konfirmasi, SQLSRV_FETCH_ASSOC);

    if ($data_konfirmasi === null) {
        // Insert data baru ke tabel konfirmasi_akademik jika belum ada
        $query_insert_konfirmasi = "INSERT INTO konfirmasi_akademik (id_pengajuan, status1, status2, komentar, last_modified) VALUES (?, NULL, NULL, NULL, ?)";
        $params_insert_konfirmasi = [$id_pengajuan, $last_modified];
        $stmt_insert_konfirmasi = sqlsrv_query($conn, $query_insert_konfirmasi, $params_insert_konfirmasi);

        if ($stmt_insert_konfirmasi === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    // Update data konfirmasi_akademik
    $query_update = "UPDATE konfirmasi_akademik SET status = ?,  komentar = ?, last_modified = ? WHERE id_pengajuan = ?";
    $params_update = [
        $status,
        $komentar,
        $last_modified,
        $id_pengajuan
    ];

    $stmt_update = sqlsrv_query($conn, $query_update, $params_update);

    if ($stmt_update === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Redirect kembali ke halaman sebelumnya dengan pesan sukses
    $_SESSION['success_message'] = "Data konfirmasi berhasil diperbarui.";
    header("Location: tabel.php");
    exit();
} else {
    die("Metode request tidak valid.");
}
?>
