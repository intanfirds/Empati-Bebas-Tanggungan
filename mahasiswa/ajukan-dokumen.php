<?php
session_start();
include '../koneksi.php';

$idMahasiswa = $_SESSION['id_mahasiswa'];
$tglMengajukan = date('Y-m-d H:i:s');

// Debugging Koneksi
if (!$conn) {
    die('Koneksi database gagal: ' . print_r(sqlsrv_errors(), true));
}

// Periksa apakah ada pengajuan sebelumnya untuk mahasiswa ini
$queryCheck = "
    SELECT p.id AS id_pengajuan, k.status
    FROM pengajuan_perpustakaan p
    LEFT JOIN konfirmasi_perpus k ON p.id = k.id_pengajuan
    WHERE p.id_mahasiswa = ?
";
$paramsCheck = array($idMahasiswa);
$stmtCheck = sqlsrv_query($conn, $queryCheck, $paramsCheck);

if ($stmtCheck === false) {
    die('Kesalahan saat memeriksa data pengajuan: ' . print_r(sqlsrv_errors(), true));
}

$dataExisting = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);

if ($dataExisting) {
    // Jika sudah ada pengajuan sebelumnya, perbarui data
    $idPengajuan = $dataExisting['id_pengajuan'];
    $status = strtolower(trim($dataExisting['status']));

    // Hanya perbarui jika status sebelumnya adalah "Tidak Sesuai" atau dokumen baru diajukan
    if (in_array($status, ['tidak sesuai', 'menunggu'])) {
        // Update tabel pengajuan_perpustakaan
        $queryUpdatePengajuan = "
    UPDATE pengajuan_perpustakaan
    SET tgl_mengajukan = GETDATE()
    WHERE id = ?
";
        $paramsUpdatePengajuan = array($idPengajuan);

        $stmtUpdatePengajuan = sqlsrv_query($conn, $queryUpdatePengajuan, $paramsUpdatePengajuan);

        if ($stmtUpdatePengajuan === false) {
            die('Kesalahan saat memperbarui data pengajuan: ' . print_r(sqlsrv_errors(), true));
        }

        // Update tabel konfirmasi_perpus
        $queryUpdateKonfirmasi = "
            UPDATE konfirmasi_perpus
            SET terakhir_dirubah = ?, status = ?, komentar = ?
            WHERE id_pengajuan = ?
        ";
        $paramsUpdateKonfirmasi = array(
            $tglMengajukan,
            'Menunggu',
            'Dokumen sedang dalam proses pemeriksaan',
            $idPengajuan
        );
        $stmtUpdateKonfirmasi = sqlsrv_query($conn, $queryUpdateKonfirmasi, $paramsUpdateKonfirmasi);

        if ($stmtUpdateKonfirmasi === false) {
            die('Kesalahan saat memperbarui data konfirmasi perpustakaan: ' . print_r(sqlsrv_errors(), true));
        }
    } else {
        // Jika status adalah "Disetujui", tidak perlu tindakan lebih lanjut
        $_SESSION['pesan'] = 'Pengajuan Anda sudah disetujui. Tidak perlu mengajukan ulang.';
        header('Location: admin-perpustakaan.php');
        exit();
    }
} else {
    // Jika tidak ada pengajuan sebelumnya, buat pengajuan baru
    $queryPengajuan = "
        INSERT INTO pengajuan_perpustakaan (id_mahasiswa, tgl_mengajukan) 
        OUTPUT INSERTED.id
        VALUES (?, ?)
    ";
    $paramsPengajuan = array($idMahasiswa, $tglMengajukan);
    $stmtPengajuan = sqlsrv_query($conn, $queryPengajuan, $paramsPengajuan);

    if ($stmtPengajuan === false) {
        die('Kesalahan saat membuat pengajuan baru: ' . print_r(sqlsrv_errors(), true));
    }

    // Ambil ID pengajuan baru
    $row = sqlsrv_fetch_array($stmtPengajuan, SQLSRV_FETCH_ASSOC);
    $idPengajuan = $row['id'];

    if (!$idPengajuan) {
        die('Gagal mendapatkan ID pengajuan yang baru dibuat!');
    }

    // Tambahkan data konfirmasi
    $queryKonfirmasi = "
        INSERT INTO konfirmasi_perpus (id_pengajuan, id_admin, terakhir_dirubah, status, komentar) 
        VALUES (?, ?, ?, ?, ?)
    ";
    $paramsKonfirmasi = array(
        $idPengajuan,
        4, // ID Admin Perpustakaan
        $tglMengajukan,
        'Menunggu',
        'Dokumen sedang dalam proses pemeriksaan'
    );
    $stmtKonfirmasi = sqlsrv_query($conn, $queryKonfirmasi, $paramsKonfirmasi);

    if ($stmtKonfirmasi === false) {
        die('Kesalahan saat menyimpan data ke konfirmasi perpustakaan: ' . print_r(sqlsrv_errors(), true));
    }
}

// Redirect ke halaman admin perpustakaan
$_SESSION['pesan'] = 'Pengajuan dokumen berhasil diajukan.';
header('Location: admin-perpustakaan.php');
exit();
