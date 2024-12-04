<?php
session_start();
include_once '../koneksi.php';

$idMahasiswa = $_SESSION['id_mahasiswa'];
$uploadDir = 'uploads/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil file lama dari database
    $query = "SELECT bukti_pelunasan_ukt, bukti_pengisian_data_alumni FROM pengajuan_akademik WHERE id_mahasiswa = ?";
    $stmt = sqlsrv_query($conn, $query, array($idMahasiswa));
    $existingFiles = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Proses pengunggahan file Bukti Pelunasan UKT
    if (isset($_FILES['dokumen1']) && $_FILES['dokumen1']['error'] == UPLOAD_ERR_OK) {
        $file1 = $_FILES['dokumen1'];
        $file1Path = $uploadDir . basename($file1['name']);

        // Hapus file lama jika ada
        if (!empty($existingFiles['bukti_pelunasan_ukt']) && file_exists($uploadDir . $existingFiles['bukti_pelunasan_ukt'])) {
            unlink($uploadDir . $existingFiles['bukti_pelunasan_ukt']);
        }

        // Pindahkan file baru ke folder tujuan
        if (move_uploaded_file($file1['tmp_name'], $file1Path)) {
            // Update database dengan nama file baru
            $queryUpdate = "UPDATE pengajuan_akademik SET bukti_pelunasan_ukt = ? WHERE id_mahasiswa = ?";
            sqlsrv_query($conn, $queryUpdate, array($file1['name'], $idMahasiswa));
        }
    }

    // Proses pengunggahan file Bukti Pengisian Data Alumni
    if (isset($_FILES['dokumen2']) && $_FILES['dokumen2']['error'] == UPLOAD_ERR_OK) {
        $file2 = $_FILES['dokumen2'];
        $file2Path = $uploadDir . basename($file2['name']);

        // Hapus file lama jika ada
        if (!empty($existingFiles['bukti_pengisian_data_alumni']) && file_exists($uploadDir . $existingFiles['bukti_pengisian_data_alumni'])) {
            unlink($uploadDir . $existingFiles['bukti_pengisian_data_alumni']);
        }

        // Pindahkan file baru ke folder tujuan
        if (move_uploaded_file($file2['tmp_name'], $file2Path)) {
            // Update database dengan nama file baru
            $queryUpdate = "UPDATE pengajuan_akademik SET bukti_pengisian_data_alumni = ? WHERE id_mahasiswa = ?";
            sqlsrv_query($conn, $queryUpdate, array($file2['name'], $idMahasiswa));
        }
    }

    // Redirect kembali ke halaman form
    header('Location: admin-akademik.php');
    exit;
}
?>
