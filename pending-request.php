<?php
// Include the database connection
include('koneksi.php');  // Make sure this is correctly pointing to the koneksi.php file

// Query to count pending requests
$sql = "
    SELECT 
        SUM(CASE WHEN COALESCE(ka.status1, '') = 'Menunggu' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN COALESCE(ka.status2, '') = 'Menunggu' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN COALESCE(kaj.status1, '') = 'Menunggu' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN COALESCE(kaj.status2, '') = 'Menunggu' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN COALESCE(kaj.status3, '') = 'Menunggu' THEN 1 ELSE 0 END) AS pending_count
    FROM pengajuan_akademik pa
    LEFT JOIN konfirmasi_akademik ka ON pa.id = ka.id_pengajuan
    LEFT JOIN pengajuan_jurusan pj ON pa.id_mahasiswa = pj.id_mahasiswa
    LEFT JOIN konfirmasi_admin_jurusan kaj ON pj.id = kaj.id_pengajuan
";

// Execute query
$result = sqlsrv_query($conn, $sql);

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
