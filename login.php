<?php
session_start();
require_once 'koneksi.php'; // Menghubungkan ke database

// Interface untuk autentikasi
interface AuthInterface
{
    public function authenticate($username, $password);
}

// Abstract class untuk pengguna
abstract class User
{
    protected $name;
    protected $nip;
    protected $role;

    public function __construct($name, $nip, $role)
    {
        $this->name = $name;
        $this->nip = $nip;
        $this->role = $role;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRole()
    {
        return $this->role;
    }

    abstract public function redirect();
}

// Class turunan untuk setiap role
class Jurusan extends User
{
    public function redirect()
    {
        header("Location: /Empati-Bebas-Tanggungan/jurusan/index.php");
        exit;
    }
}

class AdminTI extends User
{
    public function redirect()
    {
        header("Location: /Empati-Bebas-Tanggungan/bestang/bestang ti/index.php");
        exit;
    }
}

class AdminSIB extends User
{
    public function redirect()
    {
        header("Location: /Empati-Bebas-Tanggungan/bestang/bestang sib/index.php");
        exit;
    }
}

class Akademik extends User
{
    public function redirect()
    {
        header("Location: /Empati-Bebas-Tanggungan/akademik/index.php");
        exit;
    }
}

class Perpustakaan extends User
{
    public function redirect()
    {
        header("Location: /Empati-Bebas-Tanggungan/perpustakaan/index.php");
        exit;
    }
}

// Kelas utama untuk login
class AdminLogin implements AuthInterface
{
    private $conn;

    public function __construct($dbConnection)
    {
        $this->conn = $dbConnection;
    }

    public function authenticate($username, $password)
    {
        $query = "SELECT nama, nip, role, password FROM admin WHERE username = ?";
        $params = [$username];
        $stmt = sqlsrv_prepare($this->conn, $query, $params);

        if (sqlsrv_execute($stmt)) {
            if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                if (password_verify($password, $row['password'])) {
                    $user = $this->createUserObject($row);
                    $this->setSession($user);
                    $user->redirect();
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

    private function createUserObject($row)
    {
        switch ($row['role']) {
            case 'Jurusan':
                return new Jurusan($row['nama'], $row['nip'], $row['role']);
            case 'Admin TI':
                return new AdminTI($row['nama'], $row['nip'], $row['role']);
            case 'Admin SIB':
                return new AdminSIB($row['nama'], $row['nip'], $row['role']);
            case 'Akademik':
                return new Akademik($row['nama'], $row['nip'], $row['role']);
            case 'Perpustakaan':
                return new Perpustakaan($row['nama'], $row['nip'], $row['role']);
            default:
                $this->showAlertAndRedirect('Role tidak dikenali!', 'index-admin.html');
        }
    }

    private function setSession(User $user)
    {
        $_SESSION['nama_admin'] = $user->getName();
        $_SESSION['role'] = $user->getRole();
    }

    private function showAlertAndRedirect($message, $url)
    {
        echo "<script>alert('$message'); window.location.href='$url';</script>";
        exit;
    }
}

// Proses login
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
