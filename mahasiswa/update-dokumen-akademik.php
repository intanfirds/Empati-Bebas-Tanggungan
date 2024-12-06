<?php
session_start();
include_once '../koneksi.php'; // Koneksi ke database

// Folder tujuan upload
$uploadDir = 'uploads/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pastikan session memiliki ID mahasiswa
    if (!isset($_SESSION['id_mahasiswa']) || empty($_SESSION['id_mahasiswa'])) {
        die("Session ID Mahasiswa tidak valid.");
    }
    $idMahasiswa = $_SESSION['id_mahasiswa'];

    // Debugging session ID
    echo "ID Mahasiswa: $idMahasiswa<br>";

    // Periksa folder uploads/
    if (!is_dir($uploadDir)) {
        die("Folder upload tidak ditemukan.");
    }

    // Cek data lama di database
    $queryCheck = "SELECT * FROM pengajuan_akademik WHERE id_mahasiswa = ?";
    $stmtCheck = sqlsrv_query($conn, $queryCheck, array($idMahasiswa));
    if ($stmtCheck === false) {
        die("Query SELECT gagal: " . print_r(sqlsrv_errors(), true));
    }

    // Jika data pengajuan belum ada, tambahkan entri baru
    if (!sqlsrv_has_rows($stmtCheck)) {
        $queryInsert = "INSERT INTO pengajuan_akademik (id_mahasiswa, last_modified) VALUES (?, GETDATE())";
        $stmtInsert = sqlsrv_query($conn, $queryInsert, array($idMahasiswa));
        if ($stmtInsert === false) {
            die("Query INSERT gagal: " . print_r(sqlsrv_errors(), true));
        }
    }

    // Ambil data lama dari database
    $query = "SELECT bukti_pelunasan_ukt, bukti_pengisian_data_alumni FROM pengajuan_akademik WHERE id_mahasiswa = ?";
    $stmt = sqlsrv_query($conn, $query, array($idMahasiswa));
    if ($stmt === false) {
        die("Query SELECT gagal: " . print_r(sqlsrv_errors(), true));
    }
    $existingFiles = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Debugging data lama
    echo "<pre>Data lama di database:";
    print_r($existingFiles);
    echo "</pre>";

    // Proses upload dokumen 1
    if (isset($_FILES['dokumen1']) && $_FILES['dokumen1']['error'] == UPLOAD_ERR_OK) {
        $file1 = $_FILES['dokumen1'];
        $file1Name = basename($file1['name']);
        $file1Path = $uploadDir . $file1Name;

        // Hapus file lama jika ada
        if (!empty($existingFiles['bukti_pelunasan_ukt']) && file_exists($uploadDir . $existingFiles['bukti_pelunasan_ukt'])) {
            unlink($uploadDir . $existingFiles['bukti_pelunasan_ukt']);
        }

        // Upload file baru
        if (move_uploaded_file($file1['tmp_name'], $file1Path)) {
            // Simpan ke database
            $queryUpdate = "UPDATE pengajuan_akademik SET bukti_pelunasan_ukt = ?, path1 = ?, last_modified = GETDATE() WHERE id_mahasiswa = ?";
            $stmtUpdate = sqlsrv_query($conn, $queryUpdate, array($file1Name, $file1Path, $idMahasiswa));
            if ($stmtUpdate === false) {
                die("Query UPDATE gagal untuk dokumen1: " . print_r(sqlsrv_errors(), true));
            }
            echo "File Bukti Pelunasan UKT berhasil diupload.<br>";
        } else {
            die("Gagal mengupload file Bukti Pelunasan UKT.");
        }
    }

    // Proses upload dokumen 2
    if (isset($_FILES['dokumen2']) && $_FILES['dokumen2']['error'] == UPLOAD_ERR_OK) {
        $file2 = $_FILES['dokumen2'];
        $file2Name = basename($file2['name']);
        $file2Path = $uploadDir . $file2Name;

        // Hapus file lama jika ada
        if (!empty($existingFiles['bukti_pengisian_data_alumni']) && file_exists($uploadDir . $existingFiles['bukti_pengisian_data_alumni'])) {
            unlink($uploadDir . $existingFiles['bukti_pengisian_data_alumni']);
        }

        // Upload file baru
        if (move_uploaded_file($file2['tmp_name'], $file2Path)) {
            // Simpan ke database
            $queryUpdate = "UPDATE pengajuan_akademik SET bukti_pengisian_data_alumni = ?, path2 = ?, last_modified = GETDATE() WHERE id_mahasiswa = ?";
            $stmtUpdate = sqlsrv_query($conn, $queryUpdate, array($file2Name, $file2Path, $idMahasiswa));
            if ($stmtUpdate === false) {
                die("Query UPDATE gagal untuk dokumen2: " . print_r(sqlsrv_errors(), true));
            }
            echo "File Bukti Pengisian Data Alumni berhasil diupload.<br>";
        } else {
            die("Gagal mengupload file Bukti Pengisian Data Alumni.");
        }
    }

    // Update atau insert ke tabel konfirmasi_akademik
    $queryCheckKonfirmasi = "SELECT * FROM konfirmasi_akademik WHERE id_pengajuan = (
        SELECT id FROM pengajuan_akademik WHERE id_mahasiswa = ?
    )";
    $stmtCheckKonfirmasi = sqlsrv_query($conn, $queryCheckKonfirmasi, array($idMahasiswa));
    if ($stmtCheckKonfirmasi === false) {
        die("Query SELECT konfirmasi_akademik gagal: " . print_r(sqlsrv_errors(), true));
    }

    $idAdmin = 1; // Ganti dengan ID admin yang sesuai jika ada, atau ID default jika tidak.

    if (!sqlsrv_has_rows($stmtCheckKonfirmasi)) {
        // Jika belum ada entri, tambahkan entri baru dengan id_admin
        $queryInsert = "INSERT INTO pengajuan_akademik (id_mahasiswa, last_modified) VALUES (?, GETDATE())";
        $stmtInsert = sqlsrv_query($conn, $queryInsert, array($idMahasiswa));
        if ($stmtInsert === false) {
            die("Query INSERT gagal: " . print_r(sqlsrv_errors(), true));
        }
        
    } else {
        // Jika entri sudah ada, perbarui status dan komentar
        $queryUpdateKonfirmasi = "UPDATE konfirmasi_akademik SET status = 'Menunggu', komentar = 'Menunggu', last_modified = GETDATE() WHERE id_pengajuan = (
            SELECT id FROM pengajuan_akademik WHERE id_mahasiswa = ?
        )";
        $stmtUpdateKonfirmasi = sqlsrv_query($conn, $queryUpdateKonfirmasi, array($idMahasiswa));
        if ($stmtUpdateKonfirmasi === false) {
            die("Query UPDATE konfirmasi_akademik gagal: " . print_r(sqlsrv_errors(), true));
        }
    }

    header('Location: admin-akademik.php');
    exit;
} else {
    die("Request method tidak valid.");
}
?>