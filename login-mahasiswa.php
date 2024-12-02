<?php
session_start(); // Memulai session

// Koneksi ke SQL Server
include('koneksi.php'); // Atau sesuaikan dengan file koneksi Anda

// Ambil data dari form login
$username = $_POST['username']; // Misalnya NIM atau username
$password = $_POST['password']; // Kata sandi

// Query untuk mengambil nama mahasiswa berdasarkan username
$query = "SELECT m.*, a.angkatan
        FROM mahasiswa m
        INNER JOIN angkatan a ON m.id_angkatan = a.id
        WHERE m.username = ? AND m.password = ?";



// Menyiapkan statement
$params = array($username, $password);
$stmt = sqlsrv_prepare($conn, $query, $params);

// Menjalankan query
if (sqlsrv_execute($stmt)) {
    // Jika data ditemukan
    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Simpan nama mahasiswa di session
        $_SESSION['nama_mahasiswa'] = $row['nama'];
        $_SESSION['nim_mahasiswa'] = $row['nim'];
        $_SESSION['ipk_mahasiswa'] = isset($row['ipk']) ? floatval($row['ipk']) : null;
        $_SESSION['jurusan_mahasiswa'] = $row['jurusan'];
        $_SESSION['prodi_mahasiswa'] = $row['prodi'];
        $_SESSION['email_mahasiswa'] = $row['email'];
        $_SESSION['no_telp_mahasiswa'] = $row['no_telp'];
        $_SESSION['angkatan_mahasiswa'] = $row['angkatan'];
        


        // Redirect ke halaman dashboard atau halaman lain setelah login sukses
        header("Location: /Empati-Bebas-Tanggungan/mahasiswa/index.php");
        exit();
    } else {
        // Jika login gagal
        echo "<script>alert('NIM atau Password salah!'); window.location.href='index-mahasiswa.html';</script>";
        exit;
    }
} else {
    echo "Query gagal dieksekusi!";
}
?>
