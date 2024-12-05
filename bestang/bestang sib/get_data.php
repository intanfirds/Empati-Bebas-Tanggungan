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
$sql = "
SELECT 
    (SELECT COUNT(*) 
     FROM pengajuan_prodi p
     JOIN Mahasiswa m ON p.id_mahasiswa = m.id
     WHERE m.prodi = 'Sistem Informasi Bisnis' AND p.path1 IS NOT NULL) AS jumlah_laporan_skripsi,
    (SELECT COUNT(*) 
     FROM pengajuan_prodi p
     JOIN Mahasiswa m ON p.id_mahasiswa = m.id
     WHERE m.prodi = 'Sistem Informasi Bisnis' AND p.path2 IS NOT NULL) AS jumlah_laporan_magang,
    (SELECT COUNT(*) 
     FROM pengajuan_prodi p
     JOIN Mahasiswa m ON p.id_mahasiswa = m.id
     WHERE m.prodi = 'Sistem Informasi Bisnis' AND p.path3 IS NOT NULL) AS jumlah_bebas_kompensasi,
    (SELECT COUNT(*) 
     FROM pengajuan_prodi p
     JOIN Mahasiswa m ON p.id_mahasiswa = m.id
     WHERE m.prodi = 'Sistem Informasi Bisnis' AND p.path4 IS NOT NULL) AS jumlah_toeic;
";

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
