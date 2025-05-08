-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: dbpeminjamanbuku
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `buku`
--

DROP TABLE IF EXISTS `buku`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `buku` (
  `ID_Buku` char(5) NOT NULL,
  `Judul` varchar(75) DEFAULT NULL,
  `Tahun_Terbit` year DEFAULT NULL,
  `Jumlah_Halaman` int DEFAULT NULL,
  `ID_Penerbit` char(4) DEFAULT NULL,
  PRIMARY KEY (`ID_Buku`),
  KEY `ID_Penerbit` (`ID_Penerbit`),
  CONSTRAINT `buku_ibfk_1` FOREIGN KEY (`ID_Penerbit`) REFERENCES `penerbit` (`ID_Penerbit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buku`
--

LOCK TABLES `buku` WRITE;
/*!40000 ALTER TABLE `buku` DISABLE KEYS */;
INSERT INTO `buku` VALUES ('BK001','Dasar Pemograman',2010,275,'PB02'),('BK002','Dasar-Dasar Teknik Elektro',2011,300,'PB05'),('BK003','Teknologi Pasca Panen',2012,400,'PB04'),('BK004','Budidaya Tanaman Organik',2012,200,'PB08'),('BK005','Pengantar Ilmu Hukum',2013,230,'PB06'),('BK006','Kurikulum dan Pembelajaran',2020,600,'PB07'),('BK007','Ekonomi Makro',2015,180,'PB09'),('BK008','Struktur Molekul dan Ikatan Kimia',2023,150,'PB10'),('BK009','Hubungan Internasional Kontemporer',2017,243,'PB01'),('BK010','Kalkulus Dasar',2018,350,'PB03');
/*!40000 ALTER TABLE `buku` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mahasiswa`
--

DROP TABLE IF EXISTS `mahasiswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mahasiswa` (
  `NIM` varchar(50) NOT NULL,
  `Nama_Mahasiswa` varchar(50) DEFAULT NULL,
  `Alamat_Mahasiswa` text,
  `No_Telepon_Mahasiswa` varchar(15) DEFAULT NULL,
  `Status_Mahasiswa` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`NIM`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mahasiswa`
--

LOCK TABLES `mahasiswa` WRITE;
/*!40000 ALTER TABLE `mahasiswa` DISABLE KEYS */;
INSERT INTO `mahasiswa` VALUES ('A1012201032','Budi Santoso','Jl. Sungai Raya Dalam','082627282930','Aktif'),('B1011241157','Ahmad Fauzi','Jl. M. Sohor','083637383940','Aktif'),('C1021201051','Muhammad Rizky','Jl. Imam Bonjol','081617181920','Aktif'),('C1061231036','Reza Kurniawan','Jl. Merdeka','084647484950','Aktif'),('D1021221078','Maya Anggraini','Jl. Purnama','085152535455','Aktif'),('D1041191045','Siti Nurhaliza','Jl. Tanjung Raya II','081112131415','Aktif'),('E1112241014','Ratna Sari','Jl. Karet','083132333435','Aktif'),('F1061221004','Dewi Lestari','Jl. Sepakat 2','082122232425','Aktif'),('H1012110007','Putri Ramadhani','Jl. Veteran','084142434445','Aktif'),('H1031221047','Andi Pratama','Jl. Ahmad Yani','081234567890','Aktif');
/*!40000 ALTER TABLE `mahasiswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penerbit`
--

DROP TABLE IF EXISTS `penerbit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penerbit` (
  `ID_Penerbit` char(4) NOT NULL,
  `Nama_Penerbit` varchar(50) DEFAULT NULL,
  `Alamat_Penerbit` text,
  `Kota` varchar(75) DEFAULT NULL,
  `No_Telepon_Penerbit` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ID_Penerbit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penerbit`
--

LOCK TABLES `penerbit` WRITE;
/*!40000 ALTER TABLE `penerbit` DISABLE KEYS */;
INSERT INTO `penerbit` VALUES ('PB01','Gramedia Pustaka','Jl. Cilembu','Jakarta','021-53650110'),('PB02','Mizan Pustaka','Jl. Palmerah Barat','Jakarta','022-7834310'),('PB03','Erlangga','Jl. Palmerah Timur','Bandung','021-8717006'),('PB04','Bentang Pustaka','Jl. Haji Bamping','Yogyakarta','0274-889836'),('PB05','Penerbit Kompas','Jl. Pandega Padma','Yogyakarta','021-53650008'),('PB06','Kepustakaan Populer','Jl. Haji Motong','Jakarta','021-5347710'),('PB07','Gagas Media','Jl. Beo','Bandung','021-78842005'),('PB08','Pustaka Bentang','Jl. Bentang Panjang','Bandung','0274-561881'),('PB09','Pustaka Alfa','Jl. Cipinang','Yogyakarta','021-8193324'),('PB10','Pustaka Noura','Jl. Jagakarsa','Jakarta','021-78882066');
/*!40000 ALTER TABLE `penerbit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff` (
  `ID_Staff` char(5) NOT NULL,
  `Nama_Staff` varchar(50) DEFAULT NULL,
  `Alamat_Staff` text,
  `Jabatan` varchar(15) DEFAULT NULL,
  `Username` varchar(20) DEFAULT NULL,
  `Password` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`ID_Staff`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
INSERT INTO `staff` VALUES ('ST001','Bambang Hermawan','Jl. Kom Yos Sudarso','Pustakawan','bambang','bam123'),('ST002','Ratna','Jl. Reformasi','Pustakawan','ratna','ratna456'),('ST003','Hendra Gunawan','Jl. Veteran','Pustakawan','hendra','hen789'),('ST004','Dina Mardina','Jl. Gajah Mada','Pustakawan','dina','din012'),('ST005','Agus Setiawan','Jl. Tabrani Ahmad','Admin','agus','agus345'),('ST006','Siska Amelia','Jl. Tebu','Pustakawan','siska','siska678'),('ST007','Arif Rahman','Jl. Imam Bonjol','Admin','arif','ari123'),('ST008','Nita Anggraini','Jl. Purnama','Pustakawan','nita','nita456'),('ST009','Tono Wijaya','Jl. Merdeka','Admin','tono','tono789'),('ST010','Lusi Indah','Jl. Purnama','Admin','lusi','lusi123');
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksi_peminjaman`
--

DROP TABLE IF EXISTS `transaksi_peminjaman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaksi_peminjaman` (
  `ID_Peminjaman` int NOT NULL AUTO_INCREMENT,
  `Tanggal_Pinjam` date DEFAULT NULL,
  `Tanggal_Kembali` date DEFAULT NULL,
  `Status_Peminjaman` varchar(20) DEFAULT NULL,
  `NIM` varchar(15) DEFAULT NULL,
  `ID_Buku` char(5) DEFAULT NULL,
  `ID_Staff` char(5) DEFAULT NULL,
  PRIMARY KEY (`ID_Peminjaman`),
  KEY `NIM` (`NIM`),
  KEY `ID_Buku` (`ID_Buku`),
  KEY `ID_Staff` (`ID_Staff`),
  CONSTRAINT `transaksi_peminjaman_ibfk_1` FOREIGN KEY (`NIM`) REFERENCES `mahasiswa` (`NIM`),
  CONSTRAINT `transaksi_peminjaman_ibfk_2` FOREIGN KEY (`ID_Buku`) REFERENCES `buku` (`ID_Buku`),
  CONSTRAINT `transaksi_peminjaman_ibfk_3` FOREIGN KEY (`ID_Staff`) REFERENCES `staff` (`ID_Staff`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksi_peminjaman`
--

LOCK TABLES `transaksi_peminjaman` WRITE;
/*!40000 ALTER TABLE `transaksi_peminjaman` DISABLE KEYS */;
INSERT INTO `transaksi_peminjaman` VALUES (1,'2024-10-05','2024-10-10','Dikembalikan','D1041191045','BK001','ST002'),(2,'2024-12-15','2024-12-20','Dikembalikan','C1021201051','BK004','ST004'),(3,'2025-01-05','2025-01-13','Dikembalikan','D1021221078','BK002','ST006'),(4,'2025-01-17','2025-01-23','Dikembalikan','A1012021032','BK005','ST002'),(5,'2025-02-19','2025-02-27','Dikembalikan','H1031221047','BK008','ST008'),(6,'2025-02-28','2025-03-06','Dikembalikan','C1061232036','BK003','ST006'),(7,'2025-03-12','2025-03-19','Dikembalikan','B1011241157','BK007','ST004'),(8,'2025-03-21','2025-03-27','Dikembalikan','E1112241014','BK009','ST008'),(9,'2025-04-02','2025-04-08','Dikembalikan','F1061221004','BK006','ST003'),(10,'2025-04-10','2025-04-16','Dikembalikan','H1012110007','BK010','ST001');
/*!40000 ALTER TABLE `transaksi_peminjaman` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-05 22:40:20
