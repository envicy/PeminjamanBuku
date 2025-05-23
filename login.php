<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Peminjaman Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo h1 {
            color: #3c4858;
        }
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
            width: 100%;
        }
        .alert {
            margin-bottom: 20px;
        }
        .position-relative {
            position: relative;
        }
        .toggle-Password-icon {
            position: absolute;
            top: 38px;
            right: 15px;
            width: 20px;
            cursor: pointer;
            z-index: 2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-logo">
                <h1>Perpustakaan</h1>
                <p>Sistem Peminjaman Buku</p>
            </div>
            
            <?php
            if (isset($_GET['error'])) {
                echo '<div class="alert alert-danger">Username atau Password salah!</div>';
            }
            if (isset($_GET['logout'])) {
                echo '<div class="alert alert-success">Berhasil logout.</div>';
            }
            ?>
            
            <form action="proses_login.php" method="post">
                <div class="mb-3">
                    <label for="Username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="Username" name="Username" required>
                </div>
                <div class="mb-3 position-relative">
                    <label for="Password" class="form-label">Password</label>
                    <input type="Password" class="form-control" id="Password" name="Password" required>
                    <img id="eyeIcon" src="image/eye.png" alt="Toggle Password" class="toggle-Password-icon" onclick="togglePassword()">
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const PasswordInput = document.getElementById('Password');
            const icon = document.getElementById('eyeIcon');

            if (PasswordInput.type === 'Password') {
                PasswordInput.type = 'text';
                icon.src = 'image/eye-off.png';
            } else {
                PasswordInput.type = 'Password';
                icon.src = 'image/eye.png';
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
