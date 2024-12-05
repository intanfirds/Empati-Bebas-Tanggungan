<?php
include 'koneksi.php';
session_start();
$nim = $_GET['nim'];


if (isset($_POST['status'])) {
    $status = $_POST['status'];
    // Cek status untuk mengetahui apakah sesuai atau tidak sesuai
    if ($status === 'sesuai') {
        $sql_selesai = "UPDATE k
            SET k.status = 'selesai'
            FROM konfirmasi_akademik k
            JOIN pengajuan_akademik p ON k.id_pengajuan = p.id
            JOIN dbo.Mahasiswa m ON p.id_mahasiswa = m.id
            WHERE m.nim = ?";
        $params_selesai = [$nim];
        $stmt_selesai = sqlsrv_query($conn, $sql_selesai, $params_selesai);
        sqlsrv_commit($conn);
        if ($sql_selesai) {
            if (isset($komentar)) {
            $sql_komentar = "UPDATE konfirmasi_akademik
                    SET komentar = '$komentar'
                    WHERE id_pengajuan = (
                        SELECT TOP 1 id
                        FROM pengajuan_akademik
                        WHERE id_mahasiswa = (SELECT id FROM Mahasiswa WHERE nim = ?)
                    );";
            $params_komentar = [$nim, $komentar];
            $stmt_komentar = sqlsrv_query($conn, $sql_komentar, $params_komentar);
            sqlsrv_commit($conn);
            if ($sql_komentar) {
                header("Location: tabel.php");
                exit();
            } else {
                header("Location: tabel.php");
                exit();
            }
            
            } else {
                echo 'Error: Komentar belum diisi';
            }
        } else {
            echo 'Error: '. print_r(sqlsrv_errors(), true);
        }
        
        
    } else {
        $sql_selesai = "UPDATE k
            SET k.status = 'Tidak Sesuai'
            FROM konfirmasi_akademik k
            JOIN pengajuan_akademik p ON k.id_pengajuan = p.id
            JOIN dbo.Mahasiswa m ON p.id_mahasiswa = m.id
            WHERE m.nim = ?";
        $params_selesai = [$nim];
        $stmt_selesai = sqlsrv_query($conn, $sql_selesai, $params_selesai);
        sqlsrv_commit($conn);
        if ($sql_selesai) {
            if (isset($komentar)) {
            $sql_komentar = "UPDATE konfirmasi_akademik
                    SET komentar = '$komentar'
                    WHERE id_pengajuan = (
                        SELECT TOP 1 id
                        FROM pengajuan_akademik
                        WHERE id_mahasiswa = (SELECT id FROM Mahasiswa WHERE nim = ?)
                    );";
            $params_komentar = [$nim, $komentar];
            $stmt_komentar = sqlsrv_query($conn, $sql_komentar, $params_komentar);
            sqlsrv_commit($conn);
            if ($sql_komentar) {
                header("Location: tabel.php");
                exit();
            } else {
                header("Location: tabel.php");
                exit();
            }
            } else {
                echo 'Error: Komentar belum diisi';
            }
        } else {
            echo 'Error: '. print_r(sqlsrv_errors(), true);
        }
    } else {
        // Cek apakah komentar sudah diisi
            echo 'Error: Status belum diisi';
    }
} else {
    // Cek apakah data sudah diisi
    echo 'Error: Data belum diisi';
}

?>