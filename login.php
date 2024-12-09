<?php
session_start();
require_once 'koneksi.php'; // Menghubungkan ke database

class AdminLogin
{
    private $conn;

    public function __construct($dbConnection)
    {
        $this->conn = $dbConnection;
    }

    public function authenticate($username, $password)
    {
        $query = "SELECT nama, nip, role, password FROM admin WHERE username = ?";
        $params = array($username);
        $stmt = sqlsrv_prepare($this->conn, $query, $params);

        if (sqlsrv_execute($stmt)) {
            if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                if (password_verify($password, $row['password'])) {
                    $this->setSession($row);
                    $this->redirectUser($row['role']);
                } else {
                    $this->showAlertAndRedirect('Password salah!', 'index-admin.html');
                }
            } else {
                $this->showAlertAndRedirect('Username tidak ditemukan!', 'index-admin.html');
            }
        } else {
            throw new Exception('Query gagal dieksekusi!');
        }
    }

    private function setSession($userData)
    {
        $_SESSION['nama_admin'] = $userData['nama'];
        $_SESSION['nip_admin'] = $userData['nip'];
        $_SESSION['role'] = $userData['role'];
    }

    private function redirectUser($role)
    {
        switch ($role) {
            case 'Jurusan':
                header("Location: /Empati-Bebas-Tanggungan/jurusan/index.php");
                break;
            case 'Admin TI':
                header("Location: /Empati-Bebas-Tanggungan/bestang/bestang ti/index.php");
                break;
            case 'Admin SIB':
                header("Location: /Empati-Bebas-Tanggungan/bestang/bestang sib/index.php");
                break;
            case 'Akademik':
                header("Location: /Empati-Bebas-Tanggungan/akademik/index.php");
                break;
            case 'Perpustakaan':
                header("Location: /Empati-Bebas-Tanggungan/perpustakaan/index.php");
                break;
            default:
                $this->showAlertAndRedirect('Role tidak dikenali!', 'index-admin.html');
                break;
        }
        exit;
    }

    private function showAlertAndRedirect($message, $url)
    {
        echo "<script>alert('$message'); window.location.href='$url';</script>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $loginHandler = new AdminLogin($conn);
        $loginHandler->authenticate($username, $password);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
