<?php
$host = "localhost"; // Tambahkan :3307 jika perlu
$username = "root";
$password = "Hikari1223"; // Atau "" jika tidak pakai password
$database = "dbpeminjamanbuku";

$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
} else {
    echo "Koneksi berhasil!";
}
?>
