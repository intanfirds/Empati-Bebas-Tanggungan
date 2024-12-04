<?php
// Koneksi ke database SQL Server
$serverName = "localhost"; // Ganti dengan nama server Anda
$connectionOptions = array(
    "Database" => "bebastanggungan", // Ganti dengan nama database Anda
    "Uid" => "", // Ganti dengan username database Anda
    "PWD" => "" // Ganti dengan password database Anda
);

// Membuat koneksi
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Cek koneksi
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Query untuk mendapatkan jumlah mahasiswa yang mengupload file
$sql = "SELECT 
            (SELECT COUNT(*) FROM [dbo].[pengajuan_prodi] WHERE [path1] IS NOT NULL) AS jumlah_laporan_skripsi,
            (SELECT COUNT(*) FROM [dbo].[pengajuan_prodi] WHERE [path2] IS NOT NULL) AS jumlah_laporan_magang,
            (SELECT COUNT(*) FROM [dbo].[pengajuan_prodi] WHERE [path3] IS NOT NULL) AS jumlah_bebas_kompensasi,
            (SELECT COUNT(*) FROM [dbo].[pengajuan_prodi] WHERE [path4] IS NOT NULL) AS jumlah_toeic";
$stmt = sqlsrv_query($conn, $sql);

// Cek hasil query
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
echo json_encode($data);

// Menutup koneksi
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
