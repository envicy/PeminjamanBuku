<?php
// Mulai session
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Include file koneksi
require_once 'koneksi.php';

// Mengambil data untuk dashboard
$query_total_buku = "SELECT COUNT(*) as total FROM buku";
$result_total_buku = mysqli_query($koneksi, $query_total_buku);
$row_total_buku = mysqli_fetch_assoc($result_total_buku);
$total_buku = $row_total_buku['total'];

$query_total_mahasiswa = "SELECT COUNT(*) as total FROM mahasiswa";
$result_total_mahasiswa = mysqli_query($koneksi, $query_total_mahasiswa);
$row_total_mahasiswa = mysqli_fetch_assoc($result_total_mahasiswa);
$total_mahasiswa = $row_total_mahasiswa['total'];

$query_total_peminjaman = "SELECT COUNT(*) as total FROM transaksi_peminjaman";
$result_total_peminjaman = mysqli_query($koneksi, $query_total_peminjaman);
$row_total_peminjaman = mysqli_fetch_assoc($result_total_peminjaman);
$total_peminjaman = $row_total_peminjaman['total'];

// Mengambil data peminjaman terbaru
$query_peminjaman_terbaru = "SELECT tp.ID_Peminjaman, tp.Tanggal_Pinjam, tp.Tanggal_Kembali, tp.Status_Peminjaman, 
                             m.Nama_Mahasiswa, b.Judul 
                             FROM transaksi_peminjaman tp
                             JOIN mahasiswa m ON tp.NIM = m.NIM
                             JOIN buku b ON tp.ID_Buku = b.ID_Buku
                             ORDER BY tp.Tanggal_Pinjam DESC
                             LIMIT 5";
$result_peminjaman_terbaru = mysqli_query($koneksi, $query_peminjaman_terbaru);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Peminjaman Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background-color: #343a40;
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .nav-link {
            font-weight: 500;
            color: #ccc;
        }
        .nav-link.active {
            color: #fff;
        }
        .nav-link:hover {
            color: #fff;
        }
        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
            font-size: 1rem;
            background-color: rgba(0, 0, 0, .25);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
        }
        .navbar .navbar-toggler {
            top: .25rem;
            right: 1rem;
        }
        .card-dashboard {
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-dashboard .card-body {
            padding: 1.25rem;
        }
        .card-dashboard .card-title {
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        .card-dashboard .icon {
            float: right;
            font-size: 2rem;
            color: #3498db;
        }
        .card-dashboard .count {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        main {
            padding-top: 48px;
        }
        .welcome-message {
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Perpustakaan Digital</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <a class="nav-link px-3" href="logout.php">Sign out</a>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-home me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="buku.php">
                                <i class="fas fa-book me-2"></i>
                                Data Buku
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="mahasiswa.php">
                                <i class="fas fa-user-graduate me-2"></i>
                                Data Mahasiswa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="peminjaman.php">
                                <i class="fas fa-clipboard-list me-2"></i>
                                Peminjaman
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pengembalian.php">
                                <i class="fas fa-clipboard-check me-2"></i>
                                Pengembalian
                            </a>
                        </li>
                        <?php if ($_SESSION['jabatan'] == 'Admin') : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="penerbit.php">
                                <i class="fas fa-building me-2"></i>
                                Data Penerbit
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="staff.php">
                                <i class="fas fa-users me-2"></i>
                                Data Staff
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="welcome-message pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <p>Selamat datang, <?php echo $_SESSION['nama']; ?> (<?php echo $_SESSION['jabatan']; ?>)</p>
                </div>

                <div class="row">
                    <div class="col-xl-4 col-md-6">
                        <div class="card card-dashboard bg-light">
                            <div class="card-body">
                                <div class="icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <h5 class="card-title">Total Buku</h5>
                                <p class="count"><?php echo $total_buku; ?></p>
                                <p class="card-text">Jumlah buku yang tersedia di perpustakaan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card card-dashboard bg-light">
                            <div class="card-body">
                                <div class="icon">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <h5 class="card-title">Total Mahasiswa</h5>
                                <p class="count"><?php echo $total_mahasiswa; ?></p>
                                <p class="card-text">Jumlah mahasiswa yang terdaftar</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card card-dashboard bg-light">
                            <div class="card-body">
                                <div class="icon">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <h5 class="card-title">Total Peminjaman</h5>
                                <p class="count"><?php echo $total_peminjaman; ?></p>
                                <p class="card-text">Jumlah transaksi peminjaman yang tercatat</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Peminjaman Terbaru</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nama Mahasiswa</th>
                                                <th>Judul Buku</th>
                                                <th>Tanggal Pinjam</th>
                                                <th>Tanggal Kembali</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($result_peminjaman_terbaru)) : ?>
                                            <tr>
                                                <td><?php echo $row['ID_Peminjaman']; ?></td>
                                                <td><?php echo $row['Nama_Mahasiswa']; ?></td>
                                                <td><?php echo $row['Judul']; ?></td>
                                                <td><?php echo date('d-m-Y', strtotime($row['Tanggal_Pinjam'])); ?></td>
                                                <td><?php echo date('d-m-Y', strtotime($row['Tanggal_Kembali'])); ?></td>
                                                <td>
                                                    <?php if ($row['Status_Peminjaman'] == 'Dikembalikan') : ?>
                                                        <span class="badge bg-success">Dikembalikan</span>
                                                    <?php else : ?>
                                                        <span class="badge bg-warning">Dipinjam</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>