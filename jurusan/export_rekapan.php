<?php
// Include koneksi ke database
include 'koneksi.php';

// Query data
$sql = "SELECT nim, nama, jurusan, prodi, angkatan FROM rekapan_admin_jurusan";
$stmt = sqlsrv_query($conn, $sql);

// Periksa apakah query berhasil
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Header untuk file download (Excel)
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekapan_jurusan.xls");

// Tampilkan data
echo "NIM\tNama\tJurusan\tProdi\tAngkatan\n";

while ($data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo $data['nim'] . "\t" . $data['nama'] . "\t" . $data['jurusan'] . "\t" . $data['prodi'] . "\t" . $data['angkatan'] . "\n";
}
?>
