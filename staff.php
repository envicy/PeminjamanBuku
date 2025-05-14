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

// Fungsi untuk mendapatkan ID staff baru (sebagai saran saja)
function getNewStaffId($koneksi) {
    $query = "SELECT MAX(SUBSTRING(ID_Staff, 3)) as max_id FROM staff";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    $next_id = intval($row['max_id']) + 1;
    return 'ST' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
}

// Proses tambah staff baru
if (isset($_POST['tambah'])) {
    $ID_Staff = mysqli_real_escape_string($koneksi, $_POST['ID_Staff']);
    $Nama_Staff = mysqli_real_escape_string($koneksi, $_POST['Nama_Staff']);
    $Jabatan = mysqli_real_escape_string($koneksi, $_POST['Jabatan']);
    $Username = mysqli_real_escape_string($koneksi, $_POST['Username']);
    $Password = mysqli_real_escape_string($koneksi, $_POST['Password']);
    
    // Check if ID already exists
    $check_id_query = "SELECT * FROM staff WHERE ID_Staff = '$ID_Staff'";
    $check_id_result = mysqli_query($koneksi, $check_id_query);
    
    // Check if Username already exists
    $check_query = "SELECT * FROM staff WHERE Username = '$Username'";
    $check_result = mysqli_query($koneksi, $check_query);
    
    if (mysqli_num_rows($check_id_result) > 0) {
        $error_message = "ID Staff sudah digunakan, silakan pilih ID lain.";
    } else if (mysqli_num_rows($check_result) > 0) {
        $error_message = "Username sudah digunakan, silakan pilih Username lain.";
    } else {
        $query = "INSERT INTO staff (ID_Staff, Nama_Staff, Jabatan, Username, Password) 
                VALUES ('$ID_Staff', '$Nama_Staff', '$Jabatan', '$Username', '$Password')";
        
        if (mysqli_query($koneksi, $query)) {
            $success_message = "Staff berhasil ditambahkan!";
        } else {
            $error_message = "Error: " . mysqli_error($koneksi);
        }
    }
}

// Proses hapus staff
if (isset($_GET['hapus'])) {
    $ID_Staff = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    // Jangan hapus diri sendiri
    if ($ID_Staff == $_SESSION['user_id']) {
        $error_message = "Anda tidak dapat menghapus akun yang sedang digunakan!";
    } else {
        $query = "DELETE FROM staff WHERE ID_Staff = '$ID_Staff'";
        
        if (mysqli_query($koneksi, $query)) {
            $success_message = "Staff berhasil dihapus!";
        } else {
            $error_message = "Error: " . mysqli_error($koneksi);
        }
    }
}

// Proses edit staff
if (isset($_POST['edit'])) {
    $ID_Staff = mysqli_real_escape_string($koneksi, $_POST['ID_Staff']);
    $Nama_Staff = mysqli_real_escape_string($koneksi, $_POST['Nama_Staff']);
    $Jabatan = mysqli_real_escape_string($koneksi, $_POST['Jabatan']);
    $Username = mysqli_real_escape_string($koneksi, $_POST['Username']);
    $Password = mysqli_real_escape_string($koneksi, $_POST['Password']);
    
    // Check if Username already exists but not for the same staff
    $check_query = "SELECT * FROM staff WHERE Username = '$Username' AND ID_Staff != '$ID_Staff'";
    $check_result = mysqli_query($koneksi, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "Username sudah digunakan, silakan pilih Username lain.";
    } else {
        // If Password is not changed, keep the old one
        if (empty($Password)) {
            $query = "UPDATE staff SET Nama_Staff='$Nama_Staff', Jabatan='$Jabatan', Username='$Username' 
                    WHERE ID_Staff='$ID_Staff'";
        } else {
            $query = "UPDATE staff SET Nama_Staff='$Nama_Staff', Jabatan='$Jabatan', Username='$Username', Password='$Password' 
                    WHERE ID_Staff='$ID_Staff'";
        }
        
        if (mysqli_query($koneksi, $query)) {
            $success_message = "Data staff berhasil diupdate!";
        } else {
            $error_message = "Error: " . mysqli_error($koneksi);
        }
    }
}

// Ambil semua data staff
$query_staff = "SELECT * FROM staff ORDER BY Nama_Staff";
$result_staff = mysqli_query($koneksi, $query_staff);

// Get suggested new ID
$suggested_id = getNewStaffId($koneksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Staff - Sistem Peminjaman Buku</title>
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
        .Password-container {
            position: relative;
        }
        .toggle-Password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
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
                            <a class="nav-link" href="penerbit.php">
                                <i class="fas fa-building me-2"></i>
                                Data Penerbit
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="staff.php">
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
                    <h1 class="h2">Data Staff</h1>
                </div>

                <?php if (isset($success_message)) : ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif (isset($error_message)) : ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Form Tambah/Edit Staff -->
                <form method="POST" class="row g-3 mb-4">
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-text" title="Format: STxxx">ID</span>
                            <input type="text" name="ID_Staff" class="form-control" placeholder="ID Staff" required 
                                value="<?php echo isset($_GET['edit_id']) ? $_GET['edit_id'] : $suggested_id; ?>" 
                                <?php echo isset($_GET['edit_id']) ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="Nama_Staff" class="form-control" placeholder="Nama Staff" required value="<?php echo $_GET['nama'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="Jabatan" class="form-select" required>
                            <option value="">-- Pilih Jabatan --</option>
                            <option value="Admin" <?php echo (isset($_GET['Jabatan']) && $_GET['Jabatan'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="Pustakawan" <?php echo (isset($_GET['Jabatan']) && $_GET['Jabatan'] == 'Pustakawan') ? 'selected' : ''; ?>>Pustakawan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="Username" class="form-control" placeholder="Username" required value="<?php echo $_GET['Username'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2 Password-container">
                        <input type="Password" name="Password" id="Password" class="form-control" placeholder="<?php echo isset($_GET['edit_id']) ? 'New Password (kosongkan jika tidak diubah)' : 'Password'; ?>" <?php echo isset($_GET['edit_id']) ? '' : 'required'; ?>>
                        <i class="toggle-Password fas fa-eye-slash" onclick="togglePassword()"></i>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" name="<?php echo isset($_GET['edit_id']) ? 'edit' : 'tambah'; ?>" class="btn btn-<?php echo isset($_GET['edit_id']) ? 'warning' : 'primary'; ?>">
                            <?php echo isset($_GET['edit_id']) ? 'Update' : 'Tambah'; ?>
                        </button>
                    </div>
                </form>

                <!-- Tabel Data Staff -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID Staff</th>
                                <th>Nama Staff</th>
                                <th>Jabatan</th>
                                <th>Username</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($staff = mysqli_fetch_assoc($result_staff)) : ?>
                                <tr>
                                    <td><?php echo $staff['ID_Staff']; ?></td>
                                    <td><?php echo $staff['Nama_Staff']; ?></td>
                                    <td><?php echo $staff['Jabatan']; ?></td>
                                    <td><?php echo $staff['Username']; ?></td>
                                    <td>
                                        <a href="?edit_id=<?php echo $staff['ID_Staff']; ?>&nama=<?php echo $staff['Nama_Staff']; ?>&Jabatan=<?php echo $staff['Jabatan']; ?>&Username=<?php echo $staff['Username']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($staff['ID_Staff'] != $_SESSION['user_id']) : ?>
                                        <a href="?hapus=<?php echo $staff['ID_Staff']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus staff ini?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script>
        function togglePassword() {
            const PasswordInput = document.getElementById('Password');
            const toggleIcon = document.querySelector('.toggle-Password');
            
            if (PasswordInput.type === 'Password') {
                PasswordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                PasswordInput.type = 'Password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>