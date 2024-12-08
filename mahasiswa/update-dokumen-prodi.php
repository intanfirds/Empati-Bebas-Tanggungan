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
    $queryCheck = "SELECT * FROM pengajuan_prodi WHERE id_mahasiswa = ?";
    $stmtCheck = sqlsrv_query($conn, $queryCheck, array($idMahasiswa));
    if ($stmtCheck === false) {
        die("Query SELECT gagal: " . print_r(sqlsrv_errors(), true));
    }

    // Jika data pengajuan belum ada, tambahkan entri baru
    if (!sqlsrv_has_rows($stmtCheck)) {
        $queryInsert = "INSERT INTO pengajuan_prodi (id_mahasiswa, last_modified) VALUES (?, GETDATE())";
        $stmtInsert = sqlsrv_query($conn, $queryInsert, array($idMahasiswa));
        if ($stmtInsert === false) {
            die("Query INSERT gagal: " . print_r(sqlsrv_errors(), true));
        }
    }

    // Ambil data lama dari database
    $query = "SELECT distribusi_laporan_skripsi, distribusi_laporan_magang, bebas_kompensasi, nilai_toeic FROM pengajuan_prodi WHERE id_mahasiswa = ?";
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
        if (!empty($existingFiles['distribusi_laporan_skripsi']) && file_exists($uploadDir . $existingFiles['distribusi_laporan_skripsi'])) {
            unlink($uploadDir . $existingFiles['distribusi_laporan_skripsi']);
        }

        // Upload file baru
        if (move_uploaded_file($file1['tmp_name'], $file1Path)) {
            // Simpan ke database
            $queryUpdate = "UPDATE pengajuan_prodi SET distribusi_laporan_skripsi = ?, path1 = ?, last_modified = GETDATE() WHERE id_mahasiswa = ?";
            $stmtUpdate = sqlsrv_query($conn, $queryUpdate, array($file1Name, $file1Path, $idMahasiswa));
            if ($stmtUpdate === false) {
                die("Query UPDATE gagal untuk dokumen1: " . print_r(sqlsrv_errors(), true));
            }
            echo "File Laporan Skripsi berhasil diupload.<br>";
        } else {
            die("Gagal mengupload File Laporan Skripsi.");
        }
    }

    // Proses upload dokumen 2
    if (isset($_FILES['dokumen2']) && $_FILES['dokumen2']['error'] == UPLOAD_ERR_OK) {
        $file2 = $_FILES['dokumen2'];
        $file2Name = basename($file2['name']);
        $file2Path = $uploadDir . $file2Name;

        // Hapus file lama jika ada
        if (!empty($existingFiles['distribusi_laporan_magang']) && file_exists($uploadDir . $existingFiles['distribusi_laporan_magang'])) {
            unlink($uploadDir . $existingFiles['distribusi_laporan_magang']);
        }

        // Upload file baru
        if (move_uploaded_file($file2['tmp_name'], $file2Path)) {
            // Simpan ke database
            $queryUpdate = "UPDATE pengajuan_prodi SET distribusi_laporan_magang = ?, path2 = ?, last_modified = GETDATE() WHERE id_mahasiswa = ?";
            $stmtUpdate = sqlsrv_query($conn, $queryUpdate, array($file2Name, $file2Path, $idMahasiswa));
            if ($stmtUpdate === false) {
                die("Query UPDATE gagal untuk dokumen2: " . print_r(sqlsrv_errors(), true));
            }
            echo "File Laporan Magang berhasil diupload.<br>";
        } else {
            die("Gagal mengupload File Laporan Magang.");
        }
    }

    // Proses upload dokumen 3
    if (isset($_FILES['dokumen3']) && $_FILES['dokumen3']['error'] == UPLOAD_ERR_OK) {
        $file3 = $_FILES['dokumen3'];
        $file3Name = basename($file3['name']);
        $file3Path = $uploadDir . $file3Name;

        // Hapus file lama jika ada
        if (!empty($existingFiles['bebas_kompensasi']) && file_exists($uploadDir . $existingFiles['bebas_kompensasi'])) {
            unlink($uploadDir . $existingFiles['bebas_kompensasi']);
        }

        // Upload file baru
        if (move_uploaded_file($file3['tmp_name'], $file3Path)) {
            // Simpan ke database
            $queryUpdate = "UPDATE pengajuan_prodi SET bebas_kompensasi = ?, path3 = ?, last_modified = GETDATE() WHERE id_mahasiswa = ?";
            $stmtUpdate = sqlsrv_query($conn, $queryUpdate, array($file3Name, $file3Path, $idMahasiswa));
            if ($stmtUpdate === false) {
                die("Query UPDATE gagal untuk dokumen3: " . print_r(sqlsrv_errors(), true));
            }
            echo "File Bebas Kompensasi berhasil diupload.<br>";
        } else {
            die("Gagal mengupload File Hasil Bebas Kompensasi.");
        }
    }

    // Proses upload dokumen 4
    if (isset($_FILES['dokumen4']) && $_FILES['dokumen4']['error'] == UPLOAD_ERR_OK) {
        $file4 = $_FILES['dokumen4'];
        $file4Name = basename($file4['name']);
        $file4Path = $uploadDir . $file4Name;

        // Hapus file lama jika ada
        if (!empty($existingFiles['nilai_toeic']) && file_exists($uploadDir . $existingFiles['nilai_toeic'])) {
            unlink($uploadDir . $existingFiles['nilai_toeic']);
        }

        // Upload file baru
        if (move_uploaded_file($file4['tmp_name'], $file4Path)) {
            // Simpan ke database
            $queryUpdate = "UPDATE pengajuan_prodi SET nilai_toeic = ?, path4 = ?, last_modified = GETDATE() WHERE id_mahasiswa = ?";
            $stmtUpdate = sqlsrv_query($conn, $queryUpdate, array($file4Name, $file4Path, $idMahasiswa));
            if ($stmtUpdate === false) {
                die("Query UPDATE gagal untuk dokumen4: " . print_r(sqlsrv_errors(), true));
            }
            echo "File Sertifikat TOEIC berhasil diupload.<br>";
        } else {
            die("Gagal mengupload File Hasil Sertifikat TOEIC.");
        }
    }

    // Update atau insert ke tabel konfirmasi_admin_prodi
    $queryCheckKonfirmasi = "SELECT * FROM konfirmasi_admin_prodi WHERE id_pengajuan = (
        SELECT id FROM pengajuan_prodi WHERE id_mahasiswa = ?
    )";
    $stmtCheckKonfirmasi = sqlsrv_query($conn, $queryCheckKonfirmasi, array($idMahasiswa));
    if ($stmtCheckKonfirmasi === false) {
        die("Query SELECT konfirmasi_admin_prodi gagal: " . print_r(sqlsrv_errors(), true));
    }
    // Ambil prodi mahasiswa
    $queryProdi = "SELECT prodi FROM mahasiswa WHERE id = ?";
    $stmtProdi = sqlsrv_query($conn, $queryProdi, array($idMahasiswa));
    if ($stmtProdi === false) {
        die("Query SELECT prodi gagal: " . print_r(sqlsrv_errors(), true));
    }

    $prodi = sqlsrv_fetch_array($stmtProdi, SQLSRV_FETCH_ASSOC)['prodi'];

    // Tentukan id_admin berdasarkan prodi
    if ($prodi == 'Teknik Informatika') {
        $idAdmin = 2; // admin_TI
    } elseif ($prodi == 'Sistem Informasi Bisnis') {
        $idAdmin = 5; // admin_SIB
    }

    // Debugging id_admin
    echo "ID Admin: $idAdmin<br>";


    if (!sqlsrv_has_rows($stmtCheckKonfirmasi)) {
        // Jika belum ada entri, tambahkan entri baru dengan id_admin
        $queryInsertKonfirmasi = "
        INSERT INTO konfirmasi_admin_prodi (
            id, id_pengajuan, id_admin, status1, status2, status3, status4, komentar, last_modified
        ) VALUES (
            (SELECT ISNULL(MAX(id), 0) + 1 FROM konfirmasi_admin_prodi),  -- Menambahkan ID baru secara manual
            (SELECT id FROM pengajuan_prodi WHERE id_mahasiswa = ?),
            ?, 'Menunggu', 'Menunggu', 'Menunggu', 'Menunggu', 'Menunggu', GETDATE()
        )";


        $stmtInsertKonfirmasi = sqlsrv_query($conn, $queryInsertKonfirmasi, array($idMahasiswa, $idAdmin));
        if ($stmtInsertKonfirmasi === false) {
            die("Query INSERT konfirmasi_admin_prodi gagal: " . print_r(sqlsrv_errors(), true));
        }
    } else {
        // Jika entri sudah ada, perbarui status dan komentar
        $queryUpdateKonfirmasi = "UPDATE konfirmasi_admin_prodi SET status1 = 'Menunggu',status2 = 'Menunggu',status3 = 'Menunggu',status4 ='Menunggu', komentar = 'Menunggu', last_modified = GETDATE() WHERE id_pengajuan = (
            SELECT id FROM pengajuan_prodi WHERE id_mahasiswa = ?
        )";
        $stmtUpdateKonfirmasi = sqlsrv_query($conn, $queryUpdateKonfirmasi, array($idMahasiswa));
        if ($stmtUpdateKonfirmasi === false) {
            die("Query UPDATE konfirmasi_admin_prodi gagal: " . print_r(sqlsrv_errors(), true));
        }
    }

    header('Location: admin-bebastanggungan.php');
    exit;
} else {
    die("Request method tidak valid.");
}
?>