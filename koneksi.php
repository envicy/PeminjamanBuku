<?php
$host = "localhost"; // Tambahkan :3307 jika perlu
$Username = "root";
$Password = "Hikari1223"; // Atau "" jika tidak pakai Password
$database = "dbpeminjamanbuku";

$koneksi = mysqli_connect($host, $Username, $Password, $database);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
} else {
    echo "Koneksi berhasil!";
}
?>
