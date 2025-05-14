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

// Fungsi untuk mendapatkan ID peminjaman baru
function getNewBorrowId($koneksi) {
    $query = "SELECT ID_Peminjaman FROM transaksi_peminjaman";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    $next_id = intval($row['ID_Peminjaman']) + 1;
    return 'PM' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
}

// Proses form peminjaman
if (isset($_POST['pinjam'])) {
    $ID_Peminjaman = getNewBorrowId($koneksi);
    $NIM = mysqli_real_escape_string($koneksi, $_POST['NIM']);
    $ID_Buku = mysqli_real_escape_string($koneksi, $_POST['ID_Buku']);
    $Tanggal_Pinjam = date('Y-m-d');
    $Tanggal_Kembali = date('Y-m-d', strtotime('+7 days')); // Peminjaman 7 hari
    
    // Cek apakah buku sedang dipinjam
    $query_check = "SELECT * FROM transaksi_peminjaman 
                   WHERE ID_Buku = '$ID_Buku' AND Status_Peminjaman != 'Dikembalikan'";
    $result_check = mysqli_query($koneksi, $query_check);
    
    if (mysqli_num_rows($result_check) > 0) {
        $error_message = "Buku ini sedang dipinjam oleh mahasiswa lain!";
    } else {
        // Cek jumlah buku yang sedang dipinjam oleh mahasiswa
        $query_count = "SELECT COUNT(*) as jumlah FROM transaksi_peminjaman 
                       WHERE NIM = '$NIM' AND Status_Peminjaman != 'Dikembalikan'";
        $result_count = mysqli_query($koneksi, $query_count);
        $row_count = mysqli_fetch_assoc($result_count);
        
        if ($row_count['jumlah'] >= 3) {
            $error_message = "Mahasiswa ini sudah meminjam 3 buku. Tidak dapat meminjam lagi!";
        } else {
            $query = "INSERT INTO transaksi_peminjaman (ID_Peminjaman, NIM, ID_Buku, Tanggal_Pinjam, Tanggal_Kembali, Status_Peminjaman) 
                     VALUES ('$ID_Peminjaman', '$NIM', '$ID_Buku', '$Tanggal_Pinjam', '$Tanggal_Kembali', 'Dipinjam')";
            
            if (mysqli_query($koneksi, $query)) {
                $success_message = "Peminjaman buku berhasil dicatat!";
            } else {
                $error_message = "Error: " . mysqli_error($koneksi);
            }
        }
    }
}

// Mengambil data buku yang tersedia (tidak sedang dipinjam)
$query_buku = "SELECT b.* FROM buku b 
              WHERE b.ID_Buku NOT IN (
                  SELECT tp.ID_Buku FROM transaksi_peminjaman tp 
                  WHERE tp.Status_Peminjaman != 'Dikembalikan'
              )
              ORDER BY b.Judul";
$result_buku = mysqli_query($koneksi, $query_buku);

// Mengambil data mahasiswa
$query_mahasiswa = "SELECT * FROM mahasiswa ORDER BY Nama_Mahasiswa";
$result_mahasiswa = mysqli_query($koneksi, $query_mahasiswa);

// Mengambil data peminjaman aktif
$query_peminjaman = "SELECT tp.*, m.Nama_Mahasiswa, b.Judul 
                    FROM transaksi_peminjaman tp
                    JOIN mahasiswa m ON tp.NIM = m.NIM
                    JOIN buku b ON tp.ID_Buku = b.ID_Buku
                    WHERE tp.Status_Peminjaman != 'Dikembalikan'
                    ORDER BY tp.Tanggal_Pinjam DESC";
$result_peminjaman = mysqli_query($koneksi, $query_peminjaman);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Buku - Sistem Peminjaman Buku</title>
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
                            <a class="nav-link active" href="peminjaman.php">
                                <i class="fas fa-clipboard-list me-2"></i>
                                Peminjaman
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pengembalian.php">
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
                    <h1 class="h2">Peminjaman Buku</h1>
                </div>

                <?php if (isset($success_message)) : ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif (isset($error_message)) : ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Form Peminjaman Buku -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Form Peminjaman Buku</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="row g-3">
                            <div class="col-md-6">
                                <label for="NIM" class="form-label">Mahasiswa</label>
                                <select name="NIM" id="NIM" class="form-select" required>
                                    <option value="">-- Pilih Mahasiswa --</option>
                                    <?php while ($mhs = mysqli_fetch_assoc($result_mahasiswa)) : ?>
                                        <option value="<?php echo $mhs['NIM']; ?>">
                                            <?php echo $mhs['NIM'] . ' - ' . $mhs['Nama_Mahasiswa']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="ID_Buku" class="form-label">Buku</label>
                                <select name="ID_Buku" id="ID_Buku" class="form-select" required>
                                    <option value="">-- Pilih Buku --</option>
                                    <?php while ($buku = mysqli_fetch_assoc($result_buku)) : ?>
                                        <option value="<?php echo $buku['ID_Buku']; ?>">
                                            <?php echo $buku['ID_Buku'] . ' - ' . $buku['Judul']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="pinjam" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-2"></i>Proses Peminjaman
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabel Peminjaman Aktif -->
                <div class="card">
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
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($pinjam = mysqli_fetch_assoc($result_peminjaman)) : ?>
                                    <tr>
                                        <td><?php echo $pinjam['ID_Peminjaman']; ?></td>
                                        <td><?php echo $pinjam['NIM']; ?></td>
                                        <td><?php echo $pinjam['Nama_Mahasiswa']; ?></td>
                                        <td><?php echo $pinjam['Judul']; ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($pinjam['Tanggal_Pinjam'])); ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($pinjam['Tanggal_Kembali'])); ?></td>
                                        <td>
                                            <?php
                                            $today = strtotime(date('Y-m-d'));
                                            $return_date = strtotime($pinjam['Tanggal_Kembali']);
                                            if ($today > $return_date) {
                                                echo '<span class="badge bg-danger">Terlambat</span>';
                                            } else {
                                                echo '<span class="badge bg-warning">Dipinjam</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
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