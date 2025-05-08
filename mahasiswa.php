<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'koneksi.php';

// Ambil data mahasiswa dan jumlah buku yang sedang dipinjam
$query = "
    SELECT m.NIM, m.Nama_Mahasiswa AS Nama, COUNT(t.ID_Buku) AS Jumlah_Dipinjam
    FROM mahasiswa m
    LEFT JOIN transaksi_peminjaman t 
        ON m.NIM = t.NIM AND t.Status_Peminjaman != 'Dikembalikan'
    GROUP BY m.NIM, m.Nama_Mahasiswa
    ORDER BY m.Nama_Mahasiswa
";
$result = mysqli_query($koneksi, $query);
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
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Data Mahasiswa</h1>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Jumlah Buku Dipinjam</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['NIM']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Nama']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Jumlah_Dipinjam']); ?></td>
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
