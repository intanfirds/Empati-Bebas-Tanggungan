<?php
session_start();
include('../koneksi.php'); // Pastikan path sudah benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data yang dikirimkan
    $email = trim($_POST['email']);
    $no_telp = trim($_POST['no_telp']);
    $nim = $_SESSION['nim_mahasiswa'];

    // Tentukan direktori upload
    $uploadDir = __DIR__ . '/../uploads/';
    $fotoPath = null;

    // Cek apakah ada file foto yang diunggah
    if (isset($_FILES['foto_profil'])) {
        error_log("File upload status: " . print_r($_FILES['foto_profil'], true)); // Debugging
        if ($_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
            // Cek tipe file gambar
            $fileType = mime_content_type($_FILES['foto_profil']['tmp_name']);
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    
            if (in_array($fileType, $allowedTypes)) {
                // Tentukan nama file dan upload
                $uploadFile = $uploadDir . basename($_FILES['foto_profil']['name']);
                if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $uploadFile)) {
                    $fotoPath = 'uploads/' . basename($_FILES['foto_profil']['name']); // Path relatif
                    error_log("File uploaded successfully: " . $fotoPath); // Debugging
                } else {
                    error_log("Failed to move uploaded file."); // Debugging
                    echo "<script>alert('Gagal mengunggah file!'); window.history.back();</script>";
                    exit();
                }
            } else {
                error_log("Unsupported file type: " . $fileType); // Debugging
                echo "<script>alert('Format file tidak didukung!'); window.history.back();</script>";
                exit();
            }
        } else {
            error_log("File upload error: " . $_FILES['foto_profil']['error']); // Debugging
        }
    } else {
        error_log("No file uploaded."); // Debugging
    }

    // Query untuk memperbarui data mahasiswa
    error_log("Email: $email, No Telepon: $no_telp, Foto Path: $fotoPath, NIM: $nim"); // Debugging

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
        // Jika gagal, tampilkan error
        error_log("SQL Error: " . print_r(sqlsrv_errors(), true)); // Debugging
        echo "<script>alert('Gagal memperbarui profil!'); window.history.back();</script>";
    }
} else {
    // Jika bukan POST, redirect ke profil
    header("Location: profil.php");
    exit();
}
?>
