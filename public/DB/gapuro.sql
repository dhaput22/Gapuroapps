-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 16, 2026 at 09:26 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gapuro`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `fg_delivery_scans`
--

CREATE TABLE `fg_delivery_scans` (
  `id` bigint UNSIGNED NOT NULL,
  `label_id` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `part_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `part_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lot_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty_box` int UNSIGNED NOT NULL DEFAULT '0',
  `scanned_at` timestamp NULL DEFAULT NULL,
  `operator_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `delivery_at` timestamp NULL DEFAULT NULL,
  `delivery_operator_id` bigint UNSIGNED DEFAULT NULL,
  `transfer_card_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fg_delivery_scans`
--

INSERT INTO `fg_delivery_scans` (`id`, `label_id`, `part_code`, `part_name`, `lot_no`, `qty_box`, `scanned_at`, `operator_id`, `created_by`, `delivery_at`, `delivery_operator_id`, `transfer_card_no`, `created_at`, `updated_at`) VALUES
(1, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16001+', 165, '2026-04-15 20:58:23', 1, 1, '2026-04-15 21:00:24', 1, NULL, '2026-04-15 21:00:24', '2026-04-15 21:00:24'),
(2, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16002+', 165, '2026-04-15 20:58:26', 1, 1, '2026-04-15 21:00:26', 1, NULL, '2026-04-15 21:00:26', '2026-04-15 21:00:26'),
(3, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16004+', 165, '2026-04-15 20:58:30', 1, 1, '2026-04-15 21:00:28', 1, NULL, '2026-04-15 21:00:28', '2026-04-15 21:00:28'),
(4, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16003+', 165, '2026-04-15 20:58:28', 1, 1, '2026-04-15 21:00:31', 1, NULL, '2026-04-15 21:00:31', '2026-04-15 21:00:31'),
(5, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16005+', 165, '2026-04-15 20:58:33', 1, 1, '2026-04-15 21:00:42', 1, NULL, '2026-04-15 21:00:42', '2026-04-15 21:00:42');

-- --------------------------------------------------------

--
-- Table structure for table `fg_receiving_scans`
--

CREATE TABLE `fg_receiving_scans` (
  `id` bigint UNSIGNED NOT NULL,
  `label_id` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `part_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `part_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lot_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty_box` int UNSIGNED NOT NULL DEFAULT '0',
  `scanned_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `operator_id` bigint UNSIGNED DEFAULT NULL,
  `scan_state` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'receiving',
  `delivery_at` timestamp NULL DEFAULT NULL,
  `delivery_operator_id` bigint UNSIGNED DEFAULT NULL,
  `transfer_card_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fg_receiving_scans`
--

INSERT INTO `fg_receiving_scans` (`id`, `label_id`, `part_code`, `part_name`, `lot_no`, `qty_box`, `scanned_at`, `created_by`, `created_at`, `updated_at`, `operator_id`, `scan_state`, `delivery_at`, `delivery_operator_id`, `transfer_card_no`) VALUES
(6, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16006+', 165, '2026-04-15 20:58:35', 1, '2026-04-15 20:58:35', '2026-04-15 20:58:35', 1, 'receiving', NULL, NULL, NULL),
(7, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16007+', 165, '2026-04-15 20:58:38', 1, '2026-04-15 20:58:38', '2026-04-15 20:58:38', 1, 'receiving', NULL, NULL, NULL),
(8, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16008+', 165, '2026-04-15 20:58:42', 1, '2026-04-15 20:58:42', '2026-04-15 20:58:42', 1, 'receiving', NULL, NULL, NULL),
(9, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16009+', 165, '2026-04-15 20:58:45', 1, '2026-04-15 20:58:45', '2026-04-15 20:58:45', 1, 'receiving', NULL, NULL, NULL),
(10, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16010+', 165, '2026-04-15 20:58:48', 1, '2026-04-15 20:58:48', '2026-04-15 20:58:48', 1, 'receiving', NULL, NULL, NULL),
(11, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16011+', 165, '2026-04-15 20:58:51', 1, '2026-04-15 20:58:51', '2026-04-15 20:58:51', 1, 'receiving', NULL, NULL, NULL),
(12, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16012+', 165, '2026-04-15 20:58:53', 1, '2026-04-15 20:58:53', '2026-04-15 20:58:53', 1, 'receiving', NULL, NULL, NULL),
(13, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16013+', 165, '2026-04-15 20:58:56', 1, '2026-04-15 20:58:56', '2026-04-15 20:58:56', 1, 'receiving', NULL, NULL, NULL),
(14, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16014+', 165, '2026-04-15 20:58:58', 1, '2026-04-15 20:58:58', '2026-04-15 20:58:58', 1, 'receiving', NULL, NULL, NULL),
(15, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16015+', 165, '2026-04-15 20:59:01', 1, '2026-04-15 20:59:01', '2026-04-15 20:59:01', 1, 'receiving', NULL, NULL, NULL),
(16, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16016+', 165, '2026-04-15 20:59:03', 1, '2026-04-15 20:59:03', '2026-04-15 20:59:03', 1, 'receiving', NULL, NULL, NULL),
(17, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16017+', 165, '2026-04-15 20:59:06', 1, '2026-04-15 20:59:06', '2026-04-15 20:59:06', 1, 'receiving', NULL, NULL, NULL),
(18, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16018+', 165, '2026-04-15 20:59:08', 1, '2026-04-15 20:59:08', '2026-04-15 20:59:08', 1, 'receiving', NULL, NULL, NULL),
(19, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16020+', 165, '2026-04-15 20:59:13', 1, '2026-04-15 20:59:13', '2026-04-15 20:59:13', 1, 'receiving', NULL, NULL, NULL),
(20, NULL, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16019+', 165, '2026-04-15 20:59:28', 1, '2026-04-15 20:59:28', '2026-04-15 20:59:28', 1, 'receiving', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fg_swa_plans`
--

CREATE TABLE `fg_swa_plans` (
  `id` bigint UNSIGNED NOT NULL,
  `part_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `part_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_lot_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `end_lot_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty_box` int UNSIGNED NOT NULL,
  `total_plan` int UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fg_swa_plans`
--

INSERT INTO `fg_swa_plans` (`id`, `part_code`, `part_name`, `start_lot_no`, `end_lot_no`, `qty_box`, `total_plan`, `created_by`, `created_at`, `updated_at`) VALUES
(1, '171739801', 'BOTTLE,70,5300', 'BL23Q4-16001+', 'BL23Q4-16085+', 165, 14025, 1, '2026-04-15 20:56:57', '2026-04-15 20:57:53');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
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
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2025_12_06_042517_create_users_table', 1),
(4, '2025_12_06_044424_create_sessions_table', 1),
(5, '2026_03_20_000001_create_fg_swa_plans_table', 1),
(6, '2026_03_20_000002_create_fg_receiving_scans_table', 1),
(7, '2026_03_20_000003_create_operators_table', 1),
(8, '2026_03_20_000004_add_operator_id_to_fg_receiving_scans_table', 1),
(9, '2026_03_27_000005_add_delivery_fields_to_fg_receiving_scans_table', 1),
(10, '2026_03_27_000006_create_fg_delivery_scans_table', 1),
(11, '2026_04_16_000007_normalize_user_roles_to_super_admin_scheme', 1),
(12, '2026_04_16_000008_create_initial_super_admin_account', 2);

-- --------------------------------------------------------

--
-- Table structure for table `operators`
--

CREATE TABLE `operators` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `operators`
--

INSERT INTO `operators` (`id`, `employee_id`, `name`, `department`, `created_at`, `updated_at`) VALUES
(1, '2250357', 'Muhammad Dafa Putra', 'IK-Prod', '2026-04-15 17:51:42', '2026-04-15 17:51:42');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'leader',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `role`, `status`, `last_login_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin', 'super_admin', 'active', NULL, '$2y$12$rua7NuF6AHH7uTDTxitO3OV3PEBGPcuS.vAQ5AfGlSNqu2qYum5Oa', NULL, '2026-04-15 17:48:59', '2026-04-15 17:48:59'),
(2, 'Edi Sukasno', 'peb2190888', 'leader', 'active', NULL, '$2y$12$1Ko/Tj9QETHLNPmlem1y0OexdrDIwRpkoKUGVcBC3QnPynQpH4.FO', NULL, '2026-04-15 17:56:04', '2026-04-15 17:56:04'),
(3, 'Dafa', 'dafa22', 'staff', 'active', NULL, '$2y$12$GUrDEbCreh/F7oOh7GmFcerjofb0uNiBHL.EImOe0S.yUbSrK3C1i', NULL, '2026-04-15 17:56:41', '2026-04-15 17:56:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fg_delivery_scans`
--
ALTER TABLE `fg_delivery_scans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fg_delivery_scans_operator_id_foreign` (`operator_id`),
  ADD KEY `fg_delivery_scans_created_by_foreign` (`created_by`),
  ADD KEY `fg_delivery_scans_delivery_operator_id_foreign` (`delivery_operator_id`),
  ADD KEY `fg_delivery_scan_lot_idx` (`part_code`,`lot_no`),
  ADD KEY `fg_delivery_scan_date_idx` (`delivery_at`);

--
-- Indexes for table `fg_receiving_scans`
--
ALTER TABLE `fg_receiving_scans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fg_receiving_scans_created_by_foreign` (`created_by`),
  ADD KEY `fg_receiving_scan_lot_idx` (`part_code`,`lot_no`),
  ADD KEY `fg_receiving_scans_operator_id_foreign` (`operator_id`),
  ADD KEY `fg_receiving_scans_delivery_operator_id_foreign` (`delivery_operator_id`),
  ADD KEY `fg_receiving_scan_delivery_idx` (`scan_state`,`delivery_at`);

--
-- Indexes for table `fg_swa_plans`
--
ALTER TABLE `fg_swa_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fg_swa_plan_unique_range` (`part_code`,`start_lot_no`,`end_lot_no`),
  ADD KEY `fg_swa_plans_created_by_foreign` (`created_by`),
  ADD KEY `fg_swa_plan_range_idx` (`part_code`,`start_lot_no`,`end_lot_no`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `operators`
--
ALTER TABLE `operators`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `operators_employee_id_unique` (`employee_id`),
  ADD KEY `operators_department_index` (`department`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fg_delivery_scans`
--
ALTER TABLE `fg_delivery_scans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `fg_receiving_scans`
--
ALTER TABLE `fg_receiving_scans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `fg_swa_plans`
--
ALTER TABLE `fg_swa_plans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `operators`
--
ALTER TABLE `operators`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fg_delivery_scans`
--
ALTER TABLE `fg_delivery_scans`
  ADD CONSTRAINT `fg_delivery_scans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fg_delivery_scans_delivery_operator_id_foreign` FOREIGN KEY (`delivery_operator_id`) REFERENCES `operators` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fg_delivery_scans_operator_id_foreign` FOREIGN KEY (`operator_id`) REFERENCES `operators` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `fg_receiving_scans`
--
ALTER TABLE `fg_receiving_scans`
  ADD CONSTRAINT `fg_receiving_scans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fg_receiving_scans_delivery_operator_id_foreign` FOREIGN KEY (`delivery_operator_id`) REFERENCES `operators` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fg_receiving_scans_operator_id_foreign` FOREIGN KEY (`operator_id`) REFERENCES `operators` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `fg_swa_plans`
--
ALTER TABLE `fg_swa_plans`
  ADD CONSTRAINT `fg_swa_plans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;