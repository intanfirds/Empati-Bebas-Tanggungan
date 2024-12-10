<?php
// Include the database connection
include('koneksi.php');  

// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get the ID of the logged-in user
$idMahasiswa = $_SESSION['id_mahasiswa'];

// Query to count pending requests for the logged-in user
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

// Execute query with parameter
$params = array($idMahasiswa);
$result = sqlsrv_query($conn, $sql, $params);

// Check if query was executed successfully
if ($result) {
    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    if ($row) {
        // Set the number of pending requests
        $pendingRequests = $row['pending_count'];
    } else {
        $pendingRequests = 0;
    }
} else {
    // Handle error if query fails
    $pendingRequests = 0;
    die(print_r(sqlsrv_errors(), true));
}

// Close the database connection
sqlsrv_close($conn);

?>
