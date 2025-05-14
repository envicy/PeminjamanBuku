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

// Proses pengembalian buku
if (isset($_GET['kembali'])) {
    $ID_Peminjaman = mysqli_real_escape_string($koneksi, $_GET['kembali']);
    $tanggal_pengembalian = date('Y-m-d');
    
    // Update status peminjaman menjadi Dikembalikan
    $query = "UPDATE transaksi_peminjaman 
              SET Status_Peminjaman = 'Dikembalikan', 
                  tanggal_pengembalian = '$tanggal_pengembalian' 
              WHERE ID_Peminjaman = '$ID_Peminjaman'";
    
    if (mysqli_query($koneksi, $query)) {
        $success_message = "Buku berhasil dikembalikan!";
    } else {
        $error_message = "Error: " . mysqli_error($koneksi);
    }
}

// Mengambil data peminjaman aktif
$query_peminjaman = "SELECT tp.*, m.Nama_Mahasiswa, b.Judul 
                    FROM transaksi_peminjaman tp
                    JOIN mahasiswa m ON tp.NIM = m.NIM
                    JOIN buku b ON tp.ID_Buku = b.ID_Buku
                    WHERE tp.Status_Peminjaman != 'Dikembalikan'
                    ORDER BY tp.Tanggal_Pinjam DESC";
$result_peminjaman = mysqli_query($koneksi, $query_peminjaman);

// Mengambil data riwayat pengembalian
$query_riwayat = "SELECT tp.*, m.Nama_Mahasiswa, b.Judul 
                 FROM transaksi_peminjaman tp
                 JOIN mahasiswa m ON tp.NIM = m.NIM
                 JOIN buku b ON tp.ID_Buku = b.ID_Buku
                 WHERE tp.Status_Peminjaman = 'Dikembalikan'
                 ORDER BY tp.Tanggal_Kembali DESC
                 LIMIT 10";
$result_riwayat = mysqli_query($koneksi, $query_riwayat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku - Sistem Peminjaman Buku</title>
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
        main {
            padding-top: 48px;
        }
        .badge.bg-danger {
            font-size: 0.8rem;
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
                <a class="nav-link px-3" href="logout.php">Log Out</a>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
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
                            <a class="nav-link active" href="pengembalian.php">
                                <i class="fas fa-undo-alt me-2"></i>
                                Pengembalian
                            </a>
                        </li>
                        <?php if (isset($_SESSION['Jabatan']) && $_SESSION['Jabatan'] == 'Admin') : ?>
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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Pengembalian Buku</h1>
                </div>

                <?php if (isset($success_message)) : ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif (isset($error_message)) : ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Tabel Peminjaman Aktif -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Daftar Peminjaman Aktif</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID Peminjaman</th>
                                        <th>NIM</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>Judul Buku</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tanggal Kembali</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result_peminjaman) > 0) : ?>
                                        <?php while ($pinjam = mysqli_fetch_assoc($result_peminjaman)) : ?>
                                        <tr>
                                            <td><?php echo $pinjam['ID_Peminjaman']; ?></td>
                                            <td><?php echo $pinjam['NIM']; ?></td>
                                            <td><?php echo $pinjam['Nama_Mahasiswa']; ?></td>
                                            <td><?php echo $pinjam['Judul']; ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($pinjam['Tanggal_Pinjam'])); ?></td>
                                            <td>
                                                <?php echo date('d-m-Y', strtotime($pinjam['Tanggal_Kembali'])); ?>
                                                <?php
                                                $today = strtotime(date('Y-m-d'));
                                                $return_date = strtotime($pinjam['Tanggal_Kembali']);
                                                $diff = $today - $return_date;
                                                $days_late = floor($diff / (60 * 60 * 24));
                                                
                                                if ($days_late > 0) {
                                                    echo '<span class="badge bg-danger">Terlambat ' . $days_late . ' hari</span>';
                                                }
                                                ?>
                                            </td>
                                            <td><span class="badge bg-warning">Dipinjam</span></td>
                                            <td>
                                                <a href="?kembali=<?php echo $pinjam['ID_Peminjaman']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Konfirmasi pengembalian buku?');">
                                                    <i class="fas fa-check-circle me-1"></i>Kembalikan
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada peminjaman aktif saat ini.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tabel Riwayat Pengembalian -->
                <div class="card">
                    <div class="card-header">
                        <h5>Riwayat Pengembalian Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID Peminjaman</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>Judul Buku</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tanggal Kembali</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result_riwayat) > 0) : ?>
                                        <?php while ($riwayat = mysqli_fetch_assoc($result_riwayat)) : ?>
                                        <tr>
                                            <td><?php echo $riwayat['ID_Peminjaman']; ?></td>
                                            <td><?php echo $riwayat['Nama_Mahasiswa']; ?></td>
                                            <td><?php echo $riwayat['Judul']; ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($riwayat['Tanggal_Pinjam'])); ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($riwayat['Tanggal_Kembali'])); ?></td>
                                            <td><span class="badge bg-success">Dikembalikan</span></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada riwayat pengembalian.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>