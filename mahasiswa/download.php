<?php
session_start();
require '../vendor/autoload.php'; // Autoload PHPWord dan dependencies lainnya
include '../koneksi.php';

// Pastikan pengguna memiliki akses
if (!isset($_SESSION['id_mahasiswa'])) {
    die('Akses ditolak.');
}

// ID Mahasiswa dari sesi
$idMahasiswa = $_SESSION['id_mahasiswa'];

// Fungsi untuk mendapatkan data mahasiswa
function getDataMahasiswa($conn, $query, $params) {
    $stmt = sqlsrv_query($conn, $query, $params);
    return sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
}

// Mendapatkan data mahasiswa dari tabel
$identitasMahasiswa = getDataMahasiswa($conn, "
    SELECT nama, nim, prodi
    FROM mahasiswa
    WHERE id = ?
", array($idMahasiswa));

// Mengecek apakah data mahasiswa ditemukan
if (empty($identitasMahasiswa)) {
    die('Data mahasiswa tidak ditemukan.');
}

$statusValid = true; // Validasi, sesuaikan dengan kebutuhan Anda

if ($statusValid) {
    // Path template
    $templatePath = realpath('../form/form-bestang.docx');
    if (!$templatePath) {
        die('Template tidak ditemukan di: ../form/form-bestang.docx');
    }

    // Membuat template processor
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

    // Pastikan file template ada
    if (!file_exists($templatePath)) {
        die('File template tidak ditemukan. Pastikan jalur file benar.');
    }
    
    // Mengisi template dengan data mahasiswa
    $templateProcessor->setValue('nama', $identitasMahasiswa['nama']);
    $templateProcessor->setValue('nim', $identitasMahasiswa['nim']);
    $templateProcessor->setValue('prodi', $identitasMahasiswa['prodi']);
    
    // Menyimpan dokumen yang telah diisi dengan nama file berdasarkan nama mahasiswa
    $outputPath = '../output/' . strtolower(str_replace(' ', '_', $identitasMahasiswa['nama'])) . '-form-bestang.docx';
    $templateProcessor->saveAs($outputPath);

    // Memastikan file hasil telah dibuat
    if (!file_exists($outputPath)) {
        die('File hasil tidak berhasil dibuat.');
    }
    
    // Kirim header untuk pengunduhan dengan nama file yang baru
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($outputPath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($outputPath));
    flush(); // Bersihkan buffer output sistem
    readfile($outputPath);
    exit;
} else {
    echo 'Dokumen tidak lengkap. Anda tidak dapat mengunduh file ini.';
}
?>
