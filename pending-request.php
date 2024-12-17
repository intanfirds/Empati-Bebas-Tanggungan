<?php
include('koneksi.php');  

// Memulai session jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Mendapatkan ID mahasiswa yang sedang login dari session
$idMahasiswa = $_SESSION['id_mahasiswa'];

// Query ngitung 'Menunggu'
$sql = "
    SELECT 
        -- Pengajuan Akademik
        SUM(CASE WHEN COALESCE(ka.status1, '') = 'Menunggu' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN COALESCE(ka.status2, '') = 'Menunggu' THEN 1 ELSE 0 END) +

        -- Pengajuan Jurusan
        SUM(CASE WHEN COALESCE(kaj.status1, '') = 'Menunggu' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN COALESCE(kaj.status2, '') = 'Menunggu' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN COALESCE(kaj.status3, '') = 'Menunggu' THEN 1 ELSE 0 END) +

        -- Pengajuan Prodi
        SUM(CASE WHEN COALESCE(kap.status1, '') = 'Menunggu' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN COALESCE(kap.status2, '') = 'Menunggu' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN COALESCE(kap.status3, '') = 'Menunggu' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN COALESCE(kap.status4, '') = 'Menunggu' THEN 1 ELSE 0 END) +

        -- Pengajuan Perpustakaan
        SUM(CASE WHEN COALESCE(kp.status, '') = 'Menunggu' THEN 1 ELSE 0 END) AS pending_count
    FROM mahasiswa m
    LEFT JOIN pengajuan_akademik pa ON m.id = pa.id_mahasiswa
    LEFT JOIN konfirmasi_akademik ka ON pa.id = ka.id_pengajuan
    LEFT JOIN pengajuan_jurusan pj ON m.id = pj.id_mahasiswa
    LEFT JOIN konfirmasi_admin_jurusan kaj ON pj.id = kaj.id_pengajuan
    LEFT JOIN pengajuan_prodi pp ON m.id = pp.id_mahasiswa
    LEFT JOIN konfirmasi_admin_prodi kap ON pp.id = kap.id_pengajuan
    LEFT JOIN pengajuan_perpustakaan ppustaka ON m.id = ppustaka.id_mahasiswa
    LEFT JOIN konfirmasi_perpus kp ON ppustaka.id = kp.id_pengajuan
    WHERE m.id = ?
";

// Menjalankan query dengan parameter id mahasiswa
$params = array($idMahasiswa);
$result = sqlsrv_query($conn, $sql, $params);

// Check query apakah berhasil?
if ($result) {
    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    if ($row) {
        // Menyimpan jumlah yang statusnya 'Menunggu'
        $pendingRequests = $row['pending_count'];
    } else {
        $pendingRequests = 0;
    }
} else {
    // Jika query gagal
    $pendingRequests = 0;
    die(print_r(sqlsrv_errors(), true));
}

sqlsrv_close($conn);

?>
