<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role'])) {
    die("Anda tidak memiliki akses.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nip_admin = $_SESSION['nip_admin'];
    $old_password = trim($_POST['old_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi password baru
    if ($new_password !== $confirm_password) {
        $error = "Password baru tidak cocok.";
    } else {
        // Cek password lama di database
        $sql = "SELECT password FROM [dbo].[Admin] WHERE nip = ?";
        $params = [$nip_admin];
        $stmt = sqlsrv_query($conn, $sql, $params);
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        if ($data) {
            // Debugging sementara
            // echo "Password dari database: " . htmlspecialchars($data['password']) . "<br>";
            // echo "Password lama yang dimasukkan: " . htmlspecialchars($old_password) . "<br>";
            echo "Password dari database (asli): " . htmlspecialchars($data['password']) . "<br>";

            // Verifikasi password lama
            if ($old_password === $data['password']) {
                // Hash dan simpan password baru
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE [dbo].[Admin] SET password = ? WHERE nip = ?";
                $update_params = [$hashed_password, $nip_admin];
                $update_stmt = sqlsrv_query($conn, $update_sql, $update_params);
        
                if ($update_stmt) {
                    $success = "Password berhasil diubah.";
                } else {
                    $error = "Gagal mengubah password: " . print_r(sqlsrv_errors(), true);
                }
            } else {
                $error = "Password lama salah.";
            }
        } else {
            $error = "Data admin tidak ditemukan.";
        }
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Password</title>
    <link href="sb-admin-2.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <h2>Edit Password</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="old_password">Password Lama</label>
                <input type="password" class="form-control" name="old_password" required />
            </div>
            <div class="form-group">
                <label for="new_password">Password Baru</label>
                <input type="password" class="form-control" name="new_password" required />
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" name="confirm_password" required />
            </div>
            <button type="submit" class="btn btn-primary">Konfirmasi</button>
        </form>
    </div>
</body>
</html>