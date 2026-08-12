-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: dashboard_pengadaan
-- ------------------------------------------------------
-- Server version	8.0.46-0ubuntu0.24.04.3

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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `import_rencana_umum_pengadaan`
--

DROP TABLE IF EXISTS `import_rencana_umum_pengadaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `import_rencana_umum_pengadaan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_rup` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_pekerjaan` text COLLATE utf8mb4_unicode_ci,
  `pagu` text COLLATE utf8mb4_unicode_ci,
  `nama_jenis_pengadaan` text COLLATE utf8mb4_unicode_ci,
  `nama_jenis_produk_rup` text COLLATE utf8mb4_unicode_ci,
  `nama_jenis_usaha` text COLLATE utf8mb4_unicode_ci,
  `nama_metode_pengadaan` text COLLATE utf8mb4_unicode_ci,
  `waktu_pemilihan_penyedia` text COLLATE utf8mb4_unicode_ci,
  `nama_instansi` text COLLATE utf8mb4_unicode_ci,
  `nama_organisasi` text COLLATE utf8mb4_unicode_ci,
  `lokasi_pekerjaan` longtext COLLATE utf8mb4_unicode_ci,
  `nama_bidang_pekerjaan` text COLLATE utf8mb4_unicode_ci,
  `tahun_anggaran` text COLLATE utf8mb4_unicode_ci,
  `pic` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `is_sirup` int DEFAULT NULL,
  `is_import` int DEFAULT NULL,
  `is_pekerjaan_prospek` int unsigned DEFAULT NULL,
  `id_sis_rup` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_organisasi` text COLLATE utf8mb4_unicode_ci,
  `telepon_organisasi` text COLLATE utf8mb4_unicode_ci,
  `is_status_kirim_penawaran` int DEFAULT NULL,
  `id_nomor_surat` bigint unsigned DEFAULT NULL,
  `input_id` bigint DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import_rencana_umum_pengadaan`
--

LOCK TABLES `import_rencana_umum_pengadaan` WRITE;
/*!40000 ALTER TABLE `import_rencana_umum_pengadaan` DISABLE KEYS */;
INSERT INTO `import_rencana_umum_pengadaan` VALUES (1,'RUP-001','Proyek Pengadaan Perangkat','1.088.662.601','Barang','Infrastruktur','UMKM','Seleksi','2026-07-22','Kementerian Kominfo','Organisasi 1','Jakarta 1','Teknologi Informasi','2026','PIC 1','2026-06-17 04:02:48','Seed data untuk testing dashboard RUP.',1,0,0,'SIS-1','Jl. Demo No. 1','021-0000001',0,3104,1,'2026-06-17 04:02:48',NULL),(2,'RUP-002','Proyek Pengadaan Perangkat','590.734.995','Jasa','Infrastruktur','UMKM','Seleksi','2026-07-23','Badan Pusat Statistik','Organisasi 2','Jakarta 2','Manajemen Proyek','2026','PIC 2','2026-06-18 04:02:48','Seed data untuk testing dashboard RUP.',0,0,0,'SIS-2','Jl. Demo No. 2','021-0000002',0,1299,2,'2026-06-18 04:02:48',NULL),(3,'RUP-003','Proyek Pengadaan Perangkat','1.333.151.542','Barang','Aplikasi','UMKM','Tender','2026-07-24','Kementerian Kominfo','Organisasi 3','Jakarta 3','Teknologi Informasi','2026','PIC 3','2026-06-19 04:02:48','Seed data untuk testing dashboard RUP.',1,1,0,'SIS-3','Jl. Demo No. 3','021-0000003',0,7642,3,'2026-06-19 04:02:48',NULL),(4,'RUP-004','Proyek Pengadaan Perangkat','2.306.148.159','Jasa','Infrastruktur','Perusahaan Teknologi','Seleksi','2026-07-25','Badan Pusat Statistik','Organisasi 4','Jakarta 4','Manajemen Proyek','2026','PIC 4','2026-06-20 04:02:48','Seed data untuk testing dashboard RUP.',0,0,1,'SIS-4','Jl. Demo No. 4','021-0000004',0,3799,4,'2026-06-20 04:02:48',NULL),(5,'RUP-005','Proyek Pengadaan Sistem','821.647.527','Barang','Infrastruktur','UMKM','Seleksi','2026-07-26','Kementerian Kominfo','Organisasi 5','Jakarta 5','Teknologi Informasi','2026','PIC 5','2026-06-21 04:02:48','Seed data untuk testing dashboard RUP.',1,0,0,'SIS-5','Jl. Demo No. 5','021-0000005',1,1408,5,'2026-06-21 04:02:48',NULL),(6,'RUP-006','Proyek Pengadaan Perangkat','871.557.990','Jasa','Aplikasi','UMKM','Tender','2026-07-27','Badan Pusat Statistik','Organisasi 6','Jakarta 6','Manajemen Proyek','2026','PIC 6','2026-06-22 04:02:48','Seed data untuk testing dashboard RUP.',0,1,0,'SIS-6','Jl. Demo No. 6','021-0000006',0,9632,6,'2026-06-22 04:02:48',NULL),(7,'RUP-007','Proyek Pengadaan Perangkat','711.586.030','Barang','Infrastruktur','UMKM','Seleksi','2026-07-28','Kementerian Kominfo','Organisasi 7','Jakarta 7','Teknologi Informasi','2026','PIC 7','2026-06-23 04:02:48','Seed data untuk testing dashboard RUP.',1,0,0,'SIS-7','Jl. Demo No. 7','021-0000007',0,2170,7,'2026-06-23 04:02:48',NULL),(8,'RUP-008','Proyek Pengadaan Perangkat','743.640.188','Jasa','Infrastruktur','Perusahaan Teknologi','Seleksi','2026-07-29','Badan Pusat Statistik','Organisasi 8','Jakarta 8','Manajemen Proyek','2026','PIC 8','2026-06-24 04:02:48','Seed data untuk testing dashboard RUP.',0,0,1,'SIS-8','Jl. Demo No. 8','021-0000008',0,3255,8,'2026-06-24 04:02:48',NULL),(9,'RUP-009','Proyek Pengadaan Perangkat','1.916.264.169','Barang','Aplikasi','UMKM','Tender','2026-07-30','Kementerian Kominfo','Organisasi 9','Jakarta 9','Teknologi Informasi','2026','PIC 9','2026-06-25 04:02:48','Seed data untuk testing dashboard RUP.',1,1,0,'SIS-9','Jl. Demo No. 9','021-0000009',0,6737,9,'2026-06-25 04:02:48',NULL),(10,'RUP-010','Proyek Pengadaan Sistem','1.575.871.616','Jasa','Infrastruktur','UMKM','Seleksi','2026-07-31','Badan Pusat Statistik','Organisasi 10','Jakarta 10','Manajemen Proyek','2026','PIC 10','2026-06-26 04:02:48','Seed data untuk testing dashboard RUP.',0,0,0,'SIS-10','Jl. Demo No. 10','021-0000010',1,3594,10,'2026-06-26 04:02:48',NULL),(11,'RUP-011','Proyek Pengadaan Perangkat','1.504.343.458','Barang','Infrastruktur','UMKM','Seleksi','2026-08-01','Kementerian Kominfo','Organisasi 11','Jakarta 11','Teknologi Informasi','2026','PIC 11','2026-06-27 04:02:48','Seed data untuk testing dashboard RUP.',1,0,0,'SIS-11','Jl. Demo No. 11','021-0000011',0,8123,11,'2026-06-27 04:02:48',NULL),(12,'RUP-012','Proyek Pengadaan Perangkat','1.550.383.043','Jasa','Aplikasi','Perusahaan Teknologi','Tender','2026-08-02','Badan Pusat Statistik','Organisasi 12','Jakarta 12','Manajemen Proyek','2026','PIC 12','2026-06-28 04:02:48','Seed data untuk testing dashboard RUP.',0,1,1,'SIS-12','Jl. Demo No. 12','021-0000012',0,5018,12,'2026-06-28 04:02:48',NULL),(13,'RUP-013','Proyek Pengadaan Perangkat','1.925.488.575','Barang','Infrastruktur','UMKM','Seleksi','2026-08-03','Kementerian Kominfo','Organisasi 13','Jakarta 13','Teknologi Informasi','2026','PIC 13','2026-06-29 04:02:48','Seed data untuk testing dashboard RUP.',1,0,0,'SIS-13','Jl. Demo No. 13','021-0000013',0,3725,13,'2026-06-29 04:02:48',NULL),(14,'RUP-014','Proyek Pengadaan Perangkat','2.217.659.330','Jasa','Infrastruktur','UMKM','Seleksi','2026-08-04','Badan Pusat Statistik','Organisasi 14','Jakarta 14','Manajemen Proyek','2026','PIC 14','2026-06-30 04:02:48','Seed data untuk testing dashboard RUP.',0,0,0,'SIS-14','Jl. Demo No. 14','021-0000014',0,3461,14,'2026-06-30 04:02:48',NULL),(15,'RUP-015','Proyek Pengadaan Sistem','1.717.591.421','Barang','Aplikasi','UMKM','Tender','2026-08-05','Kementerian Kominfo','Organisasi 15','Jakarta 15','Teknologi Informasi','2026','PIC 15','2026-07-01 04:02:48','Seed data untuk testing dashboard RUP.',1,1,0,'SIS-15','Jl. Demo No. 15','021-0000015',1,6025,15,'2026-07-01 04:02:48',NULL),(16,'RUP-016','Proyek Pengadaan Perangkat','1.590.183.258','Jasa','Infrastruktur','Perusahaan Teknologi','Seleksi','2026-08-06','Badan Pusat Statistik','Organisasi 16','Jakarta 16','Manajemen Proyek','2026','PIC 16','2026-07-02 04:02:48','Seed data untuk testing dashboard RUP.',0,0,1,'SIS-16','Jl. Demo No. 16','021-0000016',0,3013,16,'2026-07-02 04:02:48',NULL),(17,'RUP-017','Proyek Pengadaan Perangkat','1.625.489.012','Barang','Infrastruktur','UMKM','Seleksi','2026-08-07','Kementerian Kominfo','Organisasi 17','Jakarta 17','Teknologi Informasi','2026','PIC 17','2026-07-03 04:02:48','Seed data untuk testing dashboard RUP.',1,0,0,'SIS-17','Jl. Demo No. 17','021-0000017',0,5374,17,'2026-07-03 04:02:48',NULL),(18,'RUP-018','Proyek Pengadaan Perangkat','1.271.816.087','Jasa','Aplikasi','UMKM','Tender','2026-08-08','Badan Pusat Statistik','Organisasi 18','Jakarta 18','Manajemen Proyek','2026','PIC 18','2026-07-04 04:02:48','Seed data untuk testing dashboard RUP.',0,1,0,'SIS-18','Jl. Demo No. 18','021-0000018',0,6791,18,'2026-07-04 04:02:48',NULL),(19,'RUP-019','Proyek Pengadaan Perangkat','1.935.423.826','Barang','Infrastruktur','UMKM','Seleksi','2026-08-09','Kementerian Kominfo','Organisasi 19','Jakarta 19','Teknologi Informasi','2026','PIC 19','2026-07-05 04:02:48','Seed data untuk testing dashboard RUP.',1,0,0,'SIS-19','Jl. Demo No. 19','021-0000019',0,4650,19,'2026-07-05 04:02:48',NULL),(20,'RUP-020','Proyek Pengadaan Sistem','2.399.466.275','Jasa','Infrastruktur','Perusahaan Teknologi','Seleksi','2026-08-10','Badan Pusat Statistik','Organisasi 20','Jakarta 20','Manajemen Proyek','2026','PIC 20','2026-07-06 04:02:48','Seed data untuk testing dashboard RUP.',0,0,1,'SIS-20','Jl. Demo No. 20','021-0000020',1,9111,20,'2026-07-06 04:02:48',NULL),(21,'RUP-021','Proyek Pengadaan Perangkat','1.273.006.121','Barang','Aplikasi','UMKM','Tender','2026-08-11','Kementerian Kominfo','Organisasi 21','Jakarta 21','Teknologi Informasi','2026','PIC 21','2026-07-07 04:02:48','Seed data untuk testing dashboard RUP.',1,1,0,'SIS-21','Jl. Demo No. 21','021-0000021',0,6519,21,'2026-07-07 04:02:48',NULL),(22,'RUP-022','Proyek Pengadaan Perangkat','1.105.054.658','Jasa','Infrastruktur','UMKM','Seleksi','2026-08-12','Badan Pusat Statistik','Organisasi 22','Jakarta 22','Manajemen Proyek','2026','PIC 22','2026-07-08 04:02:48','Seed data untuk testing dashboard RUP.',0,0,0,'SIS-22','Jl. Demo No. 22','021-0000022',0,9542,22,'2026-07-08 04:02:48',NULL),(23,'RUP-023','Proyek Pengadaan Perangkat','1.889.706.820','Barang','Infrastruktur','UMKM','Seleksi','2026-08-13','Kementerian Kominfo','Organisasi 23','Jakarta 23','Teknologi Informasi','2026','PIC 23','2026-07-09 04:02:48','Seed data untuk testing dashboard RUP.',1,0,0,'SIS-23','Jl. Demo No. 23','021-0000023',0,5384,23,'2026-07-09 04:02:48',NULL),(24,'RUP-024','Proyek Pengadaan Perangkat','1.907.694.273','Jasa','Aplikasi','Perusahaan Teknologi','Tender','2026-08-14','Badan Pusat Statistik','Organisasi 24','Jakarta 24','Manajemen Proyek','2026','PIC 24','2026-07-10 04:02:48','Seed data untuk testing dashboard RUP.',0,1,1,'SIS-24','Jl. Demo No. 24','021-0000024',0,2922,24,'2026-07-10 04:02:48',NULL),(25,'RUP-025','Proyek Pengadaan Sistem','1.719.257.586','Barang','Infrastruktur','UMKM','Seleksi','2026-08-15','Kementerian Kominfo','Organisasi 25','Jakarta 25','Teknologi Informasi','2026','PIC 25','2026-07-11 04:02:48','Seed data untuk testing dashboard RUP.',1,0,0,'SIS-25','Jl. Demo No. 25','021-0000025',1,5339,25,'2026-07-11 04:02:48',NULL),(26,'RUP-026','Proyek Pengadaan Perangkat','625.033.518','Jasa','Infrastruktur','UMKM','Seleksi','2026-08-16','Badan Pusat Statistik','Organisasi 26','Jakarta 26','Manajemen Proyek','2026','PIC 26','2026-07-12 04:02:48','Seed data untuk testing dashboard RUP.',0,0,0,'SIS-26','Jl. Demo No. 26','021-0000026',0,4327,26,'2026-07-12 04:02:48',NULL),(27,'RUP-027','Proyek Pengadaan Perangkat','1.232.289.200','Barang','Aplikasi','UMKM','Tender','2026-08-17','Kementerian Kominfo','Organisasi 27','Jakarta 27','Teknologi Informasi','2026','PIC 27','2026-07-13 04:02:48','Seed data untuk testing dashboard RUP.',1,1,0,'SIS-27','Jl. Demo No. 27','021-0000027',0,8746,27,'2026-07-13 04:02:48',NULL),(28,'RUP-028','Proyek Pengadaan Perangkat','1.703.225.365','Jasa','Infrastruktur','Perusahaan Teknologi','Seleksi','2026-08-18','Badan Pusat Statistik','Organisasi 28','Jakarta 28','Manajemen Proyek','2026','PIC 28','2026-07-14 04:02:48','Seed data untuk testing dashboard RUP.',0,0,1,'SIS-28','Jl. Demo No. 28','021-0000028',0,6517,28,'2026-07-14 04:02:48',NULL),(29,'RUP-029','Proyek Pengadaan Perangkat','1.679.288.225','Barang','Infrastruktur','UMKM','Seleksi','2026-08-19','Kementerian Kominfo','Organisasi 29','Jakarta 29','Teknologi Informasi','2026','PIC 29','2026-07-15 04:02:48','Seed data untuk testing dashboard RUP.',1,0,0,'SIS-29','Jl. Demo No. 29','021-0000029',0,2787,29,'2026-07-15 04:02:48',NULL),(30,'RUP-030','Proyek Pengadaan Sistem','2.005.313.563','Jasa','Aplikasi','UMKM','Tender','2026-08-20','Badan Pusat Statistik','Organisasi 30','Jakarta 30','Manajemen Proyek','2026','PIC 30','2026-07-16 04:02:48','Seed data untuk testing dashboard RUP.',0,1,0,'SIS-30','Jl. Demo No. 30','021-0000030',1,6840,30,'2026-07-16 04:02:48',NULL),(31,'RUP-031','Proyek Pengadaan Perangkat','1.898.763.529','Barang','Infrastruktur','UMKM','Seleksi','2026-08-21','Kementerian Kominfo','Organisasi 31','Jakarta 31','Teknologi Informasi','2026','PIC 31','2026-07-17 04:02:48','Seed data untuk testing dashboard RUP.',1,0,0,'SIS-31','Jl. Demo No. 31','021-0000031',0,6013,31,'2026-07-17 04:02:48',NULL),(32,'RUP-032','Proyek Pengadaan Perangkat','2.392.956.028','Jasa','Infrastruktur','Perusahaan Teknologi','Seleksi','2026-08-22','Badan Pusat Statistik','Organisasi 32','Jakarta 32','Manajemen Proyek','2026','PIC 32','2026-07-18 04:02:48','Seed data untuk testing dashboard RUP.',0,0,1,'SIS-32','Jl. Demo No. 32','021-0000032',0,5224,32,'2026-07-18 04:02:48',NULL),(33,'RUP-033','Proyek Pengadaan Perangkat','1.193.699.971','Barang','Aplikasi','UMKM','Tender','2026-08-23','Kementerian Kominfo','Organisasi 33','Jakarta 33','Teknologi Informasi','2026','PIC 33','2026-07-19 04:02:48','Seed data untuk testing dashboard RUP.',1,1,0,'SIS-33','Jl. Demo No. 33','021-0000033',0,8687,33,'2026-07-19 04:02:48',NULL),(34,'RUP-034','Proyek Pengadaan Perangkat','1.176.277.855','Jasa','Infrastruktur','UMKM','Seleksi','2026-08-24','Badan Pusat Statistik','Organisasi 34','Jakarta 34','Manajemen Proyek','2026','PIC 34','2026-07-20 04:02:48','Seed data untuk testing dashboard RUP.',0,0,0,'SIS-34','Jl. Demo No. 34','021-0000034',0,9933,34,'2026-07-20 04:02:48',NULL),(35,'RUP-035','Proyek Pengadaan Sistem','828.432.750','Barang','Infrastruktur','UMKM','Seleksi','2026-08-25','Kementerian Kominfo','Organisasi 35','Jakarta 35','Teknologi Informasi','2026','PIC 35','2026-07-21 04:02:48','Seed data untuk testing dashboard RUP.',1,0,0,'SIS-35','Jl. Demo No. 35','021-0000035',1,8973,35,'2026-07-21 04:02:48',NULL);
/*!40000 ALTER TABLE `import_rencana_umum_pengadaan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_07_20_014450_create_personal_access_tokens_table',1),(5,'2026_07_16_000001_create_rup_records_table',2),(6,'2026_07_20_000001_create_n8n_webhook_logs_table',2),(7,'2026_07_21_000001_create_system_notifications_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `n8n_webhook_logs`
--

DROP TABLE IF EXISTS `n8n_webhook_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `n8n_webhook_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `channel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` json DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `customer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'accepted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `n8n_webhook_logs`
--

LOCK TABLES `n8n_webhook_logs` WRITE;
/*!40000 ALTER TABLE `n8n_webhook_logs` DISABLE KEYS */;
INSERT INTO `n8n_webhook_logs` VALUES (1,'n8n','customer_message','web','{\"pagu\": 16650000, \"id_rup\": 64627649, \"nama_instansi\": \"BKKBN\", \"nama_pekerjaan\": \"Penjamin Mutu\", \"tahun_anggaran\": 2026, \"nama_organisasi\": \"Perwakilan BKKBN\", \"lokasi_pekerjaan\": \"Sulawesi Barat\", \"nama_jenis_usaha\": \"UMK\", \"nama_jenis_pengadaan\": \"Jasa Konsultansi lagi\", \"nama_bidang_pekerjaan\": \"Konsultansi\", \"nama_jenis_produk_rup\": \"PDN\", \"nama_metode_pengadaan\": \"E-Purchasing\", \"waktu_pemilihan_penyedia\": \"February\"}','Pesan masuk tanpa isi','unknown','accepted','2026-07-20 21:28:49','2026-07-20 21:28:49');
/*!40000 ALTER TABLE `n8n_webhook_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paket_pengadaan`
--

DROP TABLE IF EXISTS `paket_pengadaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paket_pengadaan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_rup` bigint NOT NULL,
  `nama_pekerjaan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pagu` bigint NOT NULL DEFAULT '0',
  `jenis_pengadaan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `produk_dalam_negeri` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usaha` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metode_pengadaan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bulan_pemilihan` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instansi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `organisasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bidang_pekerjaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` year NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_rup` (`id_rup`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paket_pengadaan`
--

LOCK TABLES `paket_pengadaan` WRITE;
/*!40000 ALTER TABLE `paket_pengadaan` DISABLE KEYS */;
INSERT INTO `paket_pengadaan` VALUES (1,64627648,'Penjamin Mutu Kualitas Sistem Manajemen Anti Penyuapan',16650000,'Jasa Konsultansi','Ya','UMK','E-Purchasing','February','BKKBN','Perwakilan BKKBN Sulawesi Barat','Sulawesi Barat','Konsultansi',2026,'2026-07-14 08:19:50','2026-07-14 08:19:50'),(2,66187047,'Pengadaan Jasa Sertifikasi ISO 37001',36320000,'Jasa Lainnya','Ya','UMK','Pengadaan Langsung','April','Kementerian Keuangan','Ditjen Perbendaharaan','DKI Jakarta','Jasa Lainnya',2026,'2026-07-14 08:19:50','2026-07-14 08:19:50'),(3,62674083,'Belanja Bahan Implementasi SMAP',5000000,'Barang','Ya','UMK','Pengadaan Langsung','April','Kemendikdasmen','Inspektorat Jenderal','DKI Jakarta','Barang',2026,'2026-07-14 08:19:50','2026-07-14 08:19:50'),(4,64949907,'Pendampingan Surveillance ISO 37001',123000000,'Jasa Konsultansi','Ya','UMK','Penunjukan Langsung','June','Komdigi','Ditjen Ekosistem Digital','DKI Jakarta','Konsultansi',2026,'2026-07-14 08:19:50','2026-07-14 08:19:50'),(5,62642846,'Konsultansi Sistem Manajemen Mutu',50000000,'Jasa Konsultansi','Ya','UMK','E-Purchasing','January','Kota Madiun','Disdukcapil','Jawa Timur','Konsultansi',2026,'2026-07-14 08:19:50','2026-07-14 08:19:50'),(6,62838574,'ISO Manajemen Anti Penyuapan',100000000,'Jasa Konsultansi','Ya','UMK','Pengadaan Langsung','January','Kota Probolinggo','DPMPTSP','Jawa Timur','Konsultansi',2026,'2026-07-14 08:19:50','2026-07-14 08:19:50'),(7,63426496,'Pendampingan Pemutakhiran Dokumen ISO',64736000,'Jasa Konsultansi','Ya','UMK','Pengadaan Langsung','July','Kementerian Perhubungan','Distrik Navigasi Tanjung Priok','DKI Jakarta','Konsultansi',2026,'2026-07-14 08:19:50','2026-07-14 08:19:50'),(8,65722493,'Dekorasi Pelatihan Teknis Manajemen Anti Penyuapan',1400000,'Jasa Lainnya','Ya','UMK','Pengadaan Langsung','May','Provinsi Jawa Tengah','BPSDMD','Jawa Tengah','Jasa Lainnya',2026,'2026-07-14 08:19:50','2026-07-14 08:19:50'),(9,64949908,'Sertifikasi Sistem Manajemen Anti Penyuapan',60000000,'Jasa Lainnya','Ya','UMK','Penunjukan Langsung','October','Komdigi','Ditjen Ekosistem Digital','DKI Jakarta','Jasa Lainnya',2026,'2026-07-14 08:19:50','2026-07-14 08:19:50'),(10,67012375,'Pendampingan Sistem Manajemen Anti Penyuapan',100000000,'Jasa Konsultansi','Ya','UMK','Pengadaan Langsung','January','Kota Denpasar','DPMPTSP','Bali','Konsultansi',2026,'2026-07-14 08:19:50','2026-07-14 08:19:50');
/*!40000 ALTER TABLE `paket_pengadaan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('5QmC4OhfSwFM1RAixvKlig43uFj3ehufj2ts42pc',NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiemRWVVdBMTUwQTB6Z0I5bkYybXVqZ2FqTWhzSmVqWTJBTE1ObTBGTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo5OiJkYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1784607549);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_notifications`
--

DROP TABLE IF EXISTS `system_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `payload` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_notifications`
--

LOCK TABLES `system_notifications` WRITE;
/*!40000 ALTER TABLE `system_notifications` DISABLE KEYS */;
INSERT INTO `system_notifications` VALUES (1,'Customer message','Pesan masuk tanpa isi','n8n','medium',NULL,'n8n',0,'{\"pagu\": 16650000, \"id_rup\": 64627649, \"nama_instansi\": \"BKKBN\", \"nama_pekerjaan\": \"Penjamin Mutu\", \"tahun_anggaran\": 2026, \"nama_organisasi\": \"Perwakilan BKKBN\", \"lokasi_pekerjaan\": \"Sulawesi Barat\", \"nama_jenis_usaha\": \"UMK\", \"nama_jenis_pengadaan\": \"Jasa Konsultansi lagi\", \"nama_bidang_pekerjaan\": \"Konsultansi\", \"nama_jenis_produk_rup\": \"PDN\", \"nama_metode_pengadaan\": \"E-Purchasing\", \"waktu_pemilihan_penyedia\": \"February\"}','2026-07-20 21:28:49','2026-07-20 21:28:49');
/*!40000 ALTER TABLE `system_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Test User','test@example.com','2026-07-20 21:02:48','$2y$12$z0zAzAOCAAMUZi1uXh/BvOXK6WSNWyjVOXghSmboipGhBdRPV.7mS','5eBh05lFxl','2026-07-20 21:02:48','2026-07-20 21:02:48');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-24 14:40:13
