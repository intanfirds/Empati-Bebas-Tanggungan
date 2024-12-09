<?php
// Koneksi ke database SQL Server
$serverName = "localhost"; // Ganti dengan nama server Anda
$connectionOptions = array(
    "Database" => "bebastanggungangg", // Ganti dengan nama database Anda
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
            (SELECT COUNT(*) FROM [dbo].[pengajuan_akademik] WHERE [bukti_pelunasan_ukt] IS NOT NULL) AS jumlah_bukti_pelunasan,
            (SELECT COUNT(*) FROM [dbo].[pengajuan_akademik] WHERE [bukti_pengisian_data_alumni] IS NOT NULL) AS jumlah_bukti_pengisian,
            (SELECT COUNT(*) FROM [dbo].[konfirmasi_akademik] WHERE [status1] = 'sesuai' AND [status2] ='sesuai') AS jumlah_selesai";
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