-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 06, 2026 at 01:43 PM
-- Server version: 10.11.16-MariaDB-cll-lve
-- PHP Version: 8.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bpbe9361_sidarlog`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barang`
--

CREATE TABLE `tbl_barang` (
  `id` int(11) NOT NULL,
  `nm_barang` varchar(150) NOT NULL,
  `stok_totalbarang_kecil` int(11) DEFAULT NULL,
  `harga_totalbarang_kecil` int(11) DEFAULT NULL,
  `stok_totalbarang_besar` int(11) DEFAULT NULL,
  `harga_totalbarang_besar` int(11) DEFAULT NULL,
  `sumber_anggaran` int(11) NOT NULL,
  `tgl_kadaluarsa` date DEFAULT NULL,
  `lokasi_barang` int(11) NOT NULL,
  `tgl_terima_barang` date NOT NULL,
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='tabel barang';

--
-- Dumping data for table `tbl_barang`
--

INSERT INTO `tbl_barang` (`id`, `nm_barang`, `stok_totalbarang_kecil`, `harga_totalbarang_kecil`, `stok_totalbarang_besar`, `harga_totalbarang_besar`, `sumber_anggaran`, `tgl_kadaluarsa`, `lokasi_barang`, `tgl_terima_barang`, `tgl_dibuat`, `tgl_diupdate`) VALUES
(35, 'Perlengkapan Bayi / Kid Ware', NULL, NULL, 33, 390858, 6, NULL, 7, '2020-07-15', '2026-04-09 12:22:07', '2026-04-09 06:56:35'),
(36, 'Peti Jenazah', NULL, NULL, 38, 900000, 11, NULL, 8, '2021-08-18', '2026-04-09 12:47:12', '2026-04-09 07:05:04'),
(37, 'Perlengkapan Kebersihan Keluarga', NULL, NULL, 15, 5865000, 7, NULL, 7, '2022-01-31', '2026-04-09 13:59:36', '2026-04-09 13:59:36'),
(38, 'Hygene Kit', NULL, NULL, 51, 33150000, 6, NULL, 7, '2024-12-26', '2026-04-09 14:09:57', '2026-04-09 14:09:57'),
(39, 'Terpal', NULL, NULL, 61, 16775000, 6, NULL, 7, '2024-12-26', '2026-04-09 14:12:53', '2026-04-09 14:12:53'),
(40, 'Matras', NULL, NULL, 20, 3806000, 6, NULL, 7, '2024-12-26', '2026-04-09 14:15:22', '2026-04-09 14:15:22'),
(41, 'Selimut', NULL, NULL, 19, 2642900, 6, NULL, 7, '2024-12-26', '2026-04-09 14:16:21', '2026-04-09 14:16:21'),
(42, 'Kasur Lipat', NULL, NULL, 18, 11700000, 6, NULL, 7, '2024-12-26', '2026-04-09 14:30:07', '2026-04-09 14:30:07'),
(43, 'Jas Hujan', NULL, NULL, 5, 1673325, 8, NULL, 7, '2026-01-23', '2026-04-09 15:08:30', '2026-04-09 15:08:30'),
(44, 'Sepatu Boots', NULL, NULL, 5, 804750, 8, NULL, 4, '2026-01-26', '2026-04-09 15:09:52', '2026-04-09 08:11:18');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barang_keluar`
--

CREATE TABLE `tbl_barang_keluar` (
  `id` int(11) NOT NULL,
  `nm_barang` int(11) NOT NULL,
  `jum_barang_kecil` int(11) DEFAULT NULL,
  `jum_barang_besar` int(11) DEFAULT NULL,
  `keperluan` text NOT NULL,
  `pihak_kedua` int(3) NOT NULL,
  `karyawan_input` int(11) NOT NULL,
  `pejabat` int(11) NOT NULL,
  `no_barang_keluar` int(11) NOT NULL,
  `tgl_keluar` datetime NOT NULL,
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='tabel transaksi barang keluar';

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barang_masuk`
--

CREATE TABLE `tbl_barang_masuk` (
  `id` int(11) NOT NULL,
  `nm_barang` int(11) NOT NULL,
  `jum_barang_kecil` int(11) DEFAULT NULL,
  `jum_barang_besar` int(11) DEFAULT NULL,
  `keperluan` text NOT NULL,
  `pihak_kesatu` int(3) NOT NULL,
  `karyawan_input` int(11) NOT NULL,
  `pejabat` int(11) NOT NULL,
  `tgl_masuk` datetime NOT NULL,
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='tabel transaksi barang masuk';

--
-- Dumping data for table `tbl_barang_masuk`
--

INSERT INTO `tbl_barang_masuk` (`id`, `nm_barang`, `jum_barang_kecil`, `jum_barang_besar`, `keperluan`, `pihak_kesatu`, `karyawan_input`, `pejabat`, `tgl_masuk`, `tgl_dibuat`, `tgl_diupdate`) VALUES
(30, 36, NULL, 38, 'Bantuan', 5, 23, 22, '2021-08-18 00:00:00', '2026-04-09 05:53:28', '2026-04-09 05:53:28'),
(31, 35, NULL, 33, 'Bantuan', 5, 23, 22, '2020-07-15 00:00:00', '2026-04-09 05:54:44', '2026-04-09 05:54:44');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barang_no_keluar`
--

CREATE TABLE `tbl_barang_no_keluar` (
  `id` int(11) NOT NULL,
  `no_keluar` int(10) NOT NULL,
  `tahun_keluar` varchar(5) NOT NULL,
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_barang_no_keluar`
--

INSERT INTO `tbl_barang_no_keluar` (`id`, `no_keluar`, `tahun_keluar`, `tgl_dibuat`, `tgl_diupdate`) VALUES
(54, 4, '2022', '2023-10-24 08:58:08', '2024-02-26 04:34:51'),
(55, 3, '2024', '2024-01-23 14:27:17', '2024-01-23 14:27:17'),
(56, 1, '2024', '2024-02-26 04:35:19', '2024-02-26 04:35:19'),
(57, 5, '2025', '2025-12-12 06:50:32', '2025-12-12 06:50:32'),
(58, 6, '2025', '2025-12-12 07:52:32', '2025-12-12 07:52:32'),
(59, 7, '2025', '2025-12-19 13:19:09', '2025-12-19 13:19:09');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barang_opname`
--

CREATE TABLE `tbl_barang_opname` (
  `id` int(11) NOT NULL,
  `nm_barang` int(11) NOT NULL,
  `stok_awalbarang_kecil` int(11) DEFAULT NULL,
  `harga_awalbarang_kecil` int(11) DEFAULT NULL,
  `stok_awalbarang_besar` int(11) DEFAULT NULL,
  `harga_awalbarang_besar` int(11) DEFAULT NULL,
  `jum_barangmasuk_kecil` int(11) DEFAULT NULL,
  `jum_barangkeluar_kecil` int(11) DEFAULT NULL,
  `jum_barangmasuk_besar` int(11) DEFAULT NULL,
  `jum_barangkeluar_besar` int(11) DEFAULT NULL,
  `stok_akhirbarang_kecil` int(11) DEFAULT NULL,
  `harga_akhirbarang_kecil` int(11) DEFAULT NULL,
  `stok_akhirbarang_besar` int(11) DEFAULT NULL,
  `harga_akhirbarang_besar` int(11) DEFAULT NULL,
  `karyawan_input` int(11) NOT NULL,
  `pejabat` int(11) NOT NULL,
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='tabel transaksi stok opname';

--
-- Dumping data for table `tbl_barang_opname`
--

INSERT INTO `tbl_barang_opname` (`id`, `nm_barang`, `stok_awalbarang_kecil`, `harga_awalbarang_kecil`, `stok_awalbarang_besar`, `harga_awalbarang_besar`, `jum_barangmasuk_kecil`, `jum_barangkeluar_kecil`, `jum_barangmasuk_besar`, `jum_barangkeluar_besar`, `stok_akhirbarang_kecil`, `harga_akhirbarang_kecil`, `stok_akhirbarang_besar`, `harga_akhirbarang_besar`, `karyawan_input`, `pejabat`, `tgl_dibuat`, `tgl_diupdate`) VALUES
(32, 36, NULL, NULL, 38, 900000, NULL, NULL, 38, NULL, NULL, NULL, 76, 1800000, 23, 22, '2026-04-09 05:53:49', '2026-04-09 05:53:49'),
(33, 35, NULL, NULL, 33, 12898314, NULL, NULL, 33, NULL, NULL, NULL, 66, 25796628, 23, 22, '2026-04-09 05:55:12', '2026-04-09 05:55:12');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_karyawan`
--

CREATE TABLE `tbl_karyawan` (
  `id` int(11) NOT NULL,
  `nm_karyawan` varchar(100) NOT NULL,
  `nip` varchar(50) NOT NULL,
  `id_jabatan` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status_karyawan` enum('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif',
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='tabel untuk pejabat penanggung jawab';

--
-- Dumping data for table `tbl_karyawan`
--

INSERT INTO `tbl_karyawan` (`id`, `nm_karyawan`, `nip`, `id_jabatan`, `email`, `password`, `status_karyawan`, `tgl_dibuat`, `tgl_diupdate`) VALUES
(19, 'Restu Agung', '12345', 15, 'restu@bpbd.id', '$2y$10$ERAU9KnY4FCHMOMsYQXQ6eKn3bKgE4smgS7WPn.UZUVgcA4hfvtY.', 'aktif', '2023-07-20 02:31:05', '2023-08-08 03:31:04'),
(22, 'CAHYONO RAHMAN', '197301302005011001', 13, 'cahyonorahman2024@gmail.com', '$2y$10$9cNR4pM.4GvyH2Fqt.f6uuc92jF/P7Z2PAlyEhoi36WI4tvTGPfem', 'aktif', '2023-07-26 06:08:34', '2026-04-09 08:15:18'),
(23, 'azis', '1111111', 14, 'azis@bpbd.id', '$2y$10$Gyk0wfFQwbmFn1mfuIPQ.u.TEKU784/FqQGOBjnaUHyTV8dGHzKTG', 'aktif', '2023-07-30 05:21:52', '2025-12-11 23:45:10'),
(34, 'Admin', '112233', 15, 'oriza@bpbd.id', '$2y$10$Yy85nm0HglbpwzhZO/guS.Uy6G72ypU5rmid8vZd3eftpN4v243ly', 'aktif', '2024-02-27 09:26:49', '2024-09-26 10:46:28');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reff_brg_lokasi`
--

CREATE TABLE `tbl_reff_brg_lokasi` (
  `id` int(11) NOT NULL,
  `nm_gudang` varchar(100) NOT NULL,
  `keterangan` text NOT NULL,
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='tabel referensi lokasi gudang penyimpanan';

--
-- Dumping data for table `tbl_reff_brg_lokasi`
--

INSERT INTO `tbl_reff_brg_lokasi` (`id`, `nm_gudang`, `keterangan`, `tgl_dibuat`, `tgl_diupdate`) VALUES
(4, 'Gudang A', 'Gudang A', '2023-07-16 02:50:33', '2023-07-16 02:50:33'),
(6, 'Gudang B', 'Peralatan Penanganan Bencana', '2023-07-20 02:20:33', '2026-01-20 09:54:36'),
(7, 'GUDANG C', 'Logistik Penanggulangan Bencana', '2026-01-20 09:53:53', '2026-01-20 09:53:53'),
(8, 'Gudang Gebu', 'Gudang yang berada di kawasan Gedung Bupati', '2026-04-09 06:45:28', '2026-04-09 06:45:28');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reff_brg_satuan`
--

CREATE TABLE `tbl_reff_brg_satuan` (
  `id` int(11) NOT NULL,
  `nm_satuan_barang` varchar(100) NOT NULL,
  `keterangan` text NOT NULL,
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='tabel referensi barang untuk satuan barang kecil dan besar';

--
-- Dumping data for table `tbl_reff_brg_satuan`
--

INSERT INTO `tbl_reff_brg_satuan` (`id`, `nm_satuan_barang`, `keterangan`, `tgl_dibuat`, `tgl_diupdate`) VALUES
(8, 'Buah', 'Satuan untuk pcs', '2023-08-17 11:36:27', '2023-08-17 13:22:52'),
(9, 'Dus', 'Satuan untuk Dus', '2023-08-22 02:37:29', '2023-08-22 02:37:29'),
(10, 'Paket', 'Paket', '2026-04-09 05:23:08', '2026-04-09 05:23:08'),
(11, 'Lembar', 'Satuan Lembar', '2026-04-09 05:23:38', '2026-04-09 05:23:38'),
(12, 'Botol', 'Satuan untuk botol', '2026-04-09 05:24:09', '2026-04-09 05:24:09'),
(13, 'Pcs', 'Untuk satuan pieces', '2026-04-09 05:25:23', '2026-04-09 05:25:23'),
(14, 'Pasang', 'Satuan untuk barang yang berpasangan', '2026-04-09 08:10:19', '2026-04-09 08:10:19');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reff_brg_sumberanggaran`
--

CREATE TABLE `tbl_reff_brg_sumberanggaran` (
  `id` int(11) NOT NULL,
  `nm_sumberanggaran` varchar(100) NOT NULL,
  `tahun_anggaran` date NOT NULL,
  `keterangan` text NOT NULL,
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='tabel referensi barang untuk sumber anggaran';

--
-- Dumping data for table `tbl_reff_brg_sumberanggaran`
--

INSERT INTO `tbl_reff_brg_sumberanggaran` (`id`, `nm_sumberanggaran`, `tahun_anggaran`, `keterangan`, `tgl_dibuat`, `tgl_diupdate`) VALUES
(6, 'BNPB', '2023-08-01', 'Anggaran dari BNPB', '2023-08-17 11:34:06', '2023-08-17 11:34:06'),
(7, 'BNPB', '2020-01-01', 'ANGGARAN dari BNPB', '2026-01-20 09:57:34', '2026-01-20 09:58:23'),
(8, 'APBD Provinsi', '2025-01-01', 'Anggaran Provinsi', '2026-04-09 05:10:19', '2026-04-09 05:11:44'),
(9, 'DSP BNPB', '2024-01-01', 'Dana Siap Pakai', '2026-04-09 05:13:23', '2026-04-09 05:13:23'),
(10, 'BNPB', '2022-01-01', 'DSP D3 BNPB', '2026-04-09 05:14:28', '2026-04-09 05:14:28'),
(11, 'APBD KABUPATEN', '2021-01-01', 'Bantuan Kabupaten', '2026-04-09 05:16:48', '2026-04-09 05:16:48'),
(12, 'APBD Provinsi', '2026-01-23', 'Bantuan Provinsi', '2026-04-09 05:17:54', '2026-04-09 05:17:54');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reff_jabatan`
--

CREATE TABLE `tbl_reff_jabatan` (
  `id` int(11) NOT NULL,
  `nm_jabatan` varchar(20) NOT NULL,
  `keterangan` text NOT NULL,
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_reff_jabatan`
--

INSERT INTO `tbl_reff_jabatan` (`id`, `nm_jabatan`, `keterangan`, `tgl_dibuat`, `tgl_diupdate`) VALUES
(13, 'kabid_darlog', 'kabid darlog', '2023-07-20 02:29:42', '2023-07-20 02:29:42'),
(14, 'staff_gudang', 'staff gudang', '2023-07-20 02:30:20', '2023-07-20 02:30:20'),
(15, 'admin', 'admin', '2023-07-20 02:30:35', '2023-07-20 02:30:35');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reff_pihak_kedua`
--

CREATE TABLE `tbl_reff_pihak_kedua` (
  `id` int(3) NOT NULL,
  `nm_pihak_kedua` varchar(50) NOT NULL,
  `nip_pihak_kedua` varchar(50) DEFAULT NULL,
  `jabatan_pihak_kedua` varchar(50) DEFAULT NULL,
  `alamat_pihak_kedua` text NOT NULL,
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_reff_pihak_kedua`
--

INSERT INTO `tbl_reff_pihak_kedua` (`id`, `nm_pihak_kedua`, `nip_pihak_kedua`, `jabatan_pihak_kedua`, `alamat_pihak_kedua`, `tgl_dibuat`, `tgl_diupdate`) VALUES
(3, 'Samsul', '9210223', 'Ketua', 'Situ Bagendit', '2023-08-17 11:35:56', '2023-10-24 01:27:00'),
(4, 'Andi', NULL, NULL, 'Sukaraja', '2023-08-22 02:44:06', '2023-10-24 01:25:56');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reff_pihak_kesatu`
--

CREATE TABLE `tbl_reff_pihak_kesatu` (
  `id` int(3) NOT NULL,
  `nm_pihak_kesatu` varchar(50) NOT NULL,
  `nip_pihak_kesatu` varchar(50) NOT NULL,
  `jabatan_pihak_kesatu` varchar(50) NOT NULL,
  `alamat_pihak_kesatu` text NOT NULL,
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_reff_pihak_kesatu`
--

INSERT INTO `tbl_reff_pihak_kesatu` (`id`, `nm_pihak_kesatu`, `nip_pihak_kesatu`, `jabatan_pihak_kesatu`, `alamat_pihak_kesatu`, `tgl_dibuat`, `tgl_diupdate`) VALUES
(4, 'Rohman', '12901232', 'Sekretaris', 'Ciamis', '2023-08-17 11:35:17', '2023-10-24 01:25:42'),
(5, 'Abdul', '102021', 'Ketua', 'Tasikmalaya', '2023-08-29 02:20:34', '2023-10-24 01:25:23');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reff_role`
--

CREATE TABLE `tbl_reff_role` (
  `id` int(11) NOT NULL,
  `nm_role` varchar(20) NOT NULL,
  `keterangan` text NOT NULL,
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_reff_role`
--

INSERT INTO `tbl_reff_role` (`id`, `nm_role`, `keterangan`, `tgl_dibuat`, `tgl_diupdate`) VALUES
(2, 'admin', 'Admin', '2023-07-15 19:35:31', '2023-07-20 02:25:30'),
(4, 'kabid_darlog', 'kepala bidang darurat logistik', '2023-07-20 02:25:22', '2023-07-20 02:25:22'),
(5, 'staff_gudang', 'staff gudang', '2023-07-20 02:25:45', '2023-07-20 02:25:45');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rell_brg_satuanstok`
--

CREATE TABLE `tbl_rell_brg_satuanstok` (
  `id` int(11) NOT NULL,
  `nm_barang` int(11) NOT NULL,
  `jum_barang_kecil` int(11) DEFAULT NULL,
  `satuan_barang_kecil` int(11) DEFAULT NULL,
  `harga_barang_kecil` int(11) DEFAULT NULL,
  `jum_barang_besar` int(11) DEFAULT NULL,
  `satuan_barang_besar` int(11) DEFAULT NULL,
  `harga_barang_besar` int(11) DEFAULT NULL,
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='tabel relasi satuan stok barang';

--
-- Dumping data for table `tbl_rell_brg_satuanstok`
--

INSERT INTO `tbl_rell_brg_satuanstok` (`id`, `nm_barang`, `jum_barang_kecil`, `satuan_barang_kecil`, `harga_barang_kecil`, `jum_barang_besar`, `satuan_barang_besar`, `harga_barang_besar`, `tgl_dibuat`, `tgl_diupdate`) VALUES
(27, 35, NULL, 8, NULL, 33, 8, 390858, '2026-04-09 05:22:07', '2026-04-09 06:56:35'),
(28, 36, NULL, NULL, NULL, 38, 8, 900000, '2026-04-09 05:47:12', '2026-04-09 07:06:04'),
(29, 37, NULL, NULL, NULL, 15, 10, 391000, '2026-04-09 06:59:36', '2026-04-09 06:59:36'),
(30, 38, NULL, NULL, NULL, 51, 10, 650000, '2026-04-09 07:09:57', '2026-04-09 07:09:57'),
(31, 39, NULL, NULL, NULL, 61, 11, 275000, '2026-04-09 07:12:53', '2026-04-09 07:12:53'),
(32, 40, NULL, NULL, NULL, 20, 11, 190300, '2026-04-09 07:15:22', '2026-04-09 07:15:22'),
(33, 41, NULL, NULL, NULL, 19, 11, 139100, '2026-04-09 07:16:21', '2026-04-09 07:16:21'),
(34, 42, NULL, NULL, NULL, 18, 11, 650000, '2026-04-09 07:30:07', '2026-04-09 07:30:07'),
(35, 43, NULL, NULL, NULL, 5, 13, 334665, '2026-04-09 08:08:30', '2026-04-09 08:08:30'),
(36, 44, NULL, NULL, NULL, 5, 14, 804750, '2026-04-09 08:09:52', '2026-04-09 08:11:18');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rell_role`
--

CREATE TABLE `tbl_rell_role` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `id_role` int(11) NOT NULL,
  `tgl_dibuat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_diupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_rell_role`
--

INSERT INTO `tbl_rell_role` (`id`, `id_karyawan`, `id_role`, `tgl_dibuat`, `tgl_diupdate`) VALUES
(12, 19, 2, '2023-07-20 02:32:34', '2023-07-20 02:32:34'),
(13, 22, 4, '2023-07-26 13:09:29', '2026-04-09 08:15:18'),
(14, 23, 5, '2023-07-30 05:22:10', '2025-12-11 23:45:10'),
(22, 34, 2, '2024-02-27 02:26:49', '2024-02-27 02:26:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_barang`
--
ALTER TABLE `tbl_barang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sumber_anggaran` (`sumber_anggaran`),
  ADD KEY `lokasi_barang` (`lokasi_barang`);

--
-- Indexes for table `tbl_barang_keluar`
--
ALTER TABLE `tbl_barang_keluar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nm_barang` (`nm_barang`),
  ADD KEY `nm_karyawan` (`karyawan_input`),
  ADD KEY `no_barang_keluar` (`no_barang_keluar`) USING BTREE,
  ADD KEY `pejabat` (`pejabat`),
  ADD KEY `pihak_kedua` (`pihak_kedua`);

--
-- Indexes for table `tbl_barang_masuk`
--
ALTER TABLE `tbl_barang_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nm_barang` (`nm_barang`),
  ADD KEY `nm_karyawan` (`karyawan_input`),
  ADD KEY `pejabat` (`pejabat`),
  ADD KEY `pihak_satu` (`pihak_kesatu`);

--
-- Indexes for table `tbl_barang_no_keluar`
--
ALTER TABLE `tbl_barang_no_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_barang_opname`
--
ALTER TABLE `tbl_barang_opname`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_input` (`karyawan_input`),
  ADD KEY `nm_barang` (`nm_barang`),
  ADD KEY `pejabat` (`pejabat`);

--
-- Indexes for table `tbl_karyawan`
--
ALTER TABLE `tbl_karyawan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jabatan` (`id_jabatan`) USING BTREE;

--
-- Indexes for table `tbl_reff_brg_lokasi`
--
ALTER TABLE `tbl_reff_brg_lokasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_reff_brg_satuan`
--
ALTER TABLE `tbl_reff_brg_satuan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_reff_brg_sumberanggaran`
--
ALTER TABLE `tbl_reff_brg_sumberanggaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_reff_jabatan`
--
ALTER TABLE `tbl_reff_jabatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_reff_pihak_kedua`
--
ALTER TABLE `tbl_reff_pihak_kedua`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_reff_pihak_kesatu`
--
ALTER TABLE `tbl_reff_pihak_kesatu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_reff_role`
--
ALTER TABLE `tbl_reff_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_rell_brg_satuanstok`
--
ALTER TABLE `tbl_rell_brg_satuanstok`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nm_barang` (`nm_barang`),
  ADD KEY `satuan_barang_kecil` (`satuan_barang_kecil`,`satuan_barang_besar`),
  ADD KEY `satuan_barang_besar` (`satuan_barang_besar`);

--
-- Indexes for table `tbl_rell_role`
--
ALTER TABLE `tbl_rell_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`),
  ADD KEY `id_karyawan_role` (`id_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_barang`
--
ALTER TABLE `tbl_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `tbl_barang_keluar`
--
ALTER TABLE `tbl_barang_keluar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `tbl_barang_masuk`
--
ALTER TABLE `tbl_barang_masuk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `tbl_barang_no_keluar`
--
ALTER TABLE `tbl_barang_no_keluar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `tbl_barang_opname`
--
ALTER TABLE `tbl_barang_opname`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `tbl_karyawan`
--
ALTER TABLE `tbl_karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `tbl_reff_brg_lokasi`
--
ALTER TABLE `tbl_reff_brg_lokasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_reff_brg_satuan`
--
ALTER TABLE `tbl_reff_brg_satuan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_reff_brg_sumberanggaran`
--
ALTER TABLE `tbl_reff_brg_sumberanggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_reff_jabatan`
--
ALTER TABLE `tbl_reff_jabatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_reff_pihak_kedua`
--
ALTER TABLE `tbl_reff_pihak_kedua`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_reff_pihak_kesatu`
--
ALTER TABLE `tbl_reff_pihak_kesatu`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_reff_role`
--
ALTER TABLE `tbl_reff_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_rell_brg_satuanstok`
--
ALTER TABLE `tbl_rell_brg_satuanstok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tbl_rell_role`
--
ALTER TABLE `tbl_rell_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_barang`
--
ALTER TABLE `tbl_barang`
  ADD CONSTRAINT `tbl_barang_ibfk_1` FOREIGN KEY (`sumber_anggaran`) REFERENCES `tbl_reff_brg_sumberanggaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_barang_ibfk_2` FOREIGN KEY (`lokasi_barang`) REFERENCES `tbl_reff_brg_lokasi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_barang_keluar`
--
ALTER TABLE `tbl_barang_keluar`
  ADD CONSTRAINT `tbl_barang_keluar_ibfk_1` FOREIGN KEY (`nm_barang`) REFERENCES `tbl_barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_barang_keluar_ibfk_2` FOREIGN KEY (`karyawan_input`) REFERENCES `tbl_karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_barang_keluar_ibfk_3` FOREIGN KEY (`no_barang_keluar`) REFERENCES `tbl_barang_no_keluar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_barang_keluar_ibfk_4` FOREIGN KEY (`pejabat`) REFERENCES `tbl_karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_barang_keluar_ibfk_5` FOREIGN KEY (`pihak_kedua`) REFERENCES `tbl_reff_pihak_kedua` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_barang_masuk`
--
ALTER TABLE `tbl_barang_masuk`
  ADD CONSTRAINT `tbl_barang_masuk_ibfk_1` FOREIGN KEY (`nm_barang`) REFERENCES `tbl_barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_barang_masuk_ibfk_2` FOREIGN KEY (`karyawan_input`) REFERENCES `tbl_karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_barang_masuk_ibfk_3` FOREIGN KEY (`pejabat`) REFERENCES `tbl_karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_barang_masuk_ibfk_4` FOREIGN KEY (`pihak_kesatu`) REFERENCES `tbl_reff_pihak_kesatu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_barang_opname`
--
ALTER TABLE `tbl_barang_opname`
  ADD CONSTRAINT `tbl_barang_opname_ibfk_1` FOREIGN KEY (`karyawan_input`) REFERENCES `tbl_karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_barang_opname_ibfk_2` FOREIGN KEY (`nm_barang`) REFERENCES `tbl_barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_barang_opname_ibfk_3` FOREIGN KEY (`pejabat`) REFERENCES `tbl_karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_karyawan`
--
ALTER TABLE `tbl_karyawan`
  ADD CONSTRAINT `tbl_karyawan_ibfk_1` FOREIGN KEY (`id_jabatan`) REFERENCES `tbl_reff_jabatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_rell_brg_satuanstok`
--
ALTER TABLE `tbl_rell_brg_satuanstok`
  ADD CONSTRAINT `tbl_rell_brg_satuanstok_ibfk_1` FOREIGN KEY (`nm_barang`) REFERENCES `tbl_barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_rell_brg_satuanstok_ibfk_2` FOREIGN KEY (`satuan_barang_kecil`) REFERENCES `tbl_reff_brg_satuan` (`id`),
  ADD CONSTRAINT `tbl_rell_brg_satuanstok_ibfk_3` FOREIGN KEY (`satuan_barang_besar`) REFERENCES `tbl_reff_brg_satuan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_rell_role`
--
ALTER TABLE `tbl_rell_role`
  ADD CONSTRAINT `tbl_rell_role_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `tbl_karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_rell_role_ibfk_2` FOREIGN KEY (`id_role`) REFERENCES `tbl_reff_role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
