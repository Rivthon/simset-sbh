-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 21, 2026 at 06:18 AM
-- Server version: 8.0.46-0ubuntu0.24.04.4
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_simaset_sbh`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `container_id` bigint UNSIGNED DEFAULT NULL,
  `identification_type` enum('individual','group') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'individual',
  `quantity` smallint UNSIGNED NOT NULL DEFAULT '1',
  `satuan` enum('unit','pcs','set','buah','lembar','pasang','box','lainnya') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unit',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `asset_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `legacy_inventory_code` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kondisi_aset` enum('baik','sedang','rusak','hilang') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `jumlah_baik` int UNSIGNED NOT NULL DEFAULT '0',
  `jumlah_sedang` int UNSIGNED NOT NULL DEFAULT '0',
  `jumlah_rusak` int UNSIGNED NOT NULL DEFAULT '0',
  `jumlah_hilang` int UNSIGNED NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `unit_id`, `category_id`, `location_id`, `container_id`, `identification_type`, `quantity`, `satuan`, `created_by`, `asset_code`, `legacy_inventory_code`, `qr_code`, `name`, `kondisi_aset`, `jumlah_baik`, `jumlah_sedang`, `jumlah_rusak`, `jumlah_hilang`, `description`) VALUES
(1, 1, 1, 1, 1, 'group', 9, 'pcs', 3, 'AST-BID-AUK-0001', NULL, 'SIMASET-AST-BIXRWKDVEA', 'Termometer Digital Thermo One', 'baik', 9, 0, 0, 0, 'kalibrasi'),
(2, 1, 1, 1, 1, 'group', 1, 'pcs', 3, 'AST-BID-AUK-0002', NULL, 'SIMASET-AST-07TJTLZL55', 'Termometer Digital Infrared', 'baik', 1, 0, 0, 0, 'kalibrasi'),
(3, 1, 2, 1, 1, 'group', 6, 'pcs', 3, 'AST-BID-APR-0001', NULL, 'SIMASET-AST-OSLAWMKTON', 'Penlight Generalcare Putih', 'baik', 6, 0, 0, 0, NULL),
(4, 1, 2, 1, 1, 'group', 2, 'pcs', 3, 'AST-BID-APR-0002', NULL, 'SIMASET-AST-WRSH34TGI1', 'Penlight Generalcare Biru', 'rusak', 1, 0, 1, 0, NULL),
(5, 1, 1, 1, 1, 'group', 5, 'pcs', 3, 'AST-BID-AUK-0003', NULL, 'SIMASET-AST-9HEZWGB81H', 'Metlin Ping', 'rusak', 4, 0, 1, 0, NULL),
(6, 1, 1, 1, 1, 'group', 1, 'pcs', 3, 'AST-BID-AUK-0004', NULL, 'SIMASET-AST-IPRTLW6YHA', 'Metlin Merah', 'baik', 1, 0, 0, 0, NULL),
(7, 1, 1, 1, 1, 'group', 2, 'pcs', 3, 'AST-BID-AUK-0005', NULL, 'SIMASET-AST-VIOOH6U6HA', 'Tensi Meter Digital Omron', 'sedang', 0, 2, 0, 0, 'Perlu perbaikan/kalibrasi'),
(8, 1, 1, 1, 1, 'group', 5, 'pcs', 3, 'AST-BID-AUK-0006', NULL, 'SIMASET-AST-FFVYVFAFJ6', 'Tensi Meter Manual Onemed Navy', 'baik', 5, 0, 0, 0, 'kalibrasi'),
(9, 1, 1, 1, 1, 'group', 6, 'pcs', 3, 'AST-BID-AUK-0007', NULL, 'SIMASET-AST-AT23TUN9IJ', 'Alat Cek HB Easy Touch (GCHB)', 'sedang', 4, 2, 0, 0, 'Perlu perbaikan/kalibrasi'),
(10, 1, 1, 1, 1, 'group', 2, 'pcs', 3, 'AST-BID-AUK-0008', NULL, 'SIMASET-AST-UXVKSSEASP', 'Alat Cek AU Easy Touch (GCU)', 'baik', 2, 0, 0, 0, 'kalibrasi'),
(11, 1, 1, 1, 1, 'group', 2, 'pcs', 3, 'AST-BID-AUK-0009', NULL, 'SIMASET-AST-OXLGHMIEFW', 'Doppler Omicron Layar', 'sedang', 1, 1, 0, 0, 'kalibrasi'),
(12, 1, 1, 1, 1, 'group', 1, 'pcs', 3, 'AST-BID-AUK-0010', NULL, 'SIMASET-AST-DXVCON1GBU', 'Doppler Bistos Layar', 'baik', 1, 0, 0, 0, 'kalibrasi'),
(13, 1, 1, 1, 1, 'group', 2, 'pcs', 3, 'AST-BID-AUK-0011', NULL, 'SIMASET-AST-DP0MVHNABK', 'Doppler Bistos Tanpa Layar', 'sedang', 0, 2, 0, 0, 'kalibrasi'),
(14, 1, 1, 1, 1, 'group', 6, 'pcs', 3, 'AST-BID-AUK-0012', NULL, 'SIMASET-AST-Y1QOVGTVL4', 'Pulse Oximeter', 'baik', 6, 0, 0, 0, 'kalibrasi'),
(15, 1, 2, 1, 1, 'group', 23, 'pcs', 3, 'AST-BID-APR-0003', NULL, 'SIMASET-AST-61QUWFEETG', 'Palu Hammer', 'sedang', 19, 4, 0, 0, NULL),
(16, 1, 4, 1, 1, 'group', 15, 'pcs', 3, 'AST-BID-MIN-0001', NULL, 'SIMASET-AST-17OOL8ES6I', 'Tongue Spatel Besi', 'baik', 15, 0, 0, 0, NULL),
(17, 1, 4, 1, 1, 'group', 15, 'pcs', 3, 'AST-BID-MIN-0002', NULL, 'SIMASET-AST-CGQWF7JLA9', 'Leneck Besi', 'sedang', 12, 3, 0, 0, NULL),
(18, 1, 4, 1, 1, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0003', NULL, 'SIMASET-AST-NHKQE3C96Q', 'Korentang+Tempat', 'baik', 5, 0, 0, 0, NULL),
(19, 1, 4, 1, 1, 'group', 1, 'pcs', 3, 'AST-BID-MIN-0004', NULL, 'SIMASET-AST-HMTHCYJERN', 'Alat Ukur Panggul', 'baik', 1, 0, 0, 0, NULL),
(20, 1, 1, 1, 1, 'group', 10, 'pcs', 3, 'AST-BID-AUK-0013', NULL, 'SIMASET-AST-F0EDFDSOKP', 'Pita LILA', 'baik', 10, 0, 0, 0, NULL),
(21, 1, 4, 1, 1, 'group', 10, 'pcs', 3, 'AST-BID-MIN-0005', NULL, 'SIMASET-AST-P0WATG41H4', 'Kom Sedang', 'baik', 10, 0, 0, 0, NULL),
(22, 1, 4, 1, 1, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0006', NULL, 'SIMASET-AST-WTEJ4GFNMS', 'Kom Tutup Kassa', 'baik', 5, 0, 0, 0, NULL),
(23, 1, 4, 1, 1, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0007', NULL, 'SIMASET-AST-SJENFLHBFF', 'Kom Tutup Tissu', 'baik', 5, 0, 0, 0, NULL),
(24, 1, 4, 1, 1, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0008', NULL, 'SIMASET-AST-61VM9S9O2P', 'Garpu Tala', 'baik', 5, 0, 0, 0, NULL),
(25, 1, 2, 1, 1, 'group', 8, 'pcs', 3, 'AST-BID-APR-0004', NULL, 'SIMASET-AST-RZQYHYKRYX', 'Baki Biru', 'baik', 8, 0, 0, 0, NULL),
(26, 1, 1, 1, 1, 'group', 4, 'pcs', 3, 'AST-BID-AUK-0014', NULL, 'SIMASET-AST-SJXUPZCQUI', 'Timbangan Dewasa Manual', 'sedang', 0, 4, 0, 0, 'perlu kalibrasi'),
(27, 1, 4, 1, 1, 'group', 20, 'pcs', 3, 'AST-BID-MIN-0009', NULL, 'SIMASET-AST-VI4AT556W3', 'Bengkok', 'rusak', 17, 0, 3, 0, 'perlu penggantian baru'),
(28, 1, 4, 1, 1, 'group', 10, 'pcs', 3, 'AST-BID-MIN-0010', NULL, 'SIMASET-AST-KFQFMCAMQA', 'Bak Instrumen Sedang', 'baik', 10, 0, 0, 0, NULL),
(29, 1, 2, 1, 1, 'group', 6, 'pcs', 3, 'AST-BID-APR-0005', NULL, 'SIMASET-AST-UQQADORKDZ', 'Bak Klorin Abu', 'baik', 6, 0, 0, 0, NULL),
(30, 1, 1, 1, 2, 'group', 1, 'pcs', 3, 'AST-BID-AUK-0015', NULL, 'SIMASET-AST-XPLTBELRHR', 'Tensimeter Manual Onemed Ping', 'sedang', 0, 1, 0, 0, 'kalibrasi'),
(31, 1, 1, 1, 2, 'group', 1, 'pcs', 3, 'AST-BID-AUK-0016', NULL, 'SIMASET-AST-E8QPP6UJUW', 'Tensimeter Digital Omicron', 'sedang', 0, 1, 0, 0, 'perlu perbaikan/ganti baru'),
(32, 1, 2, 1, 2, 'group', 5, 'pcs', 3, 'AST-BID-APR-0006', NULL, 'SIMASET-AST-WYBKNBSUOB', 'Penlight Onemed', 'sedang', 3, 2, 0, 0, 'perlu perbaikan/ganti baru'),
(33, 1, 2, 1, 2, 'group', 3, 'pcs', 3, 'AST-BID-APR-0007', NULL, 'SIMASET-AST-8AWTR7VZD3', 'Penlight Generalcare', 'rusak', 0, 2, 1, 0, NULL),
(34, 1, 1, 1, 2, 'group', 5, 'pcs', 3, 'AST-BID-AUK-0017', NULL, 'SIMASET-AST-AF8HXSTKEM', 'Termometer Air Raksa', 'sedang', 0, 5, 0, 0, 'perlu perbaikan'),
(35, 1, 1, 1, 2, 'group', 5, 'pcs', 3, 'AST-BID-AUK-0018', NULL, 'SIMASET-AST-MARELCIEM1', 'Termometer Digital Dr. Care', 'sedang', 2, 3, 0, 0, 'kalibrasi'),
(36, 1, 1, 1, 2, 'group', 1, 'pcs', 3, 'AST-BID-AUK-0019', NULL, 'SIMASET-AST-88RP1TQGEQ', 'Termometer Tembak', 'baik', 1, 0, 0, 0, NULL),
(37, 1, 1, 1, 2, 'group', 4, 'pcs', 3, 'AST-BID-AUK-0020', NULL, 'SIMASET-AST-9PX6RDBCZW', 'Metlin Kuning', 'sedang', 0, 4, 0, 0, 'perlu kalibrasi'),
(38, 1, 1, 1, 2, 'group', 3, 'pcs', 3, 'AST-BID-AUK-0021', NULL, 'SIMASET-AST-FQDQ13VZAK', 'Dopler Lotus 3', 'sedang', 0, 3, 0, 0, NULL),
(39, 1, 4, 1, 2, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0011', NULL, 'SIMASET-AST-PKVYV0HPGQ', 'Korentang + Tempat', 'baik', 5, 0, 0, 0, NULL),
(40, 1, 2, 1, 2, 'group', 15, 'pcs', 3, 'AST-BID-APR-0008', NULL, 'SIMASET-AST-UPBODV9NTH', 'Leneck Kayu', 'sedang', 5, 10, 0, 0, NULL),
(41, 1, 4, 1, 2, 'group', 29, 'pcs', 3, 'AST-BID-MIN-0012', NULL, 'SIMASET-AST-9N6VVX0S3S', 'Gunting Tali Pusat', 'baik', 29, 0, 0, 0, NULL),
(42, 1, 4, 1, 2, 'group', 17, 'pcs', 3, 'AST-BID-MIN-0013', NULL, 'SIMASET-AST-9IKCH9EVZ9', 'Gunting Episiotomi', 'baik', 17, 0, 0, 0, NULL),
(43, 1, 4, 1, 2, 'group', 27, 'pcs', 3, 'AST-BID-MIN-0014', NULL, 'SIMASET-AST-QBL0PZK46G', 'Setengah Koher', 'baik', 27, 0, 0, 0, NULL),
(44, 1, 4, 1, 2, 'group', 17, 'pcs', 3, 'AST-BID-MIN-0015', NULL, 'SIMASET-AST-4QCRV4NEGQ', 'Klem Arteri Pean Lurus', 'baik', 17, 0, 0, 0, NULL),
(45, 1, 4, 1, 2, 'group', 19, 'pcs', 3, 'AST-BID-MIN-0016', NULL, 'SIMASET-AST-0WWG1MZF0U', 'Klem Arteri Kocher/ Sirurgis', 'baik', 19, 0, 0, 0, NULL),
(46, 1, 4, 1, 2, 'group', 16, 'pcs', 3, 'AST-BID-MIN-0017', NULL, 'SIMASET-AST-PCMYANPDYQ', 'Naldpuder', 'baik', 16, 0, 0, 0, NULL),
(47, 1, 4, 1, 2, 'group', 17, 'pcs', 3, 'AST-BID-MIN-0018', NULL, 'SIMASET-AST-BW9TBMOQL9', 'Pinset Sirurgis', 'baik', 17, 0, 0, 0, NULL),
(48, 1, 4, 1, 2, 'group', 17, 'pcs', 3, 'AST-BID-MIN-0019', NULL, 'SIMASET-AST-WG84TOSLYM', 'Pinset Anatomis', 'baik', 17, 0, 0, 0, NULL),
(49, 1, 4, 1, 2, 'group', 12, 'pcs', 3, 'AST-BID-MIN-0020', NULL, 'SIMASET-AST-A1AALUAYJV', 'Gunting Hecting', 'baik', 12, 0, 0, 0, NULL),
(50, 1, 4, 1, 2, 'group', 38, 'pcs', 3, 'AST-BID-MIN-0021', NULL, 'SIMASET-AST-24XMDZ57UT', 'Kateter Nelaton/Nilon', 'baik', 38, 0, 0, 0, NULL),
(51, 1, 4, 1, 2, 'group', 10, 'pcs', 3, 'AST-BID-MIN-0022', NULL, 'SIMASET-AST-WGV9DH8TXV', 'Kateter Besi', 'baik', 10, 0, 0, 0, 'perlu penggantian baru'),
(52, 1, 2, 1, 2, 'group', 13, 'pcs', 3, 'AST-BID-APR-0009', NULL, 'SIMASET-AST-NZ4QUFMSVY', 'Mokus', 'rusak', 8, 4, 1, 0, NULL),
(53, 1, 4, 1, 2, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0023', NULL, 'SIMASET-AST-N9XU2RJBQS', 'Kom Tutup Kassa', 'baik', 5, 0, 0, 0, NULL),
(54, 1, 4, 1, 2, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0024', NULL, 'SIMASET-AST-IMWUCLHXML', 'Kom Tutup Tissu', 'baik', 5, 0, 0, 0, NULL),
(55, 1, 4, 1, 2, 'group', 7, 'pcs', 3, 'AST-BID-MIN-0025', NULL, 'SIMASET-AST-BEWTNYBOML', 'Kom Kecil', 'baik', 7, 0, 0, 0, NULL),
(56, 1, 4, 1, 2, 'group', 10, 'pcs', 3, 'AST-BID-MIN-0026', NULL, 'SIMASET-AST-GLH6XK1WWT', 'Kom Sedang', 'baik', 10, 0, 0, 0, NULL),
(57, 1, 4, 1, 2, 'group', 10, 'pcs', 3, 'AST-BID-MIN-0027', NULL, 'SIMASET-AST-B2RKJF9DPI', 'Bengkok', 'baik', 10, 0, 0, 0, NULL),
(58, 1, 2, 1, 2, 'group', 9, 'pcs', 3, 'AST-BID-APR-0010', NULL, 'SIMASET-AST-WFRDCJHKAR', 'Piring Plasenta', 'baik', 9, 0, 0, 0, NULL),
(59, 1, 2, 1, 2, 'group', 3, 'pcs', 3, 'AST-BID-APR-0011', NULL, 'SIMASET-AST-7U9NS9T7FF', 'Baki  Merah', 'baik', 3, 0, 0, 0, NULL),
(60, 1, 2, 1, 2, 'group', 5, 'pcs', 3, 'AST-BID-APR-0012', NULL, 'SIMASET-AST-98KISCXUDM', 'Baki Ping', 'baik', 5, 0, 0, 0, NULL),
(61, 1, 4, 1, 2, 'group', 8, 'pcs', 3, 'AST-BID-MIN-0028', NULL, 'SIMASET-AST-UKSJPWFOG0', 'Baskom Besar', 'baik', 8, 0, 0, 0, NULL),
(62, 1, 4, 1, 2, 'group', 9, 'pcs', 3, 'AST-BID-MIN-0029', NULL, 'SIMASET-AST-O47CXOCG3W', 'Baskom Sedang', 'baik', 9, 0, 0, 0, NULL),
(63, 1, 4, 1, 2, 'group', 4, 'pcs', 3, 'AST-BID-MIN-0030', NULL, 'SIMASET-AST-NTILZWTX7E', 'Bak Klorin Merah', 'baik', 4, 0, 0, 0, NULL),
(64, 1, 4, 1, 2, 'group', 10, 'pcs', 3, 'AST-BID-MIN-0031', NULL, 'SIMASET-AST-MHHB9JHKRF', 'Bak Instrumen Sedang', 'baik', 10, 0, 0, 0, NULL),
(65, 1, 4, 1, 2, 'group', 10, 'pcs', 3, 'AST-BID-MIN-0032', NULL, 'SIMASET-AST-Z6NI9Z3V1P', 'Bak Instrumen Kecil', 'baik', 10, 0, 0, 0, NULL),
(66, 1, 1, 1, 2, 'group', 10, 'pcs', 3, 'AST-BID-AUK-0022', NULL, 'SIMASET-AST-V6LA4JBWUP', 'Pita LILA', 'sedang', 5, 5, 0, 0, NULL),
(67, 1, 2, 1, 2, 'group', 5, 'pcs', 3, 'AST-BID-APR-0013', NULL, 'SIMASET-AST-A68HA1ZMAP', 'Tourniquet', 'baik', 5, 0, 0, 0, NULL),
(68, 1, 4, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0033', NULL, 'SIMASET-AST-KIYDXUHDWA', 'Korentang + Tempat', 'baik', 5, 0, 0, 0, NULL),
(69, 1, 4, 1, 3, 'group', 10, 'pcs', 3, 'AST-BID-MIN-0034', NULL, 'SIMASET-AST-YFOBO6RXZ4', 'Kom Sedang', 'baik', 10, 0, 0, 0, NULL),
(70, 1, 4, 1, 3, 'group', 10, 'pcs', 3, 'AST-BID-MIN-0035', NULL, 'SIMASET-AST-TQLNIIX4PS', 'Bengkok', 'baik', 10, 0, 0, 0, NULL),
(71, 1, 4, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0036', NULL, 'SIMASET-AST-ALMBZVWLCQ', 'Kom Tutup Tissu', 'baik', 5, 0, 0, 0, NULL),
(72, 1, 4, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0037', NULL, 'SIMASET-AST-GHLFCVH8KS', 'Kom Tutup Kassa', 'baik', 5, 0, 0, 0, NULL),
(73, 1, 2, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-APR-0014', NULL, 'SIMASET-AST-KCXCE18ZMK', 'Pumping Manual', 'baik', 5, 0, 0, 0, NULL),
(74, 1, 4, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0038', NULL, 'SIMASET-AST-PIAQEQM5QT', 'Bak Instrumen Sedang', 'baik', 5, 0, 0, 0, NULL),
(75, 1, 2, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-APR-0015', NULL, 'SIMASET-AST-Q6HSGAZSLZ', 'Cup Feeder', 'baik', 5, 0, 0, 0, NULL),
(76, 1, 1, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-AUK-0023', NULL, 'SIMASET-AST-H16FZWHADC', 'Termometer Air Raksa', 'sedang', 0, 5, 0, 0, NULL),
(77, 1, 1, 1, 3, 'group', 6, 'pcs', 3, 'AST-BID-AUK-0024', NULL, 'SIMASET-AST-BTZ3N3IXSM', 'Pulse Oximeter', 'baik', 6, 0, 0, 0, 'kalibrasi'),
(78, 1, 1, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-AUK-0025', NULL, 'SIMASET-AST-GWWWBZCZM5', 'Tongue Spatel', 'baik', 5, 0, 0, 0, NULL),
(79, 1, 2, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-APR-0016', NULL, 'SIMASET-AST-CB8AKMJ5LZ', 'Nipple Sheild Besar', 'baik', 5, 0, 0, 0, NULL),
(80, 1, 2, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-APR-0017', NULL, 'SIMASET-AST-BV7H2E1G8E', 'Nipple Sheild Kecil', 'baik', 5, 0, 0, 0, NULL),
(81, 1, 2, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-APR-0018', NULL, 'SIMASET-AST-D30TBAJFQD', 'Nipple Puller', 'baik', 5, 0, 0, 0, NULL),
(82, 1, 2, 1, 3, 'group', 4, 'pcs', 3, 'AST-BID-APR-0019', NULL, 'SIMASET-AST-9XYPYPS1CT', 'Bak Klorin Biru', 'baik', 4, 0, 0, 0, NULL),
(83, 1, 2, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-APR-0020', NULL, 'SIMASET-AST-WJZW2KTSMF', 'Baki Biru', 'baik', 5, 0, 0, 0, NULL),
(84, 1, 2, 1, 3, 'group', 3, 'pcs', 3, 'AST-BID-APR-0021', NULL, 'SIMASET-AST-XTAXCHRLMX', 'Baki Oren', 'baik', 3, 0, 0, 0, NULL),
(85, 1, 1, 1, 3, 'group', 4, 'pcs', 3, 'AST-BID-AUK-0026', NULL, 'SIMASET-AST-PIBAAOBYPC', 'Timbangan Dewasa Manual', 'sedang', 0, 4, 0, 0, 'perlu kalibrasi'),
(86, 1, 2, 1, 3, 'group', 4, 'pcs', 3, 'AST-BID-APR-0022', NULL, 'SIMASET-AST-SMCQPUBBKL', 'Baskom Besar', 'sedang', 0, 4, 0, 0, NULL),
(87, 1, 1, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-AUK-0027', NULL, 'SIMASET-AST-PKP8AXANRU', 'Tensi Meter Manual Onemed Navy', 'baik', 5, 0, 0, 0, 'kalibrasi'),
(88, 1, 4, 1, 4, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0039', NULL, 'SIMASET-AST-VEJAWVNTSX', 'Korentang + Tempat', 'baik', 5, 0, 0, 0, NULL),
(89, 1, 4, 1, 4, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0040', NULL, 'SIMASET-AST-ES94QXFCYY', 'Kom Tutup Tissu', 'baik', 5, 0, 0, 0, NULL),
(90, 1, 4, 1, 4, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0041', NULL, 'SIMASET-AST-LHV1V6V6JS', 'Kom Tutup Kassa', 'baik', 5, 0, 0, 0, NULL),
(91, 1, 4, 1, 4, 'group', 12, 'pcs', 3, 'AST-BID-MIN-0042', NULL, 'SIMASET-AST-PB9CGOIZFX', 'Nasal Spekulum', 'baik', 12, 0, 0, 0, NULL),
(92, 1, 4, 1, 4, 'group', 24, 'pcs', 3, 'AST-BID-MIN-0043', NULL, 'SIMASET-AST-DLRE6LWF8O', 'Gunting Benang', 'baik', 24, 0, 0, 0, NULL),
(93, 1, 4, 1, 4, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0044', NULL, 'SIMASET-AST-E9EY7UM7BF', 'Garpu Tala', 'baik', 5, 0, 0, 0, NULL),
(94, 1, 4, 1, 4, 'group', 9, 'pcs', 3, 'AST-BID-MIN-0045', NULL, 'SIMASET-AST-YY4RCQICQT', 'Gunting Iris Bengkok', 'baik', 9, 0, 0, 0, NULL),
(95, 1, 4, 1, 4, 'group', 19, 'pcs', 3, 'AST-BID-MIN-0046', NULL, 'SIMASET-AST-WXZABIZ8CW', 'Gunting Episiotomi', 'baik', 19, 0, 0, 0, NULL),
(96, 1, 4, 1, 4, 'group', 3, 'pcs', 3, 'AST-BID-MIN-0047', NULL, 'SIMASET-AST-UB1DKI8KAM', 'Naldpuder', 'sedang', 0, 3, 0, 0, 'perlu pengajuan alat'),
(97, 1, 2, 1, 4, 'group', 5, 'pcs', 3, 'AST-BID-APR-0023', NULL, 'SIMASET-AST-4KCLL1XP9H', 'Penlight Telinga', 'baik', 5, 0, 0, 0, NULL),
(98, 1, 2, 1, 4, 'group', 3, 'pcs', 3, 'AST-BID-APR-0024', NULL, 'SIMASET-AST-I7AGJQTN21', 'Penlight Generalcare merah', 'baik', 3, 0, 0, 0, NULL),
(99, 1, 2, 1, 4, 'group', 5, 'pcs', 3, 'AST-BID-APR-0025', NULL, 'SIMASET-AST-RRXVD2JDUK', 'Penlight Generalcare hitam', 'sedang', 1, 4, 0, 0, NULL),
(100, 1, 1, 1, 4, 'group', 5, 'pcs', 3, 'AST-BID-AUK-0028', NULL, 'SIMASET-AST-MSKZNXXJB0', 'Tensimeter Manual', 'sedang', 0, 5, 0, 0, 'perlu kalibrasi'),
(101, 1, 2, 1, 4, 'group', 5, 'pcs', 3, 'AST-BID-APR-0026', NULL, 'SIMASET-AST-W3Z36N0KZH', 'Sisir', 'sedang', 2, 3, 0, 0, NULL),
(102, 1, 2, 1, 4, 'group', 3, 'pcs', 3, 'AST-BID-APR-0027', NULL, 'SIMASET-AST-Q7NXDRSMNK', 'Gunting Kuku', 'baik', 3, 0, 0, 0, NULL),
(103, 1, 4, 1, 4, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0048', NULL, 'SIMASET-AST-VCQ2DPQUGO', 'Pinset Anatomis', 'baik', 5, 0, 0, 0, NULL),
(104, 1, 4, 1, 4, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0049', NULL, 'SIMASET-AST-3GBQ2TJSOU', 'Pinset Sirurgis', 'baik', 5, 0, 0, 0, NULL),
(105, 1, 2, 1, 4, 'group', 6, 'pcs', 3, 'AST-BID-APR-0028', NULL, 'SIMASET-AST-MVSYGKTRAP', 'Torniquet', 'baik', 6, 0, 0, 0, NULL),
(106, 1, 4, 1, 4, 'group', 7, 'pcs', 3, 'AST-BID-MIN-0050', NULL, 'SIMASET-AST-EGA3LREOUB', 'Klisma Gliserin', 'sedang', 6, 1, 0, 0, NULL),
(107, 1, 4, 1, 4, 'group', 16, 'pcs', 3, 'AST-BID-MIN-0051', NULL, 'SIMASET-AST-TBDETZTOBW', 'Bengkok', 'sedang', 0, 16, 0, 0, NULL),
(108, 1, 2, 1, 4, 'group', 4, 'pcs', 3, 'AST-BID-APR-0029', NULL, 'SIMASET-AST-RT1A2SIHBU', 'Buli-buli', 'sedang', 0, 4, 0, 0, NULL),
(109, 1, 2, 1, 4, 'group', 21, 'pcs', 3, 'AST-BID-APR-0030', NULL, 'SIMASET-AST-AM5YHLAKYQ', 'Sikat Gigi', 'sedang', 20, 1, 0, 0, NULL),
(110, 1, 4, 1, 4, 'group', 19, 'pcs', 3, 'AST-BID-MIN-0052', NULL, 'SIMASET-AST-75VQUMGOPV', 'Kom Sedang', 'baik', 19, 0, 0, 0, NULL),
(111, 1, 1, 1, 4, 'group', 2, 'pcs', 3, 'AST-BID-AUK-0029', NULL, 'SIMASET-AST-IQFNBIDYJP', 'Alat Cek Easy Touch GCHB', 'sedang', 0, 2, 0, 0, NULL),
(112, 1, 1, 1, 4, 'group', 2, 'pcs', 3, 'AST-BID-AUK-0030', NULL, 'SIMASET-AST-TZVCAYIU7Q', 'Alat Cek Easy Touch GCU', 'sedang', 0, 2, 0, 0, NULL),
(113, 1, 2, 1, 5, 'group', 4, 'pcs', 3, 'AST-BID-APR-0031', NULL, 'SIMASET-AST-XHTQ5BAIHE', 'Pompaan', 'baik', 4, 0, 0, 0, NULL),
(114, 1, 2, 1, 5, 'group', 11, 'pcs', 3, 'AST-BID-APR-0032', NULL, 'SIMASET-AST-J7CUCRRAGX', 'Baskom Plastik', 'baik', 11, 0, 0, 0, NULL),
(115, 1, 2, 1, 5, 'group', 10, 'pcs', 3, 'AST-BID-APR-0033', NULL, 'SIMASET-AST-4IGXEDB77Y', 'Piring Plastik', 'baik', 10, 0, 0, 0, NULL),
(116, 1, 2, 1, 5, 'group', 3, 'pcs', 3, 'AST-BID-APR-0034', NULL, 'SIMASET-AST-HJT9T9J8WZ', 'Baskom Stainles', 'sedang', 0, 3, 0, 0, NULL),
(117, 1, 4, 1, 7, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0053', NULL, 'SIMASET-AST-JOHHNVDROP', 'Kom Tutupt Tissu', 'baik', 5, 0, 0, 0, NULL),
(118, 1, 4, 1, 7, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0054', NULL, 'SIMASET-AST-9QUQNEW8JZ', 'Kom Tutup Kassa', 'baik', 5, 0, 0, 0, NULL),
(119, 1, 1, 1, 7, 'group', 2, 'pcs', 3, 'AST-BID-AUK-0031', NULL, 'SIMASET-AST-KO9RQVGKN7', 'Metlin Biru', 'baik', 2, 0, 0, 0, NULL),
(120, 1, 1, 1, 7, 'group', 2, 'pcs', 3, 'AST-BID-AUK-0032', NULL, 'SIMASET-AST-UZ3ODXEVBG', 'Metlin Merah', 'baik', 2, 0, 0, 0, NULL),
(121, 1, 2, 1, 7, 'group', 2, 'pcs', 3, 'AST-BID-APR-0035', NULL, 'SIMASET-AST-CVARICJ80D', 'Penlight General Biru', 'baik', 2, 0, 0, 0, NULL),
(122, 1, 2, 1, 7, 'group', 1, 'pcs', 3, 'AST-BID-APR-0036', NULL, 'SIMASET-AST-WWRCPKMDFH', 'Penlight General Putih', 'baik', 1, 0, 0, 0, NULL),
(123, 1, 2, 1, 7, 'group', 1, 'pcs', 3, 'AST-BID-APR-0037', NULL, 'SIMASET-AST-GN43WOULWU', 'Penlight General Hitam', 'baik', 1, 0, 0, 0, NULL),
(124, 1, 4, 1, 7, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0055', NULL, 'SIMASET-AST-VOZUMGLFZV', 'Korentang + Tempat', 'baik', 5, 0, 0, 0, NULL),
(125, 1, 1, 1, 7, 'group', 14, 'pcs', 3, 'AST-BID-AUK-0033', NULL, 'SIMASET-AST-MXYV8PYZMQ', 'Pita LILA Bayi', 'sedang', 0, 14, 0, 0, NULL),
(126, 1, 4, 1, 7, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0056', NULL, 'SIMASET-AST-96AUEAJSTO', 'Kom Sedang', 'sedang', 0, 5, 0, 0, NULL),
(127, 1, 4, 1, 7, 'group', 10, 'pcs', 3, 'AST-BID-MIN-0057', NULL, 'SIMASET-AST-ZF4QO1MWLJ', 'Bak Instrumen Kecil', 'baik', 10, 0, 0, 0, NULL),
(128, 1, 1, 1, 7, 'group', 16, 'pcs', 3, 'AST-BID-AUK-0034', NULL, 'SIMASET-AST-NE7FQU5MO9', 'Meteran', 'sedang', 0, 16, 0, 0, NULL),
(129, 1, 1, 1, 7, 'group', 6, 'pcs', 3, 'AST-BID-AUK-0035', NULL, 'SIMASET-AST-6B7VGBDA2N', 'Termometer Air Raksa', 'sedang', 0, 6, 0, 0, NULL),
(130, 1, 2, 1, 7, 'group', 9, 'pcs', 3, 'AST-BID-APR-0038', NULL, 'SIMASET-AST-G6SUYDYBVZ', 'Tempat Bedak', 'sedang', 0, 9, 0, 0, NULL),
(131, 1, 2, 1, 7, 'group', 8, 'pcs', 3, 'AST-BID-APR-0039', NULL, 'SIMASET-AST-XQY6RELABT', 'Tempat Sabun', 'sedang', 0, 8, 0, 0, NULL),
(132, 1, 2, 1, 7, 'group', 2, 'pcs', 3, 'AST-BID-APR-0040', NULL, 'SIMASET-AST-FVSGTWPQVX', 'Sisir Bayi', 'sedang', 0, 2, 0, 0, NULL),
(133, 1, 1, 1, 7, 'group', 5, 'pcs', 3, 'AST-BID-AUK-0036', NULL, 'SIMASET-AST-OXQ7S1ZKPL', 'Alat Ukur Bayi WB-C', 'baik', 5, 0, 0, 0, NULL),
(134, 1, 1, 1, 7, 'group', 6, 'pcs', 3, 'AST-BID-AUK-0037', NULL, 'SIMASET-AST-NSRTIAEQ8W', 'Alat Ukur Bayi Penggaris', 'baik', 6, 0, 0, 0, NULL),
(135, 1, 1, 1, 7, 'group', 8, 'pcs', 3, 'AST-BID-AUK-0038', NULL, 'SIMASET-AST-LIATKEOM9Y', 'Resusitasi Bayi', 'sedang', 0, 8, 0, 0, NULL),
(136, 1, 1, 1, 7, 'group', 6, 'pcs', 3, 'AST-BID-AUK-0039', NULL, 'SIMASET-AST-9T4OYDQY5N', 'Resusitasi Dewasa', 'sedang', 0, 6, 0, 0, NULL),
(137, 1, 2, 1, 7, 'group', 8, 'pcs', 3, 'AST-BID-APR-0041', NULL, 'SIMASET-AST-VPUM9OY80S', 'Mainan Silicon', 'sedang', 0, 8, 0, 0, NULL),
(138, 1, 4, 1, 8, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0058', NULL, 'SIMASET-AST-WMR9OORBOM', 'Kom Tutup Kassa', 'baik', 5, 0, 0, 0, NULL),
(139, 1, 4, 1, 8, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0059', NULL, 'SIMASET-AST-NSZD9RXR8A', 'Kom Tutup Tissu', 'baik', 5, 0, 0, 0, NULL),
(140, 1, 4, 1, 8, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0060', NULL, 'SIMASET-AST-M8LIEVWDBZ', 'Korentang + Tempat', 'baik', 5, 0, 0, 0, NULL),
(141, 1, 4, 1, 8, 'group', 9, 'pcs', 3, 'AST-BID-MIN-0061', NULL, 'SIMASET-AST-CHYCWLTRNF', 'Klem U', 'sedang', 8, 1, 0, 0, NULL),
(142, 1, 4, 1, 8, 'group', 22, 'pcs', 3, 'AST-BID-MIN-0062', NULL, 'SIMASET-AST-IQRVWWXC8X', 'Klem Lengkung Sedang', 'baik', 22, 0, 0, 0, NULL),
(143, 1, 4, 1, 8, 'group', 13, 'pcs', 3, 'AST-BID-MIN-0063', NULL, 'SIMASET-AST-RPDLUIQLOT', 'Klem Lengkung Kecil', 'baik', 13, 0, 0, 0, NULL),
(144, 1, 4, 1, 8, 'group', 8, 'pcs', 3, 'AST-BID-MIN-0064', NULL, 'SIMASET-AST-B7M55CDBDO', 'Klem Biasa', 'sedang', 3, 5, 0, 0, NULL),
(145, 1, 4, 1, 8, 'group', 14, 'pcs', 3, 'AST-BID-MIN-0065', NULL, 'SIMASET-AST-RVTKCJDVSU', 'Skapel', 'sedang', 0, 14, 0, 0, NULL),
(146, 1, 4, 1, 8, 'group', 6, 'pcs', 3, 'AST-BID-MIN-0066', NULL, 'SIMASET-AST-F3TZGZCELK', 'Trokat', 'baik', 6, 0, 0, 0, NULL),
(147, 1, 4, 1, 8, 'group', 10, 'pcs', 3, 'AST-BID-MIN-0067', NULL, 'SIMASET-AST-JM4GBTBJ2T', 'Bak Instrumen Sedang', 'baik', 10, 0, 0, 0, NULL),
(148, 1, 4, 1, 8, 'group', 10, 'pcs', 3, 'AST-BID-MIN-0068', NULL, 'SIMASET-AST-KJV0IMXE4D', 'Bak Instrumen Besar', 'baik', 10, 0, 0, 0, NULL),
(149, 1, 4, 1, 8, 'group', 12, 'pcs', 3, 'AST-BID-MIN-0069', NULL, 'SIMASET-AST-WBGTUOJJH0', 'Spekulum Cocor Bebek', 'baik', 12, 0, 0, 0, NULL),
(150, 1, 4, 1, 8, 'group', 12, 'pcs', 3, 'AST-BID-MIN-0070', NULL, 'SIMASET-AST-7E1LAI1W76', 'Spekulum Sims', 'baik', 12, 0, 0, 0, NULL),
(151, 1, 4, 1, 8, 'group', 4, 'pcs', 3, 'AST-BID-MIN-0071', NULL, 'SIMASET-AST-KMJWFWGYA4', 'Pinset Panjang IUD', 'baik', 4, 0, 0, 0, NULL),
(152, 1, 4, 1, 8, 'group', 7, 'pcs', 3, 'AST-BID-MIN-0072', NULL, 'SIMASET-AST-OA1UVLN1RB', 'Klem Kassa / Venster Klem', 'baik', 7, 0, 0, 0, NULL),
(153, 1, 4, 1, 8, 'group', 11, 'pcs', 3, 'AST-BID-MIN-0073', NULL, 'SIMASET-AST-GBPLRY9YWH', 'Klem Biasa Panjang', 'baik', 11, 0, 0, 0, NULL),
(154, 1, 4, 1, 8, 'group', 18, 'pcs', 3, 'AST-BID-MIN-0074', NULL, 'SIMASET-AST-YW6TWHP8H4', 'Gunting Panjang IUD', 'sedang', 0, 18, 0, 0, NULL),
(155, 1, 4, 1, 8, 'group', 18, 'pcs', 3, 'AST-BID-MIN-0075', NULL, 'SIMASET-AST-TLDCYHJGT5', 'Klem Bengkok Panjang/ Tampontang', 'baik', 18, 0, 0, 0, NULL),
(156, 1, 4, 1, 8, 'group', 12, 'pcs', 3, 'AST-BID-MIN-0076', NULL, 'SIMASET-AST-7PPOZWVI9G', 'Tenakulum', 'baik', 12, 0, 0, 0, NULL),
(157, 1, 4, 1, 8, 'group', 14, 'pcs', 3, 'AST-BID-MIN-0077', NULL, 'SIMASET-AST-A9DCSOU1JS', 'Sonde', 'baik', 14, 0, 0, 0, NULL),
(158, 1, 4, 1, 8, 'group', 16, 'pcs', 3, 'AST-BID-MIN-0078', NULL, 'SIMASET-AST-FMCCCETDSE', 'Aligator', 'baik', 16, 0, 0, 0, NULL),
(159, 1, 4, 1, 8, 'group', 11, 'pcs', 3, 'AST-BID-MIN-0079', NULL, 'SIMASET-AST-Z4QYP3HFDM', 'Sendok Kuret', 'baik', 11, 0, 0, 0, NULL),
(160, 1, 4, 1, 8, 'group', 6, 'pcs', 3, 'AST-BID-MIN-0080', NULL, 'SIMASET-AST-OKLRLY8BC7', 'Tampontang Fenstrer', 'baik', 6, 0, 0, 0, NULL),
(161, 1, 2, 1, 8, 'group', 6, 'pcs', 3, 'AST-BID-APR-0042', NULL, 'SIMASET-AST-UOCFB7WDZV', 'Baki Hijau', 'baik', 6, 0, 0, 0, NULL),
(162, 1, 2, 1, 8, 'group', 4, 'pcs', 3, 'AST-BID-APR-0043', NULL, 'SIMASET-AST-SNJMXWEYSL', 'Baki Biru', 'baik', 4, 0, 0, 0, NULL),
(163, 1, 4, 1, 8, 'group', 10, 'pcs', 3, 'AST-BID-MIN-0081', NULL, 'SIMASET-AST-F3V38S9AMP', 'Bengkok', 'sedang', 0, 10, 0, 0, NULL),
(164, 1, 4, 1, 8, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0082', NULL, 'SIMASET-AST-6KEGAPL5H9', 'Kom Sedang', 'sedang', 0, 5, 0, 0, NULL),
(165, 1, 2, 1, 8, 'group', 6, 'pcs', 3, 'AST-BID-APR-0044', NULL, 'SIMASET-AST-RNA9YRH2Z1', 'Headlamp', 'baik', 6, 0, 0, 0, 'KB'),
(166, 1, 5, 1, 9, 'group', 5, 'pcs', 3, 'AST-BID-APR-0045', NULL, 'SIMASET-AST-LXZ345KGYR', 'Phantom IUD', 'rusak', 0, 3, 2, 0, 'KDK'),
(167, 1, 5, 1, 9, 'group', 6, 'pcs', 3, 'AST-BID-APR-0046', NULL, 'SIMASET-AST-QWFTFN6ZYS', 'Phantom Tuba Falopi', 'rusak', 0, 4, 2, 0, 'BBL'),
(168, 1, 5, 1, 9, 'group', 5, 'pcs', 3, 'AST-BID-APR-0047', NULL, 'SIMASET-AST-EAHRIGLEAF', 'Phantom Bayi Resusitasi', 'rusak', 0, 3, 2, 0, 'BBL'),
(169, 1, 5, 1, 9, 'group', 2, 'pcs', 3, 'AST-BID-APR-0048', NULL, 'SIMASET-AST-EN3UNI4F1T', 'Phantom Bayi Suntik', 'rusak', 0, 1, 1, 0, 'BBL'),
(170, 1, 5, 1, 9, 'group', 2, 'pcs', 3, 'AST-BID-APR-0049', NULL, 'SIMASET-AST-QFRXKSBQLM', 'Phantom Bayi Kecil', 'baik', 2, 0, 0, 0, 'INC'),
(171, 1, 5, 1, 9, 'group', 9, 'pcs', 3, 'AST-BID-APR-0050', NULL, 'SIMASET-AST-PKQESDGAO7', 'Phantom Bayi dengan Tali Pusat', 'rusak', 2, 4, 3, 0, 'KOMPLEMENTER'),
(172, 1, 5, 1, 9, 'group', 4, 'pcs', 3, 'AST-BID-APR-0051', NULL, 'SIMASET-AST-S9UF3KWOSN', 'Phantoom Baby Gym', 'baik', 4, 0, 0, 0, 'KOMPLEMENTER'),
(173, 1, 5, 1, 9, 'group', 2, 'pcs', 3, 'AST-BID-APR-0052', NULL, 'SIMASET-AST-IVUNYRIEED', 'Phantom Bayi didalam Perut', 'baik', 2, 0, 0, 0, 'INC'),
(174, 1, 5, 1, 9, 'group', 4, 'pcs', 3, 'AST-BID-APR-0053', NULL, 'SIMASET-AST-IWN9A04QNT', 'Phantom Perut Melahirkan set', 'rusak', 0, 3, 1, 0, 'INC'),
(175, 1, 5, 1, 9, 'group', 2, 'pcs', 3, 'AST-BID-APR-0054', NULL, 'SIMASET-AST-ZC41SKFAV0', 'Phantom Mekanisme Persalinan', 'sedang', 1, 1, 0, 0, 'KDK'),
(176, 1, 5, 1, 9, 'group', 2, 'pcs', 3, 'AST-BID-APR-0055', NULL, 'SIMASET-AST-R1AMEE0DWR', 'Phantom Kateter (Bening)', 'sedang', 0, 2, 0, 0, 'KDK'),
(177, 1, 5, 1, 9, 'group', 4, 'pcs', 3, 'AST-BID-APR-0056', NULL, 'SIMASET-AST-0DSVUGZGIY', 'Phantom Kateter (Kelamin Wanita/Pria)', 'rusak', 1, 2, 1, 0, 'KDK'),
(178, 1, 5, 1, 9, 'group', 1, 'pcs', 3, 'AST-BID-APR-0057', NULL, 'SIMASET-AST-7WGNEU4B3M', 'Phantom Saluran Kemih', 'sedang', 0, 1, 0, 0, 'KDK'),
(179, 1, 5, 1, 9, 'group', 1, 'pcs', 3, 'AST-BID-APR-0058', NULL, 'SIMASET-AST-6ZQ6JZEW80', 'Phantom RJP', 'sedang', 0, 1, 0, 0, 'GADAR'),
(180, 1, 5, 1, 9, 'group', 8, 'pcs', 3, 'AST-BID-APR-0059', NULL, 'SIMASET-AST-KKXRCCOQVI', 'Phantom Manual Plasenta', 'rusak', 0, 6, 2, 0, 'KOMPLE'),
(181, 1, 5, 1, 9, 'group', 10, 'pcs', 3, 'AST-BID-APR-0060', NULL, 'SIMASET-AST-DPDUDLAJWL', 'Phantom Vagina', 'baik', 10, 0, 0, 0, 'GADAR'),
(182, 1, 5, 1, 9, 'group', 1, 'pcs', 3, 'AST-BID-APR-0061', NULL, 'SIMASET-AST-UF23Z2SO23', 'Phantom KBI KBE', 'sedang', 0, 1, 0, 0, 'GADAR'),
(183, 1, 5, 1, 9, 'group', 4, 'pcs', 3, 'AST-BID-APR-0062', NULL, 'SIMASET-AST-UOLVYUTW0E', 'Alat Peraga Rahim KBI KBE', 'baik', 4, 0, 0, 0, 'KDK'),
(184, 1, 5, 1, 9, 'group', 2, 'pcs', 3, 'AST-BID-APR-0063', NULL, 'SIMASET-AST-YBBV2AJGVX', 'Phantom Luka', 'baik', 2, 0, 0, 0, 'NIFAS'),
(185, 1, 5, 1, 9, 'group', 2, 'pcs', 3, 'AST-BID-APR-0064', NULL, 'SIMASET-AST-66HLR5LAIH', 'Alat Peraga Payudara Breastcare', 'sedang', 0, 2, 0, 0, 'NIFAS'),
(186, 1, 5, 1, 9, 'group', 4, 'pcs', 3, 'AST-BID-APR-0065', NULL, 'SIMASET-AST-2RMY5ROUC5', 'Alat Peraga Payudara Boneka', 'baik', 4, 0, 0, 0, 'NIFAS'),
(187, 1, 5, 1, 9, 'group', 4, 'pcs', 3, 'AST-BID-APR-0066', NULL, 'SIMASET-AST-5Y5SGKQUGL', 'Alat Peraga Payudara Silikon', 'sedang', 0, 4, 0, 0, 'NIFAS'),
(188, 1, 5, 1, 9, 'group', 4, 'pcs', 3, 'AST-BID-APR-0067', NULL, 'SIMASET-AST-OYNACYGNMM', 'Alat Peraga Payudara Rajut', 'sedang', 0, 4, 0, 0, 'INC'),
(189, 1, 5, 1, 9, 'group', 1, 'pcs', 3, 'AST-BID-APR-0068', NULL, 'SIMASET-AST-LPVP2UYBL6', 'Phantom Tulang Panggul', 'baik', 1, 0, 0, 0, 'KDK'),
(190, 1, 5, 1, 9, 'group', 2, 'pcs', 3, 'AST-BID-APR-0069', NULL, 'SIMASET-AST-NPFDFP9IUB', 'Phantom Badan', 'baik', 2, 0, 0, 0, 'INC'),
(191, 1, 5, 1, 9, 'group', 6, 'pcs', 3, 'AST-BID-APR-0070', NULL, 'SIMASET-AST-RVBVJFKFSI', 'Phantom Robekan Perineum', 'sedang', 3, 3, 0, 0, 'NIFAS'),
(192, 1, 5, 1, 9, 'group', 18, 'pcs', 3, 'AST-BID-APR-0071', NULL, 'SIMASET-AST-TTQMRH493L', 'Bantal Hecting', 'rusak', 5, 11, 2, 0, 'NIFAS'),
(193, 1, 5, 1, 9, 'group', 6, 'pcs', 3, 'AST-BID-APR-0072', NULL, 'SIMASET-AST-QX3XGKTGLB', 'Spons Hecting', 'sedang', 0, 6, 0, 0, 'BBL, INC'),
(194, 1, 5, 1, 9, 'group', 4, 'pcs', 3, 'AST-BID-APR-0073', NULL, 'SIMASET-AST-5XJW69PB6Y', 'Injection Pad', 'baik', 4, 0, 0, 0, 'KDK'),
(195, 1, 5, 1, 9, 'group', 1, 'pcs', 3, 'AST-BID-APR-0074', NULL, 'SIMASET-AST-H1ALNVGTBG', 'Phantom Paha', 'rusak', 0, 0, 1, 0, 'KB'),
(196, 1, 5, 1, 9, 'group', 9, 'pcs', 3, 'AST-BID-APR-0075', NULL, 'SIMASET-AST-GSDLZ1FPCR', 'Phantom Implant', 'rusak', 0, 2, 7, 0, 'KDK'),
(197, 1, 5, 1, 9, 'group', 6, 'pcs', 3, 'AST-BID-APR-0076', NULL, 'SIMASET-AST-FUOVNNWRMQ', 'Phantom Lengan Infus', 'sedang', 2, 4, 0, 0, 'INC'),
(198, 1, 5, 1, 9, 'group', 9, 'pcs', 3, 'AST-BID-APR-0077', NULL, 'SIMASET-AST-ZGIQU608GS', 'Phantom Plasenta Silikon', 'rusak', 0, 5, 4, 0, 'ANC'),
(199, 1, 5, 1, 9, 'group', 5, 'pcs', 3, 'AST-BID-APR-0078', NULL, 'SIMASET-AST-X4JR2ZV9CS', 'Phantom Proses Kehamilan', 'sedang', 0, 5, 0, 0, 'ANC'),
(200, 1, 5, 1, 9, 'group', 1, 'pcs', 3, 'AST-BID-APR-0079', NULL, 'SIMASET-AST-1OWAKJ8BIS', 'Phantom Kehamilan Normal', 'sedang', 0, 1, 0, 0, 'ANC'),
(201, 1, 5, 1, 9, 'group', 1, 'pcs', 3, 'AST-BID-APR-0080', NULL, 'SIMASET-AST-CKEJU1CQP9', 'Phantom Kehamilan Gemeli', 'sedang', 0, 1, 0, 0, 'ANC'),
(202, 1, 5, 1, 9, 'group', 1, 'pcs', 3, 'AST-BID-APR-0081', NULL, 'SIMASET-AST-TRNBOHU4ZE', 'Phantom Kehamilan Lintang', 'sedang', 0, 1, 0, 0, 'KDK'),
(203, 1, 5, 1, 9, 'group', 3, 'pcs', 3, 'AST-BID-APR-0082', NULL, 'SIMASET-AST-ORYILFARIQ', 'Phantom NGT', 'sedang', 0, 3, 0, 0, 'INC'),
(204, 1, 5, 1, 9, 'group', 2, 'pcs', 3, 'AST-BID-APR-0083', NULL, 'SIMASET-AST-1MKBEVRNS3', 'Phantom Pembukaan 1-10 Kain', 'sedang', 0, 2, 0, 0, 'INC'),
(205, 1, 5, 1, 9, 'group', 2, 'pcs', 3, 'AST-BID-APR-0084', NULL, 'SIMASET-AST-2RGAEMZL8M', 'Phantom Pembukaan 1-10 Kayu', 'sedang', 0, 2, 0, 0, 'KDK'),
(206, 1, 5, 1, 9, 'group', 1, 'pcs', 3, 'AST-BID-APR-0085', NULL, 'SIMASET-AST-0UB87NGE7T', 'Phantom Manusia', 'sedang', 0, 1, 0, 0, 'KDK'),
(207, 1, 5, 1, 9, 'group', 1, 'pcs', 3, 'AST-BID-APR-0086', NULL, 'SIMASET-AST-PIQYYBO2LA', 'Phantom Anatomi', 'sedang', 0, 1, 0, 0, 'KDK'),
(208, 1, 5, 1, 9, 'group', 1, 'pcs', 3, 'AST-BID-APR-0087', NULL, 'SIMASET-AST-7OTHNYRXYF', 'Phantom Kerangka', 'rusak', 0, 0, 1, 0, 'KDK'),
(209, 1, 5, 1, 9, 'group', 1, 'pcs', 3, 'AST-BID-APR-0088', NULL, 'SIMASET-AST-LFKKPSBJBR', 'Phantom Lapisan Kulit', 'sedang', 0, 1, 0, 0, 'NIFAS, KOMPLE'),
(210, 1, 5, 1, 9, 'group', 1, 'pcs', 3, 'AST-BID-APR-0089', NULL, 'SIMASET-AST-RVLPSHFGOF', 'Phantom Gigi', 'sedang', 0, 1, 0, 0, 'NIFAS, KOMPLE'),
(211, 1, 5, 1, 9, 'group', 6, 'pcs', 3, 'AST-BID-APR-0090', NULL, 'SIMASET-AST-MLTZGOI6PZ', 'Phantom Menyusui/Sadari', 'sedang', 0, 6, 0, 0, 'ANC'),
(212, 1, 5, 1, 9, 'group', 2, 'pcs', 3, 'AST-BID-APR-0091', NULL, 'SIMASET-AST-ZAPBJC8EMP', 'Phantom Perkembangan Bayi', 'rusak', 0, 0, 2, 0, 'NIFAS'),
(213, 1, 5, 1, 9, 'group', 5, 'pcs', 3, 'AST-BID-APR-0092', NULL, 'SIMASET-AST-O546QGQOTI', 'Hecting Pad', 'sedang', 0, 5, 0, 0, 'INC'),
(214, 1, 5, 1, 9, 'group', 1, 'pcs', 3, 'AST-BID-APR-0093', NULL, 'SIMASET-AST-R3PKAUW8N7', 'Phantom Rangka Panggul', 'baik', 1, 0, 0, 0, NULL),
(215, 1, 5, 1, 9, 'group', 5, 'pcs', 3, 'AST-BID-APR-0094', NULL, 'SIMASET-AST-YULYOSSXWC', 'Aalat Peraga Vagina Rajut', 'baik', 5, 0, 0, 0, 'BBL'),
(216, 1, 5, 1, 9, 'group', 5, 'pcs', 3, 'AST-BID-APR-0095', NULL, 'SIMASET-AST-ZKFGPDZORZ', 'Alat Peraga Lambung Bayi Set', 'sedang', 0, 5, 0, 0, NULL),
(236, 1, 6, 1, 1, 'group', 8, 'pcs', 3, 'AST-BID-PDP-0001', NULL, 'SIMASET-AST-Y8LMHAUWJH', 'Kacamata Google', 'baik', 8, 0, 0, 0, NULL),
(247, 1, 6, 1, 2, 'group', 8, 'pcs', 3, 'AST-BID-PDP-0002', NULL, 'SIMASET-AST-UEQEXCPL0H', 'Kacamata Google', 'sedang', 0, 8, 0, 0, 'kalibrasi');
INSERT INTO `assets` (`id`, `unit_id`, `category_id`, `location_id`, `container_id`, `identification_type`, `quantity`, `satuan`, `created_by`, `asset_code`, `legacy_inventory_code`, `qr_code`, `name`, `kondisi_aset`, `jumlah_baik`, `jumlah_sedang`, `jumlah_rusak`, `jumlah_hilang`, `description`) VALUES
(456, 1, 4, 1, 1, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0169', NULL, 'SIMASET-AST-I8HUJGN6XQ', 'Kom  DTT', 'baik', 5, 0, 0, 0, NULL),
(467, 1, 1, 1, 2, 'group', 5, 'pcs', 3, 'AST-BID-AUK-0093', NULL, 'SIMASET-AST-8DMAFA0YXL', 'Tensimeter Manual Onemed Abu', 'sedang', 4, 1, 0, 0, 'perlu kalibrasi'),
(470, 1, 2, 1, 2, 'group', 5, 'pcs', 3, 'AST-BID-APR-0196', NULL, 'SIMASET-AST-WJNPPKSE7V', 'Headlamp', 'rusak', 3, 1, 1, 0, 'perlu perbaikan'),
(492, 1, 4, 1, 2, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0188', NULL, 'SIMASET-AST-YT0PBJO3HM', 'Kom Tutup DTT', 'baik', 5, 0, 0, 0, NULL),
(513, 1, 4, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0204', NULL, 'SIMASET-AST-JJGAB8XQ6F', 'Kom Tutup DTT', 'baik', 5, 0, 0, 0, NULL),
(520, 1, 2, 1, 3, 'group', 5, 'pcs', 3, 'AST-BID-APR-0207', NULL, 'SIMASET-AST-H1MCF8GJ8B', 'Nipple Puller 3cc', 'baik', 5, 0, 0, 0, NULL),
(521, 1, 2, 1, 3, 'group', 4, 'pcs', 3, 'AST-BID-APR-0208', NULL, 'SIMASET-AST-OV7VQ8DMHD', 'Nipple Puller 25cc', 'baik', 4, 0, 0, 0, NULL),
(534, 1, 4, 1, 4, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0209', NULL, 'SIMASET-AST-7CAY3OYJLY', 'Kom Tutup DTT', 'baik', 5, 0, 0, 0, NULL),
(561, 1, 7, 1, 6, 'individual', 8, 'unit', 3, 'AST-BID-ALB-0001', NULL, 'SIMASET-AST-IASSW5RRZ7', 'Etalase Kecil', 'rusak', 5, 2, 1, 0, NULL),
(562, 1, 7, 1, 6, 'individual', 3, 'unit', 3, 'AST-BID-ALB-0002', NULL, 'SIMASET-AST-CAWSBUQ6HC', 'Etalase Sedang', 'sedang', 2, 1, 0, 0, NULL),
(563, 1, 7, 1, 6, 'individual', 1, 'unit', 3, 'AST-BID-ALB-0003', NULL, 'SIMASET-AST-LJR3KIVYBN', 'Etalase Besar', 'rusak', 0, 0, 1, 0, NULL),
(564, 1, 7, 1, 6, 'individual', 2, 'unit', 3, 'AST-BID-ALB-0004', NULL, 'SIMASET-AST-5H8YRPANGT', 'Lemari Pakaian', 'sedang', 1, 1, 0, 0, NULL),
(565, 1, 7, 1, 6, 'individual', 2, 'unit', 3, 'AST-BID-ALB-0005', NULL, 'SIMASET-AST-EFZMAZZQRU', 'Rak Putih', 'sedang', 1, 1, 0, 0, NULL),
(566, 1, 7, 1, 6, 'individual', 1, 'unit', 3, 'AST-BID-ALB-0006', NULL, 'SIMASET-AST-XCQ2PAJV0F', 'Autoclaf', 'sedang', 0, 1, 0, 0, NULL),
(667, 1, 1, 1, 10, 'group', 4, 'pcs', 3, 'AST-BID-AUK-0119', NULL, 'SIMASET-AST-CQFM0VQVB6', 'Timbangan Dewasa Digital', 'sedang', 2, 2, 0, 0, NULL);
INSERT INTO `assets` (`id`, `unit_id`, `category_id`, `location_id`, `container_id`, `identification_type`, `quantity`, `satuan`, `created_by`, `asset_code`, `legacy_inventory_code`, `qr_code`, `name`, `kondisi_aset`, `jumlah_baik`, `jumlah_sedang`, `jumlah_rusak`, `jumlah_hilang`, `description`) VALUES
(800, 1, 4, 1, 7, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0307', NULL, 'SIMASET-AST-STK9QNLQB2', 'Kom Tutup Kpas Bayi', 'baik', 5, 0, 0, 0, NULL),
(801, 1, 4, 1, 7, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0308', NULL, 'SIMASET-AST-3IYMIWIHL5', 'Kom Tutup Tissu', 'baik', 5, 0, 0, 0, NULL),
(822, 1, 4, 1, 8, 'group', 5, 'pcs', 3, 'AST-BID-MIN-0313', NULL, 'SIMASET-AST-K9EJ4T8PKA', 'Kom Tutup DTT', 'baik', 5, 0, 0, 0, NULL),
(857, 1, 5, 1, 9, 'group', 6, 'pcs', 3, 'AST-BID-APR-0342', NULL, 'SIMASET-AST-5MOWNHQEVN', 'Phantoom Baby Massage', 'rusak', 2, 2, 2, 0, 'KOMPLEMENTER'),
(859, 1, 5, 1, 9, 'group', 2, 'pcs', 3, 'AST-BID-APR-0344', NULL, 'SIMASET-AST-4XT7BMZOBF', 'Phantoom Baby Swim', 'baik', 2, 0, 0, 0, 'ANC'),
(904, 1, 2, 1, 4, 'group', 2, 'pcs', 3, 'AST-BID-APR-0389', NULL, 'SIMASET-AST-7VNIGDKOOP', 'Alat Huknah', 'baik', 2, 0, 0, 0, 'kdk'),
(905, 1, 1, 1, 10, 'individual', 4, 'pcs', 3, 'AST-BID-AUK-0160', NULL, 'SIMASET-AST-NMUIXBNJKN', 'Timbangan Dewasa Digital', 'sedang', 2, 2, 0, 0, NULL),
(906, 1, 1, 1, 10, 'individual', 1, 'pcs', 3, 'AST-BID-AUK-0161', NULL, 'SIMASET-AST-OBNKJTUEBZ', 'Timbangan Dewasa Manual', 'baik', 1, 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `asset_quantity_additions`
--

CREATE TABLE `asset_quantity_additions` (
  `id` bigint UNSIGNED NOT NULL,
  `asset_id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `addition_date` date NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `jumlah_baik` int UNSIGNED NOT NULL DEFAULT '0',
  `jumlah_sedang` int UNSIGNED NOT NULL DEFAULT '0',
  `jumlah_rusak` int UNSIGNED NOT NULL DEFAULT '0',
  `jumlah_hilang` int UNSIGNED NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `unit_id`, `name`, `status`) VALUES
(7, 1, 'Alat Laboratorium', 'active'),
(5, 1, 'Alat Peraga', 'active'),
(2, 1, 'Alat Praktik', 'active'),
(1, 1, 'Alat Ukur', 'active'),
(3, 1, 'Alat Ukur', 'active'),
(6, 1, 'APD', 'active'),
(4, 1, 'Metal Instrumen', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `containers`
--

CREATE TABLE `containers` (
  `id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qr_code` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `containers`
--

INSERT INTO `containers` (`id`, `unit_id`, `location_id`, `name`, `code`, `qr_code`, `status`) VALUES
(1, 1, 1, 'Etalase 1 ANC', 'TPN-BID-0001', 'SIMASET-CTR-LXSMHHGKR7', 'active'),
(2, 1, 1, 'Etalase 2 INC', 'TPN-BID-0002', 'SIMASET-CTR-BPI0RJN4YE', 'active'),
(3, 1, 1, 'Etalase 3 PNC', 'TPN-BID-0003', 'SIMASET-CTR-XYX6RR1JIZ', 'active'),
(4, 1, 1, 'Etalase 6 KDK a', 'TPN-BID-0004', 'SIMASET-CTR-6XJZ0LE2PE', 'active'),
(5, 1, 1, 'Etalase 6 KDK b', 'TPN-BID-0005', 'SIMASET-CTR-6M69XTISTZ', 'active'),
(6, 1, 1, 'Di luar penyimpanan', 'TPN-BID-0006', 'SIMASET-CTR-CHYD7G3AKI', 'active'),
(7, 1, 1, 'Etalase 5 BBL', 'TPN-BID-0007', 'SIMASET-CTR-M4ZHN3DEIU', 'active'),
(8, 1, 1, 'Etalase 4 KB', 'TPN-BID-0008', 'SIMASET-CTR-NLTXSYH2LX', 'active'),
(9, 1, 1, 'Etalase Manekin', 'TPN-BID-0009', 'SIMASET-CTR-IHSETPHAH8', 'active'),
(10, 1, 1, 'Etalase 8', 'TPN-BID-0010', 'SIMASET-CTR-XUEVYAHMNM', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_checks`
--

CREATE TABLE `inventory_checks` (
  `id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `check_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `periode` char(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `semester` enum('Ganjil','Genap') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_akademik` char(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_pemeriksaan` date NOT NULL,
  `status` enum('draft','ongoing','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_check_items`
--

CREATE TABLE `inventory_check_items` (
  `id` bigint UNSIGNED NOT NULL,
  `inventory_check_id` bigint UNSIGNED NOT NULL,
  `asset_id` bigint UNSIGNED NOT NULL,
  `jumlah_sistem` smallint UNSIGNED NOT NULL DEFAULT '1',
  `jumlah_aktual` smallint UNSIGNED DEFAULT NULL,
  `jumlah_baik` smallint UNSIGNED NOT NULL DEFAULT '0',
  `jumlah_sedang` smallint UNSIGNED NOT NULL DEFAULT '0',
  `jumlah_rusak` smallint UNSIGNED NOT NULL DEFAULT '0',
  `jumlah_hilang` smallint UNSIGNED NOT NULL DEFAULT '0',
  `hasil_pemeriksaan` enum('pending','sesuai','tidak_sesuai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

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
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `unit_id`, `name`, `status`) VALUES
(1, 1, 'Depo Alat', 'active'),
(4, 1, 'Laboratorium KDK', 'active'),
(2, 1, 'Laboratorium Kebidanan', 'active'),
(3, 1, 'Laboratorium Komplementer', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_15_000001_create_asset_master_tables', 1),
(5, '2026_05_15_000002_create_asset_transaction_tables', 1),
(6, '2026_05_15_000003_backfill_asset_qr_codes', 1),
(7, '2026_05_15_000004_add_hybrid_qr_to_assets_and_containers', 1),
(8, '2026_05_15_000005_add_asset_approval_and_photo_fields', 1),
(9, '2026_05_15_000006_create_damage_reports_table', 1),
(10, '2026_05_15_000007_add_asset_type_to_assets', 1),
(11, '2026_05_15_000008_create_inventory_checks_tables', 1),
(12, '2026_05_15_000009_update_asset_type_terms', 1),
(13, '2026_05_16_000001_simplify_container_codes', 1),
(14, '2026_05_18_000001_support_group_asset_identification', 1),
(15, '2026_05_18_000002_scope_categories_and_add_lost_asset_condition', 1),
(16, '2026_05_19_000001_align_asset_and_stock_opname_schema', 1),
(17, '2026_05_19_233717_simplify_master_data_columns', 1),
(18, '2026_05_19_233913_drop_unused_timestamps_from_core_tables', 1),
(19, '2026_05_19_234136_simplify_users_columns_for_core_roles', 1),
(20, '2026_05_19_234252_merge_duplicate_laboran_units_after_master_simplification', 1),
(21, '2026_05_19_234547_keep_unit_code_and_drop_unused_category_location_codes', 1),
(22, '2026_05_19_234910_add_unit_id_to_containers_table', 1),
(23, '2026_05_19_235802_make_asset_location_nullable_for_excel_import', 1),
(24, '2026_05_20_000001_add_kondisi_aset_to_assets_table', 1),
(25, '2026_05_20_000002_ensure_core_user_columns', 1),
(26, '2026_05_20_000003_drop_non_core_simaset_tables', 1),
(27, '2026_05_20_000004_cleanup_legacy_columns_from_assets_table', 1),
(28, '2026_05_20_000005_drop_photo_from_assets_table', 1),
(29, '2026_05_20_000006_merge_duplicate_laboratory_units', 1),
(30, '2026_05_20_000007_cleanup_default_users', 1),
(31, '2026_05_20_000008_drop_description_from_units_table', 1),
(32, '2026_05_20_000009_drop_condition_notes_from_inventory_check_items', 1),
(33, '2026_05_20_000010_enforce_unique_inventory_check_date_per_unit', 1),
(34, '2026_05_20_000011_sync_inventory_check_period_to_academic_year', 1),
(35, '2026_05_24_000001_create_tool_replacement_requests_table', 1),
(36, '2026_05_24_000002_split_nim_semester_on_tool_replacement_requests', 1),
(37, '2026_05_24_000003_add_waiting_replacement_status_and_normalize_replacement_codes', 1),
(38, '2026_05_24_000004_normalize_tool_replacement_codes_to_unit_month_year', 1),
(39, '2026_05_24_000005_normalize_tool_replacement_codes_to_three_digits', 1),
(40, '2026_05_24_000006_drop_nim_semester_from_tool_replacement_requests', 1),
(41, '2026_05_24_000007_drop_unused_sessions_table', 1),
(42, '2026_05_25_000001_drop_asset_type_from_assets', 1),
(43, '2026_05_31_000001_normalize_asset_identification_types', 1),
(44, '2026_05_31_000002_remove_inactive_asset_condition', 1),
(45, '2026_08_05_000001_optimize_core_schema_for_database_audit', 1),
(46, '2026_08_27_000001_add_condition_counts_and_quantity_additions', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tool_replacement_requests`
--

CREATE TABLE `tool_replacement_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `replacement_code` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asset_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `student_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_nim` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_semester` tinyint UNSIGNED DEFAULT NULL,
  `prodi_kelas` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `practicum_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `incident_date` date NOT NULL,
  `replacement_quantity` smallint UNSIGNED NOT NULL,
  `damage_description` text COLLATE utf8mb4_unicode_ci,
  `whatsapp_number` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `damage_photo` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('menunggu_verifikasi','menunggu_penggantian','sudah_diganti','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu_verifikasi',
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `received_at` timestamp NULL DEFAULT NULL,
  `laboran_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `code`, `status`) VALUES
(1, 'Laboran Kebidanan', 'BID', 'active'),
(2, 'Laboran Farmasi', 'FAR', 'active'),
(3, 'Laboran Gizi', 'GZI', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','pengelola','pimpinan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pengelola',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `unit_id`, `name`, `username`, `email`, `password`, `role`, `status`) VALUES
(1, NULL, 'Admin SIMASET SBH', 'admin', 'admin@simaset.local', '$2y$12$gQQUAVxuXATvqEYM.9u7YOU/bR7YiEHYDDQKFuTVlOwlWGyWvLg8C', 'admin', 'active'),
(2, NULL, 'Pimpinan', 'pimpinan', 'pimpinan@simaset.test', '$2y$12$I01S/0hb/uAzi6P6T5OA1OXA7ZUCnKye0QhPVPOiJ.ZSi7XbhEG/.', 'pimpinan', 'active'),
(3, 1, 'Diah', 'diah', 'diah@sbh.ac.id', '$2y$12$tDtqr1xheIU8Xc8TBPeefO4ER0.6oGR/DilATvmJokUc5HWtn90gi', 'pengelola', 'active'),
(4, 2, 'Melisa', 'melisa', 'melisaanggiani@sbh.ac.id', '$2y$12$MlS38hhVVerYBrlVJSd.G.Tcai2pu1f2GnxB.GzrflMQadgRNexwe', 'pengelola', 'active'),
(5, 3, 'Lisda', 'lisda', 'lisda_karlina@sbh.ac.id', '$2y$12$KUaxuPGCI1/SWyC1WDJAZOVSk7t0fMCFEfhTMc54GZUWVQJAB0Xm2', 'pengelola', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assets_asset_code_unique` (`asset_code`),
  ADD UNIQUE KEY `assets_qr_code_unique` (`qr_code`),
  ADD KEY `assets_category_id_foreign` (`category_id`),
  ADD KEY `assets_location_id_foreign` (`location_id`),
  ADD KEY `assets_container_id_foreign` (`container_id`),
  ADD KEY `assets_created_by_foreign` (`created_by`),
  ADD KEY `assets_unit_location_name_index` (`unit_id`,`location_id`,`name`),
  ADD KEY `assets_unit_category_index` (`unit_id`,`category_id`),
  ADD KEY `assets_unit_kondisi_index` (`unit_id`,`kondisi_aset`),
  ADD KEY `assets_unit_legacy_inventory_code_index` (`unit_id`,`legacy_inventory_code`);

--
-- Indexes for table `asset_quantity_additions`
--
ALTER TABLE `asset_quantity_additions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_quantity_additions_asset_id_foreign` (`asset_id`),
  ADD KEY `asset_quantity_additions_created_by_foreign` (`created_by`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_unit_status_name_index` (`unit_id`,`status`,`name`);

--
-- Indexes for table `containers`
--
ALTER TABLE `containers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `containers_code_unique` (`code`),
  ADD UNIQUE KEY `containers_qr_code_unique` (`qr_code`),
  ADD KEY `containers_location_id_foreign` (`location_id`),
  ADD KEY `containers_unit_status_name_index` (`unit_id`,`status`,`name`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventory_checks`
--
ALTER TABLE `inventory_checks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventory_checks_check_code_unique` (`check_code`),
  ADD UNIQUE KEY `inventory_checks_unit_date_unique` (`unit_id`,`tanggal_pemeriksaan`),
  ADD UNIQUE KEY `inventory_checks_unit_semester_tahun_unique` (`unit_id`,`semester`,`tahun_akademik`),
  ADD KEY `inventory_checks_created_by_foreign` (`created_by`),
  ADD KEY `inventory_checks_unit_status_index` (`unit_id`,`status`);

--
-- Indexes for table `inventory_check_items`
--
ALTER TABLE `inventory_check_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventory_check_items_inventory_check_id_asset_id_unique` (`inventory_check_id`,`asset_id`),
  ADD KEY `inventory_check_items_asset_id_foreign` (`asset_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `locations_unit_name_unique` (`unit_id`,`name`),
  ADD KEY `locations_unit_status_name_index` (`unit_id`,`status`,`name`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `tool_replacement_requests`
--
ALTER TABLE `tool_replacement_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tool_replacement_requests_replacement_code_unique` (`replacement_code`),
  ADD KEY `tool_replacement_requests_asset_id_foreign` (`asset_id`),
  ADD KEY `tool_replacement_requests_verified_by_foreign` (`verified_by`),
  ADD KEY `tool_replacement_requests_unit_id_status_index` (`unit_id`,`status`),
  ADD KEY `tool_replacement_requests_incident_date_index` (`incident_date`),
  ADD KEY `tool_replacements_code_nim_index` (`replacement_code`,`student_nim`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `units_code_unique` (`code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_unit_id_foreign` (`unit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2102;

--
-- AUTO_INCREMENT for table `asset_quantity_additions`
--
ALTER TABLE `asset_quantity_additions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `containers`
--
ALTER TABLE `containers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_checks`
--
ALTER TABLE `inventory_checks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_check_items`
--
ALTER TABLE `inventory_check_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `tool_replacement_requests`
--
ALTER TABLE `tool_replacement_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assets`
--
ALTER TABLE `assets`
  ADD CONSTRAINT `assets_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `assets_container_id_foreign` FOREIGN KEY (`container_id`) REFERENCES `containers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `assets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `assets_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `assets_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

--
-- Constraints for table `asset_quantity_additions`
--
ALTER TABLE `asset_quantity_additions`
  ADD CONSTRAINT `asset_quantity_additions_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asset_quantity_additions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `containers`
--
ALTER TABLE `containers`
  ADD CONSTRAINT `containers_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `containers_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inventory_checks`
--
ALTER TABLE `inventory_checks`
  ADD CONSTRAINT `inventory_checks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_checks_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_check_items`
--
ALTER TABLE `inventory_check_items`
  ADD CONSTRAINT `inventory_check_items_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_check_items_inventory_check_id_foreign` FOREIGN KEY (`inventory_check_id`) REFERENCES `inventory_checks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `locations_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tool_replacement_requests`
--
ALTER TABLE `tool_replacement_requests`
  ADD CONSTRAINT `tool_replacement_requests_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tool_replacement_requests_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tool_replacement_requests_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
