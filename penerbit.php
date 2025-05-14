<?php
// Mulai session
session_start();

// Cek apakah user sudah login dan memiliki Jabatan Admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['Jabatan'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Include file koneksi
require_once 'koneksi.php';

// Fungsi untuk mendapatkan ID penerbit baru (sebagai saran saja)
function getNewPublisherId($koneksi) {
    $query = "SELECT MAX(SUBSTRING(ID_Penerbit, 3)) as max_id FROM penerbit";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    $next_id = intval($row['max_id']) + 1;
    return 'PB' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
}

// Proses tambah penerbit baru
if (isset($_POST['tambah'])) {
    $ID_Penerbit = mysqli_real_escape_string($koneksi, $_POST['ID_Penerbit']);
    $Nama_Penerbit = mysqli_real_escape_string($koneksi, $_POST['Nama_Penerbit']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $Kota = mysqli_real_escape_string($koneksi, $_POST['Kota']);
    
    // Check if ID already exists
    $check_id_query = "SELECT * FROM penerbit WHERE ID_Penerbit = '$ID_Penerbit'";
    $check_id_result = mysqli_query($koneksi, $check_id_query);
    
    if (mysqli_num_rows($check_id_result) > 0) {
        $error_message = "ID Penerbit sudah digunakan, silakan pilih ID lain.";
    } else {
        $query = "INSERT INTO penerbit (ID_Penerbit, Nama_Penerbit, alamat, Kota) 
                VALUES ('$ID_Penerbit', '$Nama_Penerbit', '$alamat', '$Kota')";
        
        if (mysqli_query($koneksi, $query)) {
            $success_message = "Penerbit berhasil ditambahkan!";
        } else {
            $error_message = "Error: " . mysqli_error($koneksi);
        }
    }
}

// Proses hapus penerbit
if (isset($_GET['hapus'])) {
    $ID_Penerbit = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    // Periksa apakah penerbit memiliki buku terkait
    $query_check = "SELECT * FROM buku WHERE ID_Penerbit = '$ID_Penerbit'";
    $result_check = mysqli_query($koneksi, $query_check);
    
    if (mysqli_num_rows($result_check) > 0) {
        $error_message = "Tidak dapat menghapus penerbit karena masih memiliki buku terkait!";
    } else {
        $query = "DELETE FROM penerbit WHERE ID_Penerbit = '$ID_Penerbit'";
        
        if (mysqli_query($koneksi, $query)) {
            $success_message = "Penerbit berhasil dihapus!";
        } else {
            $error_message = "Error: " . mysqli_error($koneksi);
        }
    }
}

// Proses edit penerbit
if (isset($_POST['edit'])) {
    $ID_Penerbit = mysqli_real_escape_string($koneksi, $_POST['ID_Penerbit']);
    $Nama_Penerbit = mysqli_real_escape_string($koneksi, $_POST['Nama_Penerbit']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $Kota = mysqli_real_escape_string($koneksi, $_POST['Kota']);
    
    $query = "UPDATE penerbit SET Nama_Penerbit='$Nama_Penerbit', alamat='$alamat', Kota='$Kota' 
              WHERE ID_Penerbit='$ID_Penerbit'";
    
    if (mysqli_query($koneksi, $query)) {
        $success_message = "Data penerbit berhasil diupdate!";
    } else {
        $error_message = "Error: " . mysqli_error($koneksi);
    }
}

// Ambil semua data penerbit
$query_penerbit = "SELECT * FROM penerbit ORDER BY Nama_Penerbit";
$result_penerbit = mysqli_query($koneksi, $query_penerbit);

// Get suggested new ID
$suggested_id = getNewPublisherId($koneksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penerbit - Sistem Peminjaman Buku</title>
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
                            <a class="nav-link" href="peminjaman.php">
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
                        <?php if ($_SESSION['Jabatan'] == 'Admin') : ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="penerbit.php">
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
                    <h1 class="h2">Data Penerbit</h1>
                </div>

                <?php if (isset($success_message)) : ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif (isset($error_message)) : ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Form Tambah/Edit Penerbit -->
                <form method="POST" class="row g-3 mb-4">
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-text" title="Format: PNxxx">ID</span>
                            <input type="text" name="ID_Penerbit" class="form-control" placeholder="ID Penerbit" required 
                                value="<?php echo isset($_GET['edit_id']) ? $_GET['edit_id'] : $suggested_id; ?>" 
                                <?php echo isset($_GET['edit_id']) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="Nama_Penerbit" class="form-control" placeholder="Nama Penerbit" required value="<?php echo $_GET['nama'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="alamat" class="form-control" placeholder="Alamat" required value="<?php echo $_GET['alamat'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="Kota" class="form-control" placeholder="Kota" required value="<?php echo $_GET['Kota'] ?? ''; ?>">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" name="<?php echo isset($_GET['edit_id']) ? 'edit' : 'tambah'; ?>" class="btn btn-<?php echo isset($_GET['edit_id']) ? 'warning' : 'primary'; ?>">
                            <?php echo isset($_GET['edit_id']) ? 'Update' : 'Tambah'; ?>
                        </button>
                    </div>
                </form>

                <!-- Tabel Data Penerbit -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID Penerbit</th>
                                <th>Nama Penerbit</th>
                                <th>Alamat</th>
                                <th>Kota</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($penerbit = mysqli_fetch_assoc($result_penerbit)) : ?>
                                <tr>
                                    <td><?php echo $penerbit['ID_Penerbit']; ?></td>
                                    <td><?php echo $penerbit['Nama_Penerbit']; ?></td>
                                    <td><?php echo $penerbit['Alamat_Penerbit']; ?></td>
                                    <td><?php echo $penerbit['Kota']; ?></td>
                                    <td>
                                        <a href="?edit_id=<?php echo $penerbit['ID_Penerbit']; ?>&nama=<?php echo $penerbit['Nama_Penerbit']; ?>&alamat=<?php echo $penerbit['Alamat_Penerbit']; ?>&Kota=<?php echo $penerbit['Kota']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?hapus=<?php echo $penerbit['ID_Penerbit']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus penerbit ini?');">
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