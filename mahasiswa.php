<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'koneksi.php';

// Proses tambah mahasiswa baru
if (isset($_POST['tambah'])) {
    $NIM = mysqli_real_escape_string($koneksi, $_POST['NIM']);
    $Nama_Mahasiswa = mysqli_real_escape_string($koneksi, $_POST['Nama_Mahasiswa']);
    $Alamat_Mahasiswa = mysqli_real_escape_string($koneksi, $_POST['Alamat_Mahasiswa']);
    $No_Telepon_Mahasiswa = mysqli_real_escape_string($koneksi, $_POST['No_Telepon_Mahasiswa']);
    $Status_Mahasiswa = mysqli_real_escape_string($koneksi, $_POST['Status_Mahasiswa']);
    
    // cek apakah NIMn nya sudah ada dalam tabel
    $check_NIM_query = "SELECT * FROM Mahasiswa WHERE NIM = '$NIM'";
    $check_NIM_result = mysqli_query($koneksi, $check_NIM_query);
    
    if (mysqli_num_rows($check_NIM_result) > 0) {
        $error_message = "NIM sudah terdaftar, silakan gunakan NIM lain.";
    } else {
        $query = "INSERT INTO Mahasiswa (NIM, Nama_Mahasiswa, Alamat_Mahasiswa, No_Telepon_Mahasiswa, Status_Mahasiswa) 
                VALUES ('$NIM', '$Nama_Mahasiswa', '$Alamat_Mahasiswa', '$No_Telepon_Mahasiswa', '$Status_Mahasiswa')";
        
        if (mysqli_query($koneksi, $query)) {
            $success_message = "Data mahasiswa berhasil ditambahkan!";
        } else {
            $error_message = "Error: " . mysqli_error($koneksi);
        }
    }
}

// Proses hapus mahasiswa
if (isset($_GET['hapus'])) {
    $NIM = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    // Cek apakah mahasiswa sedang meminjam buku
    $check_peminjaman = "SELECT COUNT(*) as jumlah FROM transaksi_peminjaman 
                         WHERE NIM = '$NIM' AND Status_Peminjaman != 'Dikembalikan'";
    $result_peminjaman = mysqli_query($koneksi, $check_peminjaman);
    $row_peminjaman = mysqli_fetch_assoc($result_peminjaman);
    
    if ($row_peminjaman['jumlah'] > 0) {
        $error_message = "Mahasiswa ini masih memiliki peminjaman buku yang aktif. Tidak dapat dihapus!";
    } else {
        $query = "DELETE FROM Mahasiswa WHERE NIM = '$NIM'";
        
        if (mysqli_query($koneksi, $query)) {
            $success_message = "Data mahasiswa berhasil dihapus!";
        } else {
            $error_message = "Error: " . mysqli_error($koneksi);
        }
    }
}

// Proses edit mahasiswa
if (isset($_POST['edit'])) {
    $NIM = mysqli_real_escape_string($koneksi, $_POST['NIM']);
    $Nama_Mahasiswa = mysqli_real_escape_string($koneksi, $_POST['Nama_Mahasiswa']);
    $Alamat_Mahasiswa = mysqli_real_escape_string($koneksi, $_POST['Alamat_Mahasiswa']);
    $No_Telepon_Mahasiswa = mysqli_real_escape_string($koneksi, $_POST['No_Telepon_Mahasiswa']);
    $Status_Mahasiswa = mysqli_real_escape_string($koneksi, $_POST['Status_Mahasiswa']);
    
    $query = "UPDATE Mahasiswa SET 
              Nama_Mahasiswa='$Nama_Mahasiswa', 
              Alamat_Mahasiswa='$Alamat_Mahasiswa', 
              No_Telepon_Mahasiswa='$No_Telepon_Mahasiswa', 
              Status_Mahasiswa='$Status_Mahasiswa'
              WHERE NIM='$NIM'";
    
    if (mysqli_query($koneksi, $query)) {
        $success_message = "Data mahasiswa berhasil diupdate!";
    } else {
        $error_message = "Error: " . mysqli_error($koneksi);
    }
}

// Ambil data mahasiswa dan jumlah buku yang sedang dipinjam
$query = "
    SELECT m.NIM, m.Nama_Mahasiswa, m.Alamat_Mahasiswa, m.No_Telepon_Mahasiswa, m.Status_Mahasiswa,
          COUNT(t.ID_Buku) AS Jumlah_Dipinjam
    FROM Mahasiswa m
    LEFT JOIN transaksi_peminjaman t 
        ON m.NIM = t.NIM AND t.Status_Peminjaman != 'Dikembalikan'
    GROUP BY m.NIM, m.Nama_Mahasiswa, m.Alamat_Mahasiswa, m.No_Telepon_Mahasiswa, m.Status_Mahasiswa
    ORDER BY m.Nama_Mahasiswa
";
$result = mysqli_query($koneksi, $query);

// Inisialisasi variabel untuk menampilkan riwayat peminjaman
$riwayat_peminjaman = array();
$NIM_dicari = '';
$Nama_Mahasiswa = '';

// Proses pencarian riwayat peminjaman jika ada
if (isset($_POST['cari_riwayat'])) {
    $NIM_dicari = mysqli_real_escape_string($koneksi, $_POST['NIM']);
    
    // Cari nama mahasiswa
    $query_nama = "SELECT Nama_Mahasiswa FROM Mahasiswa WHERE NIM = '$NIM_dicari'";
    $result_nama = mysqli_query($koneksi, $query_nama);
    if (mysqli_num_rows($result_nama) > 0) {
        $row_nama = mysqli_fetch_assoc($result_nama);
        $Nama_Mahasiswa = $row_nama['Nama_Mahasiswa'];
        
        // Panggil stored procedure untuk mendapatkan riwayat peminjaman
        $query_riwayat = "CALL riwayatpeminjamanmahasiswa('$NIM_dicari')";
        $result_riwayat = mysqli_query($koneksi, $query_riwayat);
        
        if ($result_riwayat) {
            while ($row = mysqli_fetch_assoc($result_riwayat)) {
                $riwayat_peminjaman[] = $row;
            }
            // Tutup result set agar bisa melakukan query lain
            mysqli_free_result($result_riwayat);
            
            // Setelah memanggil stored procedure, kita perlu membuat koneksi baru
            // tetapi jangan tutup koneksi yang lama terlebih dahulu
            $koneksi = mysqli_connect("localhost", "root", "Hikari1223", "DBPeminjamanBuku");
            
            // Periksa koneksi
            if (!$koneksi) {
                die("Koneksi database gagal: " . mysqli_connect_error());
            }
            
            // Ambil kembali data mahasiswa untuk tabel utama
            $result = mysqli_query($koneksi, $query);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa - Sistem Peminjaman Buku</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        main {
            padding-top: 48px;
        }
        .table-riwayat {
            margin-top: 20px;
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
                                <i class="fas fa-home me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="buku.php">
                                <i class="fas fa-book me-2"></i> Data Buku
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="mahasiswa.php">
                                <i class="fas fa-user-graduate me-2"></i> Data Mahasiswa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="peminjaman.php">
                                <i class="fas fa-clipboard-list me-2"></i> Peminjaman
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pengembalian.php">
                                <i class="fas fa-undo-alt me-2"></i> Pengembalian
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
                    <h1 class="h2">Data Mahasiswa</h1>
                </div>

                <?php if (isset($success_message)) : ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif (isset($error_message)) : ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Form Tambah/Edit Mahasiswa -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>
                            <?php echo isset($_GET['edit_NIM']) ? 'Edit Data Mahasiswa' : 'Tambah Mahasiswa Baru'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="row g-3">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text" title="Format: Axxxxxxxxxx">NIM</span>
                                    <input type="text" name="NIM" class="form-control" placeholder="NIM Mahasiswa" required 
                                        value="<?php echo isset($_GET['edit_NIM']) ? $_GET['edit_NIM'] : ''; ?>" 
                                        <?php echo isset($_GET['edit_NIM']) ? 'readonly' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="Nama_Mahasiswa" class="form-control" placeholder="Nama Mahasiswa" required value="<?php echo $_GET['nama'] ?? ''; ?>">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="No_Telepon_Mahasiswa" class="form-control" placeholder="No. Telepon" required value="<?php echo $_GET['telepon'] ?? ''; ?>">
                            </div>
                            <div class="col-md-2">
                                <select name="Status_Mahasiswa" class="form-select" required>
                                    <option value="">-- Status --</option>
                                    <option value="Aktif" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="Cuti" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Cuti') ? 'selected' : ''; ?>>Cuti</option>
                                    <option value="Alumni" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Alumni') ? 'selected' : ''; ?>>Alumni</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" name="<?php echo isset($_GET['edit_NIM']) ? 'edit' : 'tambah'; ?>" class="btn btn-<?php echo isset($_GET['edit_NIM']) ? 'warning' : 'primary'; ?> w-100">
                                    <i class="fas <?php echo isset($_GET['edit_NIM']) ? 'fa-edit' : 'fa-save'; ?> me-2"></i>
                                    <?php echo isset($_GET['edit_NIM']) ? 'Update' : 'Simpan'; ?>
                                </button>
                            </div>
                            <div class="col-md-12">
                                <input type="text" name="Alamat_Mahasiswa" class="form-control" placeholder="Alamat Mahasiswa" required value="<?php echo $_GET['alamat'] ?? ''; ?>">
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Form Pencarian Riwayat Peminjaman -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-search me-2"></i>Cari Riwayat Peminjaman Mahasiswa</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="row g-3">
                            <div class="col-md-6">
                                <label for="NIM" class="form-label">NIM Mahasiswa</label>
                                <input type="text" name="NIM" id="NIM" class="form-control" required placeholder="Masukkan NIM Mahasiswa" value="<?php echo $NIM_dicari; ?>">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" name="cari_riwayat" class="btn btn-primary text-white">
                                    <i class="fas fa-history me-2"></i>Cari Riwayat
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tampilkan Riwayat Peminjaman jika ada -->
                <?php if (!empty($riwayat_peminjaman)) : ?>
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Peminjaman: <?php echo $Nama_Mahasiswa; ?> (<?php echo $NIM_dicari; ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-riwayat">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nama Mahasiswa</th>
                                        <th>Judul Buku</th>
                                        <th>Tanggal Peminjaman</th>
                                        <th>Tanggal Pengembalian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($riwayat_peminjaman as $riwayat) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($riwayat['Nama Mahasiswa']); ?></td>
                                        <td><?php echo htmlspecialchars($riwayat['Judul Buku']); ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($riwayat['Tanggal Peminjaman'])); ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($riwayat['Tanggal Pengembalian'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Tabel Data Mahasiswa -->
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Daftar Mahasiswa</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        <th>No. Telepon</th>
                                        <th>Status</th>
                                        <th>Jumlah Buku Dipinjam</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) : 
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['NIM']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Nama_Mahasiswa']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Alamat_Mahasiswa']); ?></td>
                                            <td><?php echo htmlspecialchars($row['No_Telepon_Mahasiswa']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $row['Status_Mahasiswa'] == 'Aktif' ? 'success' : 
                                                        ($row['Status_Mahasiswa'] == 'Cuti' ? 'warning' : 'secondary'); 
                                                ?>">
                                                    <?php echo htmlspecialchars($row['Status_Mahasiswa']); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($row['Jumlah_Dipinjam'] > 0): ?>
                                                    <span class="badge bg-primary"><?php echo htmlspecialchars($row['Jumlah_Dipinjam']); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="?edit_NIM=<?php echo $row['NIM']; ?>&nama=<?php echo urlencode($row['Nama_Mahasiswa']); ?>&alamat=<?php echo urlencode($row['Alamat_Mahasiswa']); ?>&telepon=<?php echo urlencode($row['No_Telepon_Mahasiswa']); ?>&status=<?php echo urlencode($row['Status_Mahasiswa']); ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if ($row['Jumlah_Dipinjam'] == 0): ?>
                                                    <a href="?hapus=<?php echo $row['NIM']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data mahasiswa ini?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="NIM" value="<?php echo $row['NIM']; ?>">
                                                        <button type="submit" name="cari_riwayat" class="btn btn-sm btn-info text-white">
                                                            <i class="fas fa-history"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php 
                                        endwhile; 
                                    } else {
                                    ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data mahasiswa.</td>
                                        </tr>
                                    <?php } ?>
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