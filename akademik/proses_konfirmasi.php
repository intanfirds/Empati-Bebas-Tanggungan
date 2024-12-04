<?php
include 'koneksi.php';
session_start();
$_SESSION['nim'] = $_GET['nim'];

if (isset($_POST['status'])) {
    $status = $_POST['status'];
    // Cek status untuk mengetahui apakah sesuai atau tidak sesuai
    if ($status === 'sesuai') {
        $sql = "UPDATE k
            SET k.status = 'selesai'
            FROM konfirmasi_akademik k
            JOIN pengajuan_akademik p ON k.id_pengajuan = p.id
            JOIN dbo.Mahasiswa m ON p.id_mahasiswa = m.id
            WHERE m.nim = ?";
        $params = [$_SESSION['nim']];
        $stmt = sqlsrv_query($conn, $sql, $params);
        sqlsrv_commit($conn);
        if (isset($komentar)) {
            $sql = "UPDATE konfirmasi_akademik
                    SET komentar = '$komentar'
                    WHERE id_pengajuan = (
                        SELECT TOP 1 id
                        FROM pengajuan_akademik
                        WHERE id_mahasiswa = (SELECT id FROM Mahasiswa WHERE nim = ?)
                    );";
            $params = [$_SESSION['nim'], $komentar];
            $stmt = sqlsrv_query($conn, $sql, $params);
            sqlsrv_commit($conn);
            header("Location: tabel.php");
        }
    } else {
        $sql = "UPDATE k
            SET k.status = 'Tidak Sesuai'
            FROM konfirmasi_akademik k
            JOIN pengajuan_akademik p ON k.id_pengajuan = p.id
            JOIN dbo.Mahasiswa m ON p.id_mahasiswa = m.id
            WHERE m.nim = ?";
        $params = [$_SESSION['nim']];
        $stmt = sqlsrv_query($conn, $sql, $params);
        sqlsrv_commit($conn);
        if (isset($komentar)) {
            $sql = "UPDATE konfirmasi_akademik
                    SET komentar = '$komentar'
                    WHERE id_pengajuan = (
                        SELECT TOP 1 id
                        FROM pengajuan_akademik
                        WHERE id_mahasiswa = (SELECT id FROM Mahasiswa WHERE nim = ?)
                    );";
            $params = [$_SESSION['nim'], $komentar];
            $stmt = sqlsrv_query($conn, $sql, $params);
            sqlsrv_commit($conn);
            header("Location: tabel.php");
        }
    }
}

?>