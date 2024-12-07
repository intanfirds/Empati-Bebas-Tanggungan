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

    // Cek data lama di database (Pengajuan Akademik)
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

    // Proses upload dokumen 1 (Bukti Pelunasan UKT)
    if (isset($_FILES['dokumen1']) && $_FILES['dokumen1']['error'] == UPLOAD_ERR_OK) {
        $file1 = $_FILES['dokumen1'];
        $file1Name = basename($file1['name']);
        $file1Path = $uploadDir . $file1Name;

        // Hapus file lama jika ada
        if (!empty($existingFiles['bukti_pelunasan_ukt']) && file_exists($uploadDir . $existingFiles['bukti_pelunasan_ukt'])) {
            unlink($uploadDir . $existingFiles['bukti_pelunasan_ukt']);
        }

        // Validasi file
        if (!in_array(pathinfo($file1['name'], PATHINFO_EXTENSION), ['pdf', 'docx'])) {
            die("File harus bertipe PDF atau DOCX.");
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

    // Proses upload dokumen 2 (Bukti Pengisian Data Alumni)
    if (isset($_FILES['dokumen2']) && $_FILES['dokumen2']['error'] == UPLOAD_ERR_OK) {
        $file2 = $_FILES['dokumen2'];
        $file2Name = basename($file2['name']);
        $file2Path = $uploadDir . $file2Name;

        // Hapus file lama jika ada
        if (!empty($existingFiles['bukti_pengisian_data_alumni']) && file_exists($uploadDir . $existingFiles['bukti_pengisian_data_alumni'])) {
            unlink($uploadDir . $existingFiles['bukti_pengisian_data_alumni']);
        }

        // Validasi file
        if (!in_array(pathinfo($file2['name'], PATHINFO_EXTENSION), ['pdf', 'docx'])) {
            die("File harus bertipe PDF atau DOCX.");
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

    // Perbarui status dan komentar di tabel konfirmasi_akademik
    $queryCheckKonfirmasi = "SELECT * FROM konfirmasi_akademik WHERE id_pengajuan = (
        SELECT TOP 1 id FROM pengajuan_akademik WHERE id_mahasiswa = ?
        ORDER BY id DESC
    )";

    $stmtCheckKonfirmasi = sqlsrv_query($conn, $queryCheckKonfirmasi, array($idMahasiswa));
    if ($stmtCheckKonfirmasi === false) {
        die("Query SELECT konfirmasi_akademik gagal: " . print_r(sqlsrv_errors(), true));
    }

    if (!sqlsrv_has_rows($stmtCheckKonfirmasi)) {
        // Jika belum ada entri, tambahkan entri baru
        $queryInsertKonfirmasi = "INSERT INTO konfirmasi_akademik (id_pengajuan, status1, status2, komentar, last_modified) 
        VALUES ((SELECT TOP 1 id FROM pengajuan_akademik WHERE id_mahasiswa = ? ORDER BY id DESC), 'Menunggu', 'Menunggu', 'Menunggu', GETDATE())";

        $stmtInsertKonfirmasi = sqlsrv_query($conn, $queryInsertKonfirmasi, array($idMahasiswa));
        if ($stmtInsertKonfirmasi === false) {
            die("Query INSERT gagal untuk konfirmasi_akademik: " . print_r(sqlsrv_errors(), true));
        }
    } else {
        // Jika entri sudah ada, perbarui status dan komentar
        $queryUpdateKonfirmasi = "UPDATE konfirmasi_akademik SET status1 = 'Menunggu', status2 = 'Menunggu', komentar = 'Menunggu', last_modified = GETDATE() 
                                  WHERE id_pengajuan = (SELECT id FROM pengajuan_akademik WHERE id_mahasiswa = ?)";
        $stmtUpdateKonfirmasi = sqlsrv_query($conn, $queryUpdateKonfirmasi, array($idMahasiswa));
        if ($stmtUpdateKonfirmasi === false) {
            die("Query UPDATE gagal untuk konfirmasi_akademik: " . print_r(sqlsrv_errors(), true));
        }
    }

    // Redirect ke halaman admin setelah selesai
    header('Location: admin-akademik.php');
    exit;
} else {
    die("Request method tidak valid.");
}
?>