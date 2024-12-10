<?php
session_start();
include '../koneksi.php';

$idMahasiswa = $_SESSION['id_mahasiswa'];
$tglMengajukan = date('Y-m-d H:i:s');

// Debugging Koneksi
if (!$conn) {
    die('Koneksi database gagal: ' . print_r(sqlsrv_errors(), true));
}

// Insert ke tabel pengajuan_perpustakaan dengan OUTPUT INSERTED.id
$queryPengajuan = "
    INSERT INTO pengajuan_perpustakaan (id_mahasiswa, tgl_mengajukan) 
    OUTPUT INSERTED.id  -- Ganti 'id_pengajuan' dengan nama kolom yang benar
    VALUES (?, ?)
";
$params = array($idMahasiswa, $tglMengajukan);
$stmt = sqlsrv_query($conn, $queryPengajuan, $params);

if ($stmt === false) {
    $errors = sqlsrv_errors();
    echo "Terjadi kesalahan saat mengajukan dokumen: " . print_r($errors, true);
    exit();
}

// Ambil ID pengajuan
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$idPengajuan = $row['id']; // Ganti 'id_pengajuan' dengan nama kolom yang benar

// Cek nilai ID pengajuan
if ($idPengajuan) {
    error_log("ID Pengajuan berhasil diambil: " . $idPengajuan);
} else {
    error_log("ID Pengajuan tidak ditemukan!");
}

// Insert ke tabel konfirmasi_perpus dengan status Menunggu
$queryKonfirmasi = "
    INSERT INTO konfirmasi_perpus (id_pengajuan, id_admin, file_konfirmasi, terakhir_dirubah, status, komentar) 
    VALUES (?, ?, ?, ?, ?, ?)
";
$paramsKonfirmasi = array($idPengajuan, 4, 'file_konfirmasi.pdf', $tglMengajukan, 'Menunggu', 'Dokumen sedang dalam proses pemeriksaan');
$stmtKonfirmasi = sqlsrv_query($conn, $queryKonfirmasi, $paramsKonfirmasi);

if ($stmtKonfirmasi === false) {
    $errors = sqlsrv_errors();
    echo "Terjadi kesalahan saat menyimpan data ke konfirmasi perpustakaan: " . print_r($errors, true);
    exit();
}

// Berhasil
header('Location: admin-perpustakaan.php');
exit();
?>