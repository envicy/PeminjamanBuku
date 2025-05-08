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

// Fungsi untuk mendapatkan ID buku baru
function getNewBookId($koneksi) {
    $query = "SELECT MAX(SUBSTRING(ID_Buku, 3)) as max_id FROM buku";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    $next_id = intval($row['max_id']) + 1;
    return 'BK' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
}

// Proses tambah buku baru
if (isset($_POST['tambah'])) {
    $id_buku = getNewBookId($koneksi);
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $tahun_terbit = mysqli_real_escape_string($koneksi, $_POST['tahun_terbit']);
    $jumlah_halaman = mysqli_real_escape_string($koneksi, $_POST['jumlah_halaman']);
    $id_penerbit = mysqli_real_escape_string($koneksi, $_POST['id_penerbit']);
    
    $query = "INSERT INTO buku (ID_Buku, Judul, Tahun_Terbit, Jumlah_Halaman, ID_Penerbit) 
              VALUES ('$id_buku', '$judul', '$tahun_terbit', '$jumlah_halaman', '$id_penerbit')";
    
    if (mysqli_query($koneksi, $query)) {
        $success_message = "Buku berhasil ditambahkan!";
    } else {
        $error_message = "Error: " . mysqli_error($koneksi);
    }
}

// Proses hapus buku
if (isset($_GET['hapus'])) {
    $id_buku = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    // Periksa apakah buku sedang dipinjam
    $query_check = "SELECT * FROM transaksi_peminjaman WHERE ID_Buku = '$id_buku' AND Status_Peminjaman != 'Dikembalikan'";
    $result_check = mysqli_query($koneksi, $query_check);
    
    if (mysqli_num_rows($result_check) > 0) {
        $error_message = "Tidak dapat menghapus buku karena sedang dipinjam!";
    } else {
        $query = "DELETE FROM buku WHERE ID_Buku = '$id_buku'";
        
        if (mysqli_query($koneksi, $query)) {
            $success_message = "Buku berhasil dihapus!";
        } else {
            $error_message = "Error: " . mysqli_error($koneksi);
        }
    }
}

// Proses edit buku
if (isset($_POST['edit'])) {
    $id_buku = mysqli_real_escape_string($koneksi, $_POST['id_buku']);
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $tahun_terbit = mysqli_real_escape_string($koneksi, $_POST['tahun_terbit']);
    $jumlah_halaman = mysqli_real_escape_string($koneksi, $_POST['jumlah_halaman']);
    $id_penerbit = mysqli_real_escape_string($koneksi, $_POST['id_penerbit']);
    
    $query = "UPDATE buku SET Judul='$judul', Tahun_Terbit='$tahun_terbit', 
              Jumlah_Halaman='$jumlah_halaman', ID_Penerbit='$id_penerbit' 
              WHERE ID_Buku='$id_buku'";
    
    if (mysqli_query($koneksi, $query)) {
        $success_message = "Data buku berhasil diupdate!";
    } else {
        $error_message = "Error: " . mysqli_error($koneksi);
    }
}

// Mengambil data buku dengan nama penerbit
$query_buku = "SELECT b.*, p.Nama_Penerbit 
               FROM buku b 
               JOIN penerbit p ON b.ID_Penerbit = p.ID_Penerbit
               ORDER BY b.ID_Buku";
$result_buku = mysqli_query($koneksi, $query_buku);

// Mengambil data penerbit untuk dropdown
$query_penerbit = "SELECT * FROM penerbit ORDER BY Nama_Penerbit";
$result_penerbit = mysqli_query($koneksi, $query_penerbit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku - Sistem Peminjaman Buku</title>
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
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-home me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="buku.php">
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
                        <a class="nav-link" href="pengembalian.php">                          
                                <i class="fas fa-undo-alt me-2"></i>
                                Pengembalian
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Data Buku</h1>
                </div>

                <?php if (isset($success_message)) : ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif (isset($error_message)) : ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Form Tambah/Edit Buku -->
                <form method="POST" class="row g-3 mb-4">
                    <input type="hidden" name="id_buku" value="<?php echo isset($_GET['edit_id']) ? $_GET['edit_id'] : ''; ?>">
                    <div class="col-md-4">
                        <input type="text" name="judul" class="form-control" placeholder="Judul Buku" required value="<?php echo $_GET['judul'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="tahun_terbit" class="form-control" placeholder="Tahun Terbit" required value="<?php echo $_GET['tahun'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="jumlah_halaman" class="form-control" placeholder="Jumlah Halaman" required value="<?php echo $_GET['halaman'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="id_penerbit" class="form-select" required>
                            <option value="">-- Pilih Penerbit --</option>
                            <?php while ($penerbit = mysqli_fetch_assoc($result_penerbit)) : ?>
                                <option value="<?php echo $penerbit['ID_Penerbit']; ?>" <?php echo isset($_GET['penerbit']) && $_GET['penerbit'] == $penerbit['ID_Penerbit'] ? 'selected' : ''; ?>>
                                    <?php echo $penerbit['Nama_Penerbit']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" name="<?php echo isset($_GET['edit_id']) ? 'edit' : 'tambah'; ?>" class="btn btn-<?php echo isset($_GET['edit_id']) ? 'warning' : 'primary'; ?>">
                            <?php echo isset($_GET['edit_id']) ? 'Update' : 'Tambah'; ?>
                        </button>
                    </div>
                </form>

                <!-- Tabel Data Buku -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID Buku</th>
                                <th>Judul</th>
                                <th>Tahun Terbit</th>
                                <th>Jumlah Halaman</th>
                                <th>Penerbit</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($buku = mysqli_fetch_assoc($result_buku)) : ?>
                                <tr>
                                    <td><?php echo $buku['ID_Buku']; ?></td>
                                    <td><?php echo $buku['Judul']; ?></td>
                                    <td><?php echo $buku['Tahun_Terbit']; ?></td>
                                    <td><?php echo $buku['Jumlah_Halaman']; ?></td>
                                    <td><?php echo $buku['Nama_Penerbit']; ?></td>
                                    <td>
                                        <a href="?edit_id=<?php echo $buku['ID_Buku']; ?>&judul=<?php echo $buku['Judul']; ?>&tahun=<?php echo $buku['Tahun_Terbit']; ?>&halaman=<?php echo $buku['Jumlah_Halaman']; ?>&penerbit=<?php echo $buku['ID_Penerbit']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?hapus=<?php echo $buku['ID_Buku']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus buku ini?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
