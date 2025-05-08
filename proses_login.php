<?php
// Mulai session
session_start();

// Include file koneksi
require_once 'koneksi.php';

// Cek apakah form login sudah di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    
    // Query untuk mencari staff dengan username dan password yang sesuai
    $query = "SELECT * FROM staff WHERE Username='$username' AND Password='$password'";
    $result = mysqli_query($koneksi, $query);
    
    // Jika ditemukan user yang cocok
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Set session variables
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $row['ID_Staff'];
        $_SESSION['username'] = $row['Username'];
        $_SESSION['nama'] = $row['Nama_Staff'];
        $_SESSION['jabatan'] = $row['Jabatan'];
        
        // Redirect ke dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // Jika login gagal, redirect kembali ke halaman login dengan pesan error
        header("Location: login.php?error=1");
        exit();
    }
}
?>