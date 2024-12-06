<?php
include 'koneksi.php';
session_start();
$nim = $_GET['nim'];
$komentar = $_POST['komentar'];
$status = $_POST['status'];

if (isset($_POST['status'])) {
    // Cek status untuk mengetahui apakah sesuai atau tidak sesuai
    if ($status === 'sesuai') {
        $sql_selesai = "UPDATE k
                        SET 
                            k.status = 'selesai',
                            k.last_modified = GETDATE(),
                            k.komentar = ?
                        FROM konfirmasi_akademik k
                        JOIN pengajuan_akademik p ON k.id_pengajuan = p.id
                        JOIN dbo.Mahasiswa m ON p.id_mahasiswa = m.id
                        WHERE m.nim = ?
                        ";
        $params_selesai = [$komentar, $nim];
        $stmt_selesai = sqlsrv_query($conn, $sql_selesai, $params_selesai);
        sqlsrv_commit($conn);
        if ($stmt_selesai) {
            header("Location: tabel.php");
            exit();
        } else {
            die("Query UPDATE gagal: " . print_r(sqlsrv_errors(), true));
        }
        
        
    } else if ($status === 'tidak_sesuai') {
        $sql_selesai = "UPDATE k
                        SET 
                            k.status = 'tidak sesuai',
                            k.last_modified = GETDATE(),
                            k.komentar = ?
                        FROM konfirmasi_akademik k
                        JOIN pengajuan_akademik p ON k.id_pengajuan = p.id
                        JOIN dbo.Mahasiswa m ON p.id_mahasiswa = m.id
                        WHERE m.nim = ?
                        ";
        $params_selesai = [$komentar, $nim];
        $stmt_selesai = sqlsrv_query($conn, $sql_selesai, $params_selesai);
        sqlsrv_commit($conn);
        if ($stmt_selesai) {
            header("Location: tabel.php");
            exit();
        } else {
            die("Query UPDATE gagal: " . print_r(sqlsrv_errors(), true));
        }
    }
} else {
    // Cek apakah data sudah diisi
    echo 'Error: Data belum diisi';
}

?>