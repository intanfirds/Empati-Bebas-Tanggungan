<?php
session_start();
include('../koneksi.php'); // Pastikan path koneksi sudah benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data yang dikirimkan
    $email = trim($_POST['email']);
    $no_telp = trim($_POST['no_telp']);
    $nim = $_SESSION['nim_mahasiswa'];

    // Tentukan direktori upload yang baru
    $uploadDir = 'C:/laragon/www/Empati-Bebas-Tanggungan/mahasiswa/uploads/';

    // Debugging: Cek apakah folder uploads sudah ada
    if (!is_dir($uploadDir)) {
        // Jika folder tidak ada, buat folder baru
        if (!mkdir($uploadDir, 0777, true)) {
            echo "<script>alert('Gagal membuat folder uploads.'); window.history.back();</script>";
            exit();
        }
    }

    $fotoPath = null;

    // Cek apakah file foto_profil ada dan file telah di-upload
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        // Cek tipe file gambar
        $fileType = mime_content_type($_FILES['foto_profil']['tmp_name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array($fileType, $allowedTypes)) {
            // Tentukan nama file unik untuk menghindari tabrakan nama
            $fileName = uniqid() . '-' . basename($_FILES['foto_profil']['name']);
            $uploadFile = $uploadDir . $fileName;

            // Pindahkan file ke folder tujuan
            if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $uploadFile)) {
                $fotoPath = 'uploads/' . $fileName; // Path relatif untuk disimpan di database
            } else {
                error_log("Failed to move uploaded file.");
                echo "<script>alert('Gagal mengunggah file!'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('Format file tidak didukung!'); window.history.back();</script>";
            exit();
        }
    } else {
        // Cek error pengunggahan file jika ada
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] !== UPLOAD_ERR_NO_FILE) {
            error_log("Upload error: " . $_FILES['foto_profil']['error']);
        }
    }

    // Query untuk memperbarui data mahasiswa
    $query = "UPDATE mahasiswa 
              SET email = ?, no_telp = ?, foto_profil = COALESCE(?, foto_profil) 
              WHERE nim = ?";
    $params = [$email, $no_telp, $fotoPath, $nim];
    $stmt = sqlsrv_prepare($conn, $query, $params);

    // Eksekusi query
    if (sqlsrv_execute($stmt)) {
        // Jika berhasil, update session dan redirect
        $_SESSION['email_mahasiswa'] = $email;
        $_SESSION['no_telp_mahasiswa'] = $no_telp;
        if ($fotoPath) {
            $_SESSION['foto_profil'] = $fotoPath; // Update foto profil di session
        }

        echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='profil.php';</script>";
    } else {
        error_log("SQL Error: " . print_r(sqlsrv_errors(), true)); // Debugging
        echo "<script>alert('Gagal memperbarui profil!'); window.history.back();</script>";
    }
} else {
    header("Location: profil.php");
    exit();
}
?>