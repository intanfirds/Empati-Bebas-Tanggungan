<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role'])) {
    die("Anda tidak memiliki akses.");
}

$role = $_SESSION['role']; // Ambil role dari session (sesuaikan dengan session yang digunakan)
$sql = "SELECT nama, nip FROM Admin WHERE role = ?";
$params = [$role];
$stmt = sqlsrv_query($conn, $sql, $params);

// Periksa apakah query berhasil
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Ambil data admin
$data_admin = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// Jika data tidak ditemukan
if ($data_admin === null) {
    die("Data admin tidak ditemukan.");
}

// Simpan data admin ke dalam session (jika diperlukan)
$_SESSION['nama_admin'] = $data_admin['nama'];
$_SESSION['nip_admin'] = $data_admin['nip'];
?>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SiBeta - Perpustakaan</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const table = $('#dataTable').DataTable({
            pageLength: 25 // Mengatur panjang halaman default
        });

        document.getElementById('filterProdi').addEventListener('change', filterTable);
        document.getElementById('filterAngkatan').addEventListener('change', filterTable);
        document.getElementById('filterStatus').addEventListener('change', filterTable);

        function filterTable() {
            const prodiFilter = document.getElementById('filterProdi').value.toLowerCase();
            const angkatanFilter = document.getElementById('filterAngkatan').value;
            const statusFilter = document.getElementById('filterStatus').value.toLowerCase();

            // Menggunakan DataTables API untuk filter
            table.column(2).search(prodiFilter, true, false); // Prodi
            table.column(3).search(angkatanFilter, true, false); // Angkatan
            table.column(4).search(statusFilter, true, false); // Status

            table.draw(); // Memperbarui tabel
        }
    });
    </script>
    <script>
    $(document).ready(function() {
        $('#dataTable').DataTable(); // Inisialisasi DataTable
    });
    $(document).ready(function() {
    var table = $('#dataTable').DataTable({
        // Opsi tambahan jika diperlukan
        // Misalnya, jika Anda ingin mengatur panjang halaman default
        pageLength: 25
        });
    });
    </script>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a
                class="sidebar-brand d-flex align-items-center justify-content-center"
                href="index.php"
            >
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SiBeTa polinema</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Beranda -->
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="fas fa-home"></i>
            <span>Beranda</span></a
          >
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider my-0" />

        <!-- Nav Item - Pages Mahasiswa -->
        <li class="nav-item">
          <a
            class="nav-link collapsed"
            href="tabel.php"
          >
            <i class="fas fa-fw fa-folder"></i>
            <span>
              Mahasiswa
            </span>
          </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <button class="btn" onclick="window.history.back();">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>

                    <!-- Sidebar Toggle (Topbar) -->
                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo htmlspecialchars($_SESSION['nama_admin']); ?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Data Mahasiswa</h6>
                    </div>
                    <div class="card-body">
                    <div class="mb-3">
                        <div class="row">
                            <!-- Filter Prodi -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filterProdi" class="form-label">Filter Prodi</label>
                                    <select id="filterProdi" class="form-control">
                                        <option value="">Semua Prodi</option>
                                        <option value="Teknik Informatika">Teknik Informatika</option>
                                        <option value="Sistem Informasi Bisnis">Sistem Informasi Bisnis</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Filter Angkatan -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filterAngkatan" class="form-label">Filter Angkatan</label>
                                    <select id="filterAngkatan" class="form-control">
                                        <option value="">Semua Angkatan</option>
                                        <?php
                                        for ($year = date('Y'); $year >= 2000; $year--) {
                                            echo "<option value='$year'>$year</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Filter Status -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filterStatus" class="form-label">Filter Status</label>
                                    <select id="filterStatus" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option value="Selesai">Selesai</option>
                                        <option value="Tidak Sesuai">Tidak Sesuai</option>
                                        <option value="Menunggu">Menunggu</option>
                                        <option value="Belum Mengisi">Belum Mengisi</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="table-responsive">
                            <table class="table table-bordered data-table" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        <th>Prodi</th>
                                        <th>Angkatan</th>
                                        <th>Status</th>
                                        <th>Berkas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include 'koneksi.php';

                                    $query = "SELECT m.nim, m.nama, m.prodi,m.jurusan, a.angkatan,
                                    CASE
                                         WHEN k.status = 'sesuai' THEN 'selesai'
                                         WHEN k.status = 'tidak selesai' THEN 'tidak selesai'
                                         WHEN k.status = 'menunggu' THEN 'menunggu'
                                         ELSE 'belum mengisi'
                                            END AS status
                                    FROM Mahasiswa m 
                                    LEFT JOIN pengajuan_perpustakaan p ON m.id = p.id_mahasiswa
                                    LEFT JOIN konfirmasi_perpus k ON p.id = k.id_pengajuan
                                    LEFT JOIN Angkatan a ON m.id_angkatan = a.id
                                    ORDER BY m.nim ASC";
                          
                                    $stmt = sqlsrv_query($conn, $query, $params);

                                    while ($data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($data['nim']) . "</td>";
                                        echo "<td>" . htmlspecialchars($data['nama']) . "</td>";
                                        echo "<td>" . htmlspecialchars($data['prodi']) . "</td>";
                                        echo "<td>" . htmlspecialchars($data['angkatan']) . "</td>";
                                        echo "<td>" . htmlspecialchars($data['status']?? 'Belum Mengisi') . "</td>";
                                        echo "<td>";

                                        // Ubah kondisi tombol pratinjau
                                        $status = strtolower(trim($data['status']?? 'Belum Mengisi')); // Normalisasi data status
                                        if (in_array($status, ['selesai', 'tidak sesuai', 'menunggu'])) {
                                            echo '<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#dataMahasiswaModal" 
                                                data-nama="' . htmlspecialchars($data['nama']) . '" 
                                                data-nim="' . htmlspecialchars($data['nim']) . '" 
                                                data-jurusan="' . htmlspecialchars($data['jurusan']) . '" 
                                                data-prodi="' . htmlspecialchars($data['prodi']) . '"
                                                data-status-pengecekan="' . htmlspecialchars($data['status']) . '">Pratinjau</button>';
                                        } else {
                                            echo "-"; // Tampilkan tanda kosong untuk status lainnya
                                        }

                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <!-- Modal HTML -->
                            <div class="modal fade" id="dataMahasiswaModal" tabindex="-1" aria-labelledby="dataMahasiswaModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-md"> <!-- Mengubah ukuran modal menjadi medium -->
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="dataMahasiswaModalLabel">Bebas Tanggungan Perpustakaan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Card untuk Data Mahasiswa -->
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    <h6>Data Mahasiswa</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div id="modalContent">
                                                        <!-- Konten modal akan diisi di sini -->
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Card untuk Konfirmasi dan Komentar -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6>Status Konfirmasi</h6>
                                                </div>
                                                <div class="card-body">
                                                    <form id="konfirmasiForm" method="post" action="proses_konfirmasi.php?nim=<?php echo htmlspecialchars($data['nim']); ?>">
                                                        <div class="mb-3">
                                                            <div class="form-check">
                                                                <input type="radio" id="tidakAdaTanggungan" name="status" value="sesuai" class="from-check-input">
                                                                <label for="tidakAdaTanggungan" class="form-check-label">Tidak ada buku yang dikembalikan</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio" id="adaTanggungan" name="status" value="tidak sesuai" class="from-check-input">
                                                                <label for="adaTanggungan" class="form-check-label">Ada buku yang belum dikembalikan</label>
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="komentar" class="form-label">Komentar</label>
                                                            <textarea class="form-control" id="komentar" name="komentar" rows="2"></textarea> <!-- Mengurangi tinggi textarea -->
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <i class="fas fa-times"></i> Tutup
                                                            <button type="submit" class="btn btn-primary" form="konfirmasiForm" id="simpanKonfirmasi">
                                                                <i class="fas fa-save"></i> Kirim Konfirmasi
                                                        </div>
                                                    </form>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white " >
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; SiBeTa - Sistem Bebas Tanggungan 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const dataMahasiswaModal = document.getElementById('dataMahasiswaModal');
        dataMahasiswaModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Tombol yang diklik
            const nama = button.getAttribute('data-nama');
            const nim = button.getAttribute('data-nim');
            const jurusan = button.getAttribute('data-jurusan');
            const prodi = button.getAttribute('data-prodi');
            const status = button.getAttribute('data-status-pengecekan');

            // Isi konten modal
            const modalContent = document.getElementById('modalContent');
            modalContent.innerHTML = `
                <table class="table table-borderless">
                    <tbody>
                    <tr>
                        <td><strong>Nama</strong></td>
                        <td>${nama}</td>
                    </tr>
                    <tr>
                        <td><strong>NIM</strong></td>
                        <td>${nim}</td>
                    </tr>
                    <tr>
                        <td><strong>Jurusan</strong></td>
                        <td>${jurusan}</td>
                    </tr>
                    <tr>
                        <td><strong>Prodi</strong></td>
                        <td>${prodi}</td>
                    </tr>
                    </tbody>
                </table>
            `;

            // Set radio button sesuai status
            document.getElementById('tidakAdaTanggungan').checked = status === 'sesuai';
            document.getElementById('adaTanggungan').checked = status === 'tidak sesuai';

            // Menangani pengiriman formulir dengan AJAX
            const konfirmasiForm = document.getElementById('konfirmasiForm');
            konfirmasiForm.onsubmit = function (event) {
                event.preventDefault(); // Mencegah pengiriman formulir secara default

                const status = document.querySelector('input[name="status"]:checked').value;
                const komentar = document.getElementById('komentar').value.trim();

                // Kirim data ke server menggunakan fetch
                fetch('proses_konfirmasi.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                    },
                    body: new URLSearchParams({
                        nim: nim,
                        status: status,
                        komentar: komentar
                    })
                })
                .then(response => response.text())
                .then(data => {
                    // Tampilkan pesan sukses
                    alert(data); // Anda bisa mengganti ini dengan modal atau elemen lain untuk menampilkan pesan
                    $('#dataMahasiswaModal').modal('hide'); // Menutup modal setelah pengiriman
                })
                .catch(error => console.error('Error:', error));
            };
        });
        function fetchData() {
            $.ajax({
                url: 'proses_konfirmasi.php', // Create this PHP file to return updated data
                method: 'GET',
                success: function(data) {
                    $('#data-table').html(data);
                }
            });
        }

        setInterval(fetchData, 1000); // Refresh data every 5 seconds
    </script>



    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

</body>

</html>