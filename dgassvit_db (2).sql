-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 03 Feb 2025 pada 02.34
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dgassvit_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama_lengkap`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@dgassvit.com', '2025-01-18 10:52:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `paket` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `provinsi` varchar(50) NOT NULL,
  `kota` varchar(50) NOT NULL,
  `alamat` text NOT NULL,
  `kurir` varchar(20) NOT NULL,
  `paket_kurir` varchar(50) NOT NULL,
  `ongkir` decimal(10,2) NOT NULL,
  `estimasi` varchar(20) NOT NULL,
  `total_pembayaran` decimal(10,2) NOT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `status` enum('pending','processing','shipped','delivered','cancelled','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `paket`, `nama`, `telepon`, `provinsi`, `kota`, `alamat`, `kurir`, `paket_kurir`, `ongkir`, `estimasi`, `total_pembayaran`, `tanggal`, `status`) VALUES
(1, '1', 'joko', '085858587', '', '', 'halooo', '', '', 0.00, '', 0.00, '2025-01-18 11:07:29', 'cancelled'),
(2, '1', 'joko', '085858587', '', '', 'halooo', '', '', 0.00, '', 300000.00, '2025-01-18 11:08:12', 'pending'),
(3, '3', 'jnana', '0842423424', 'DI Yogyakarta', 'Kulon Progo', 'jl.bunga', 'jne', 'JTR', 65000.00, '4-5', 890000.00, '2025-01-18 11:14:33', 'delivered'),
(4, '6', 'ian', '087414132', 'DI Yogyakarta', 'Sleman', 'deket sch', 'jne', 'JTR', 55000.00, '3-4', 1555000.00, '2025-01-18 20:07:09', 'processing'),
(5, '1', 'Ronald Alexaner', '089674398585', 'DI Yogyakarta', 'Bantul', 'JALAN RAJAWALI NO. 213 DAERAH ISTIMEWA YOGYAKARTA, KAB BANTUL, BANGUNTAPAN, BANGUNTAPAN', 'jne', 'JTR', 55000.00, '3-4', 355000.00, '2025-01-18 20:36:00', 'pending');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_tracking`
--

CREATE TABLE `order_tracking` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `petugas`
--

CREATE TABLE `petugas` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `no_wa` varchar(20) NOT NULL,
  `no_rekening` varchar(50) DEFAULT NULL,
  `nama_bank` varchar(50) DEFAULT NULL,
  `link_page` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `petugas`
--

INSERT INTO `petugas` (`id`, `username`, `password`, `nama_lengkap`, `no_wa`, `no_rekening`, `nama_bank`, `link_page`, `created_at`) VALUES
(25, 'kambing', '$2y$10$wyMfA2Ql6clQc3LoWyny2OddKOnvgs26CV1oodAFs/acPgBiYF.i6', 'tini', '6285727654539', '3425423523', 'Hana bank', 'p/kambing-6791060486bb1.php', '2025-01-22 14:51:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jumlah_botol` int(11) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `nama`, `jumlah_botol`, `harga`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Paket 1 Botol', 1, 300000.00, 'Paket hemat 1 botol D-Gassvit', '2025-01-18 10:52:13', '2025-01-18 10:52:13'),
(2, 'Paket 3 Botol', 3, 825000.00, 'Paket hemat 3 botol D-Gassvit', '2025-01-18 10:52:13', '2025-01-18 10:52:13'),
(3, 'Paket 6 Botol', 6, 1500000.00, 'Paket hemat 6 botol D-Gassvit', '2025-01-18 10:52:13', '2025-01-18 10:52:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'site_title', 'D-Gassvit Energy Boost', 'Judul website', '2025-01-18 10:52:13'),
(2, 'admin_whatsapp', '6281215879698', 'Nomor WhatsApp Admin', '2025-01-19 01:27:58'),
(3, 'shipping_origin', '152', 'ID Kota asal pengiriman (Jakarta Pusat)', '2025-01-18 10:52:13'),
(4, 'product_weight', '1000', 'Berat produk dalam gram', '2025-01-18 10:52:13'),
(8, 'rajaongkir_key', '58188953664b7e38c8d8d3da5a3b567f', 'API Key Raja Ongkir', '2025-01-18 10:58:47'),
(9, 'available_couriers', 'JNE,TIKI,POS', 'Daftar kurir yang tersedia', '2025-01-18 10:58:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `video_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `testimonials`
--

INSERT INTO `testimonials` (`id`, `judul`, `deskripsi`, `video_path`, `created_at`) VALUES
(1, 'konsumsi gassvit baru 3 hari', 'pasien stroke bisa gerakan kaki dan tangan lagi\r\n', 'asset/video/678bebdc8e47d_WhatsApp Video 2025-01-10 at 21.08.45_3ee4eb79.mp4', '2025-01-18 17:58:52'),
(3, 'Stroke 3 Bulan', 'Alhamdulillaah Setelah Minum 4 Butir D-GASSVIT (1 Hari) Kaki Sudah Mulai Bisa Di Tekuk Dan Duduk Bersila..', 'asset/video/678bec6cca522_WhatsApp Video 2025-01-09 at 20.06.47_9fb5557b.mp4', '2025-01-18 18:01:16'),
(4, 'Saraf kejepit', 'sudah ke beberapa dokter tidak ada perubahan, tapi setelah minum GASSVIT sekarang sudah sembuh', 'asset/video/678bed5e3e494_WhatsApp Video 2025-01-08 at 16.59.05_2e3f901c.mp4', '2025-01-18 18:05:18');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_tanggal` (`tanggal`),
  ADD KEY `idx_orders_status` (`status`);

--
-- Indeks untuk tabel `order_tracking`
--
ALTER TABLE `order_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tracking_order_id` (`order_id`);

--
-- Indeks untuk tabel `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `link_page` (`link_page`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indeks untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `order_tracking`
--
ALTER TABLE `order_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `order_tracking`
--
ALTER TABLE `order_tracking`
  ADD CONSTRAINT `order_tracking_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
