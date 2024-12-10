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

// Query untuk mendapatkan jumlah mahasiswa yang terverifikasi
$sql = "SELECT
    (SELECT COUNT(*) 
        FROM pengajuan_perpustakaan p
        JOIN  konfirmasi_perpus k ON p.id = k.id_pengajuan
        WHERE k.status = 'sesuai'
    ) AS mahasiswa_terverifikasi,
    (SELECT COUNT(*) 
        FROM pengajuan_perpustakaan p
        JOIN  konfirmasi_perpus k ON p.id = k.id_pengajuan
        WHERE k.status = 'menunggu' OR k.status = 'tidak sesuai'
    ) AS mahasiswa_belum_terverifikasi;
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