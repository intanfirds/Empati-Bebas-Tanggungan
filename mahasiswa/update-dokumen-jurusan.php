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
    $queryCheck = "SELECT * FROM pengajuan_jurusan WHERE id_mahasiswa = ?";
    $stmtCheck = sqlsrv_query($conn, $queryCheck, array($idMahasiswa));
    if ($stmtCheck === false) {
        die("Query SELECT gagal: " . print_r(sqlsrv_errors(), true));
    }

    // Jika data pengajuan belum ada, tambahkan entri baru
    if (!sqlsrv_has_rows($stmtCheck)) {
        $queryInsert = "INSERT INTO pengajuan_jurusan (id_mahasiswa, last_modified) VALUES (?, GETDATE())";
        $stmtInsert = sqlsrv_query($conn, $queryInsert, array($idMahasiswa));
        if ($stmtInsert === false) {
            die("Query INSERT gagal: " . print_r(sqlsrv_errors(), true));
        }
    }

    // Ambil data lama dari database
    $query = "SELECT file_bukti_publikasi, file_skripsi, hasil_akhir_skripsi FROM pengajuan_jurusan WHERE id_mahasiswa = ?";
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
        if (!empty($existingFiles['file_bukti_publikasi']) && file_exists($uploadDir . $existingFiles['file_bukti_publikasi'])) {
            unlink($uploadDir . $existingFiles['file_bukti_publikasi']);
        }

        // Upload file baru
        if (move_uploaded_file($file1['tmp_name'], $file1Path)) {
            // Simpan ke database
            $queryUpdate = "UPDATE pengajuan_jurusan SET file_bukti_publikasi = ?, path1 = ?, last_modified = GETDATE() WHERE id_mahasiswa = ?";
            $stmtUpdate = sqlsrv_query($conn, $queryUpdate, array($file1Name, $file1Path, $idMahasiswa));
            if ($stmtUpdate === false) {
                die("Query UPDATE gagal untuk dokumen1: " . print_r(sqlsrv_errors(), true));
            }
            echo "File Bukti Publikasi berhasil diupload.<br>";
        } else {
            die("Gagal mengupload File Bukti Publikasi.");
        }
    }

    // Proses upload dokumen 2
    if (isset($_FILES['dokumen2']) && $_FILES['dokumen2']['error'] == UPLOAD_ERR_OK) {
        $file2 = $_FILES['dokumen2'];
        $file2Name = basename($file2['name']);
        $file2Path = $uploadDir . $file2Name;

        // Hapus file lama jika ada
        if (!empty($existingFiles['file_skripsi']) && file_exists($uploadDir . $existingFiles['file_skripsi'])) {
            unlink($uploadDir . $existingFiles['file_skripsi']);
        }

        // Upload file baru
        if (move_uploaded_file($file2['tmp_name'], $file2Path)) {
            // Simpan ke database
            $queryUpdate = "UPDATE pengajuan_jurusan SET file_skripsi = ?, path2 = ?, last_modified = GETDATE() WHERE id_mahasiswa = ?";
            $stmtUpdate = sqlsrv_query($conn, $queryUpdate, array($file2Name, $file2Path, $idMahasiswa));
            if ($stmtUpdate === false) {
                die("Query UPDATE gagal untuk dokumen2: " . print_r(sqlsrv_errors(), true));
            }
            echo "File Skripsi berhasil diupload.<br>";
        } else {
            die("Gagal mengupload File Skripsi.");
        }
    }

    // Proses upload dokumen 3
    if (isset($_FILES['dokumen3']) && $_FILES['dokumen3']['error'] == UPLOAD_ERR_OK) {
        $file3 = $_FILES['dokumen3'];
        $file3Name = basename($file3['name']);
        $file3Path = $uploadDir . $file3Name;

        // Hapus file lama jika ada
        if (!empty($existingFiles['hasil_akhir_skripsi']) && file_exists($uploadDir . $existingFiles['hasil_akhir_skripsi'])) {
            unlink($uploadDir . $existingFiles['hasil_akhir_skripsi']);
        }

        // Upload file baru
        if (move_uploaded_file($file3['tmp_name'], $file3Path)) {
            // Simpan ke database
            $queryUpdate = "UPDATE pengajuan_jurusan SET hasil_akhir_skripsi = ?, path3 = ?, last_modified = GETDATE() WHERE id_mahasiswa = ?";
            $stmtUpdate = sqlsrv_query($conn, $queryUpdate, array($file3Name, $file3Path, $idMahasiswa));
            if ($stmtUpdate === false) {
                die("Query UPDATE gagal untuk dokumen3: " . print_r(sqlsrv_errors(), true));
            }
            echo "File Hasil Akhir Skripsi berhasil diupload.<br>";
        } else {
            die("Gagal mengupload File Hasil Akhir Skripsi.");
        }
    }

    // Update atau insert ke tabel konfirmasi_akademik
    $queryCheckKonfirmasi = "SELECT * FROM konfirmasi_admin_jurusan WHERE id_pengajuan = (
        SELECT id FROM pengajuan_jurusan WHERE id_mahasiswa = ?
    )";
    $stmtCheckKonfirmasi = sqlsrv_query($conn, $queryCheckKonfirmasi, array($idMahasiswa));
    if ($stmtCheckKonfirmasi === false) {
        die("Query SELECT konfirmasi_admin_jurusan gagal: " . print_r(sqlsrv_errors(), true));
    }

    $idAdmin = 3; // Ganti dengan ID admin yang sesuai jika ada, atau ID default jika tidak.

    if (!sqlsrv_has_rows($stmtCheckKonfirmasi)) {
        // Jika belum ada entri, tambahkan entri baru dengan id_admin
        $queryInsertKonfirmasi = "INSERT INTO konfirmasi_admin_jurusan (id_pengajuan, id_admin, status, komentar, last_modified) VALUES (
            (SELECT id FROM pengajuan_jurusan WHERE id_mahasiswa = ?), ?, 'Menunggu', 'Menunggu', GETDATE()
        )";
        $stmtInsertKonfirmasi = sqlsrv_query($conn, $queryInsertKonfirmasi, array($idMahasiswa, $idAdmin));
        if ($stmtInsertKonfirmasi === false) {
            die("Query INSERT konfirmasi_admin_jurusan gagal: " . print_r(sqlsrv_errors(), true));
        }
    } else {
        // Jika entri sudah ada, perbarui status dan komentar
        $queryUpdateKonfirmasi = "UPDATE konfirmasi_admin_jurusan SET status = 'Menunggu', komentar = 'Menunggu', last_modified = GETDATE() WHERE id_pengajuan = (
            SELECT id FROM pengajuan_jurusan WHERE id_mahasiswa = ?
        )";
        $stmtUpdateKonfirmasi = sqlsrv_query($conn, $queryUpdateKonfirmasi, array($idMahasiswa));
        if ($stmtUpdateKonfirmasi === false) {
            die("Query UPDATE konfirmasi_admin_jurusan gagal: " . print_r(sqlsrv_errors(), true));
        }
    }

    header('Location: admin-jurusan.php');
    exit;
} else {
    die("Request method tidak valid.");
}
?>