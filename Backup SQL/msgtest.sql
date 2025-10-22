-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 22, 2025 at 03:42 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `msgtest`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_list`
--

CREATE TABLE `cart_list` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_list`
--

INSERT INTO `cart_list` (`id`, `customer_id`, `product_id`, `quantity`) VALUES
(11, 48, 39, 1),
(29, 19, 33, 1),
(30, 19, 60, 1);

-- --------------------------------------------------------

--
-- Table structure for table `category_list`
--

CREATE TABLE `category_list` (
  `id` int NOT NULL,
  `product_type_id` int DEFAULT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `other` tinyint(1) DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_list`
--

INSERT INTO `category_list` (`id`, `product_type_id`, `name`, `description`, `other`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, 2, 'ของเล่นหมวดหมู่ 0-2 ปี', 'ของเล่นหมวดหมู่ 0-2 ปี', 0, 1, 0, '2025-06-05 11:59:25', '2025-08-01 01:21:53'),
(2, NULL, 'ของเล่นหมวดหมู่ 3-4 ปี', 'ของเล่นหมวดหมู่ 3-4 ปี', 0, 1, 0, '2025-06-05 11:59:38', '2025-07-17 09:12:36'),
(3, NULL, 'ของเล่นหมวดหมู่ 5-6 ปี', 'ของเล่นหมวดหมู่ 5-6 ปี', 0, 1, 0, '2025-06-05 11:59:45', '2025-07-17 09:12:36'),
(4, NULL, 'ของเล่นกระดานลื่น', 'ของเล่นกระดานลื่น สุดสนุก!', 0, 1, 0, '2025-06-05 13:19:47', '2025-07-17 09:12:36'),
(5, 11, 'เทส', 'เทส', 0, 1, 1, '2025-06-12 09:57:50', '2025-09-05 15:48:06'),
(6, NULL, 'Keyboard/คีย์บอร์ด', 'Keyboard/คีย์บอร์ด', 0, 1, 0, '2025-06-17 13:37:41', '2025-07-17 09:12:36'),
(7, 3, 'HEADSET/หูฟัง', 'HEADSET/หูฟัง', 0, 1, 0, '2025-06-17 13:42:26', '2025-07-17 09:12:36'),
(8, NULL, 'ขนม', 'ขนม', 0, 1, 0, '2025-06-17 13:50:33', '2025-07-17 09:12:36'),
(9, NULL, 'ผงชงดื่ม', 'ผงชงดื่ม', 0, 1, 0, '2025-06-17 14:20:49', '2025-07-17 09:12:36'),
(10, 8, 'เทส2', 'เทส2', 0, 1, 1, '2025-06-17 21:43:06', '2025-07-22 10:28:35'),
(11, 1, 'ทดสอบครั้งที่1', 'ทดสอบครั้งที่1', 0, 1, 1, '2025-07-16 11:24:32', '2025-07-22 10:19:13'),
(12, 4, 'ทดสอบ2', 'ทดสอบ2', 0, 1, 1, '2025-07-16 13:59:35', '2025-07-22 10:29:44'),
(13, 1, 'เครื่องเล่นสนามกลางแจ้ง', 'เครื่องเล่นสนามกลางแจ้ง', 0, 1, 0, '2025-07-16 16:08:17', '2025-07-16 16:08:17'),
(14, 1, 'เครื่องเล่นเด็กในที่ร่ม', 'เครื่องเล่นเด็กในที่ร่ม', 0, 1, 0, '2025-07-16 16:08:42', '2025-07-16 16:08:42'),
(15, 2, 'ของเล่นไม้', 'ของเล่นไม้', 0, 1, 0, '2025-07-16 16:09:19', '2025-07-16 16:11:42'),
(16, 2, 'ของเล่นพลาสติก', 'ของเล่นพลาสติก', 0, 1, 0, '2025-07-16 16:09:38', '2025-07-16 16:09:39'),
(17, 2, 'บัตรคำประกอบภาพ', 'บัตรคำประกอบภาพ', 0, 1, 0, '2025-07-16 16:10:09', '2025-07-16 16:10:09'),
(18, 2, 'ตัวต่อเสริมพัฒนาการ', 'ตัวต่อเสริมพัฒนาการ', 0, 1, 0, '2025-07-16 16:10:37', '2025-07-16 16:10:37'),
(19, 4, 'สื่อทำมือ', 'สื่อทำมือ', 0, 1, 0, '2025-07-16 16:10:56', '2025-07-16 16:10:56'),
(20, 3, 'หนังสือนิทาน', 'หนังสือนิทาน', 0, 1, 0, '2025-07-16 16:11:13', '2025-08-01 09:27:25'),
(21, 3, 'หนังสือเรียน', 'หนังสือเรียน', 0, 1, 0, '2025-07-16 16:11:31', '2025-07-16 16:11:31'),
(22, 5, 'ชุดประเมินพัฒนาการ', 'ชุดประเมินพัฒนาการ', 0, 1, 0, '2025-07-16 16:12:11', '2025-07-16 16:12:11'),
(23, 3, 'หนังสือการ์ตูน / มังงะ', 'หนังสือการ์ตูน / มังงะ', 0, 1, 0, '2025-07-18 09:15:13', '2025-07-22 13:56:29'),
(24, 6, 'อื่น ๆ', 'อื่น ๆ', 1, 1, 0, '2025-07-21 16:01:11', '2025-08-25 10:45:44'),
(25, 11, 'ทดสอบลบ', '', 0, 1, 1, '2025-07-22 10:00:11', '2025-09-05 15:49:08'),
(26, 12, 'สมุดโน็ต', '', 0, 1, 0, '2025-07-22 18:38:08', '2025-07-22 18:38:08'),
(27, 14, '1', '', 0, 1, 1, '2025-08-28 18:23:37', '2025-08-29 09:45:25'),
(28, 15, 'ของเล่นรถเด็ก', 'ของเล่นรถเด็ก', 0, 1, 0, '2025-08-28 18:48:37', '2025-08-28 18:48:37'),
(29, 16, 'kasd;kasjdlkajsd;lkasjasdasd', 'aslkdjasl;kdja;slkdja;skd', 1, 1, 1, '2025-09-05 15:49:28', '2025-09-05 15:51:22'),
(30, 3, 'aaa', 'aaa', 1, 1, 1, '2025-09-05 16:06:48', '2025-09-05 16:08:36'),
(31, 1, 'sadas', 'dassasd', 1, 1, 1, '2025-09-05 16:09:05', '2025-09-05 16:09:15'),
(32, 1, 'afafasda', 'adasdasdasd', 1, 1, 1, '2025-09-05 16:12:30', '2025-09-05 16:12:46'),
(33, 2, '1', '1', 0, 1, 1, '2025-09-11 11:28:37', '2025-09-11 11:28:42'),
(34, 20, 'ทดสอบ', '', 0, 1, 0, '2025-10-15 09:39:34', '2025-10-15 09:39:36'),
(35, 21, 'ทดสอบ1', '', 0, 1, 0, '2025-10-15 09:39:43', '2025-10-15 09:39:43'),
(36, 22, 'ทดสอบ2', '', 0, 1, 0, '2025-10-15 09:39:51', '2025-10-15 09:39:51'),
(37, 23, 'ทดสอบ3', '', 0, 1, 0, '2025-10-15 09:40:01', '2025-10-15 09:40:01'),
(38, 21, 'ทดสอบทดสอบ', '', 0, 0, 0, '2025-10-15 09:45:21', '2025-10-15 09:45:21'),
(39, 21, 'ทดสอบทดสอบทดสอบ', '', 0, 1, 0, '2025-10-15 09:45:28', '2025-10-15 09:45:28'),
(40, 21, 'ทดสอบทดสอบทดสอบทดสอบ', '', 0, 1, 0, '2025-10-15 09:45:36', '2025-10-15 09:45:36');

-- --------------------------------------------------------

--
-- Table structure for table `coupon_code_list`
--

CREATE TABLE `coupon_code_list` (
  `id` int NOT NULL,
  `coupon_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `type` enum('fixed','percent','free_shipping','code') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'fixed',
  `cpromo` tinyint(1) NOT NULL DEFAULT '0',
  `discount_value` float DEFAULT '0',
  `minimum_order` float DEFAULT '0',
  `limit_coupon` int DEFAULT NULL,
  `unl_coupon` tinyint(1) DEFAULT '0',
  `coupon_amount` int DEFAULT NULL,
  `unl_amount` tinyint(1) NOT NULL DEFAULT '0',
  `all_products_status` tinyint(1) NOT NULL DEFAULT '0',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupon_code_list`
--

INSERT INTO `coupon_code_list` (`id`, `coupon_code`, `name`, `description`, `type`, `cpromo`, `discount_value`, `minimum_order`, `limit_coupon`, `unl_coupon`, `coupon_amount`, `unl_amount`, `all_products_status`, `start_date`, `end_date`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, 'TEST-01', '  สั่งซื้อลดไปเลย 20%!!!', 'ลดกันไปเลยจุก ๆ ไม่มีขั้นต่ำ ลดถึง 20%!!! ใช้ได้ไม่จำกัดครั้ง!!!', 'percent', 0, 20, 0, NULL, 1, NULL, 1, 1, '2025-08-08 17:01:00', '2024-08-28 17:01:00', 0, 0, '2025-08-08 17:01:16', '2025-10-20 11:03:23'),
(2, 'TEST-02', '    ส่งฟรีไม่มีขั้นต่ำ', 'ส่งฟรีไม่มีขั้นต่ำ', 'free_shipping', 1, 0, 0, 3, 0, 9999, 0, 1, '2025-08-13 14:30:00', '2026-08-31 14:30:00', 1, 0, '2025-08-13 14:31:01', '2025-09-11 14:14:27'),
(3, 'TEST-03', '   สั่งซื้อครบลดไปเลย 1000 บาท!!!', 'สั่งซื้อครบ 500.- รับส่วนลดทันที 1000 บาท!!!', 'fixed', 1, 1000, 500, NULL, 1, NULL, 1, 1, '2025-08-14 11:02:00', '2025-09-30 11:02:00', 0, 0, '2025-08-14 11:02:18', '2025-09-30 11:02:50'),
(4, 'TEST-04', ' ซื้อครบลดไป 15%!!!', 'ซื้อครบ 500.- ลดไป 15%!!!', 'percent', 1, 15, 500, 4, 0, 5, 0, 1, '2025-08-14 11:02:00', '2025-09-30 11:03:00', 0, 0, '2025-08-14 11:03:05', '2025-09-30 11:03:50'),
(5, 'FRESHP702025', ' ส่งฟรีมีขั้นต่ำ', 'คูปองส่งฟรีมีขั้นต่ำ 70 บาท.-', 'free_shipping', 1, 0, 0, 3, 0, NULL, 1, 1, '2025-08-15 11:16:00', '2026-08-12 11:16:00', 1, 0, '2025-08-15 11:16:47', '2025-09-12 11:54:58'),
(6, 'TESTTT1', '              ทดสอบ1', ' ทดสอบ1', 'percent', 1, 20, 10, 3, 0, 7, 0, 1, '2025-08-20 16:21:00', '2026-08-05 16:21:00', 0, 1, '2025-08-20 16:21:22', '2025-09-11 16:31:18'),
(7, 'TESTAMO-01', ' ทดสอบจำนวน', ' ทดสอบจำนวน', 'percent', 1, 99, 0, 10, 0, 4, 0, 1, '2025-08-21 10:59:00', '2025-09-04 10:59:00', 0, 1, '2025-08-21 10:59:14', '2025-09-11 13:49:46'),
(8, 'TEST1001', '  ทดสอบคูปอง', ' ทดสอบคูปอง', 'fixed', 1, 500, 50, 2, 0, NULL, 1, 0, '2025-08-27 13:17:00', '2025-09-03 13:17:00', 0, 1, '2025-08-27 13:18:28', '2025-09-11 13:49:49'),
(9, 'TEST555', ' ทดสอบบบบบ', ' ทดสอบบบบบ', 'fixed', 1, 500, 1000, 10, 0, 10, 0, 0, '2025-08-27 14:39:00', '2025-09-03 14:40:00', 0, 1, '2025-08-27 14:40:13', '2025-09-11 10:21:47'),
(10, 'SK1001', 'สงกราน', '', 'percent', 1, 10, 5, 1, 0, 5, 0, 0, '2025-08-28 18:32:00', '2025-08-29 18:32:00', 0, 0, '2025-08-28 18:32:56', '2025-08-29 18:32:50'),
(11, 'KID2025', '  KID', 'ลดทุกสินค้า10%!!', 'percent', 1, 10, 0, NULL, 1, 200, 0, 1, '2025-08-28 18:54:00', '2025-08-29 18:54:00', 0, 1, '2025-08-28 18:54:56', '2025-09-11 10:38:51'),
(12, 'NEW', ' TESTNEW', ' TESTNEW', 'percent', 1, 5, 0, 100, 0, 90, 0, 1, '2025-09-03 09:25:00', '2025-10-11 09:25:00', 0, 1, '2025-09-03 09:25:49', '2025-09-11 13:49:53'),
(13, ' test', '  test', ' test', 'fixed', 1, 11, 11, 1, 0, 1, 0, 1, '2025-09-11 10:04:00', '2025-09-12 10:04:00', 0, 1, '2025-09-11 10:04:16', '2025-09-11 10:38:59'),
(14, ' test1', ' test1', ' test', 'fixed', 1, 11, 11, 1, 0, 1, 0, 1, '2025-09-11 11:03:00', '2025-09-18 11:03:00', 0, 1, '2025-09-11 11:03:56', '2025-09-11 13:49:57'),
(15, ' testๅ', ' testๅ', ' test', 'fixed', 1, 11, 11, 1, 0, 1, 0, 0, '2025-09-11 11:42:00', '2025-09-11 11:43:00', 0, 1, '2025-09-11 11:43:02', '2025-09-11 13:49:59'),
(16, 'DISCOUNT061068', ' ลดแล้วลดอีก ลดเลย 20%', ' ลดแล้วลดอีก ลดเลย 20%', 'percent', 1, 20, 0, NULL, 1, NULL, 1, 1, '2025-10-06 13:44:00', '2025-10-31 13:44:00', 1, 0, '2025-10-06 13:44:53', '2025-10-06 13:44:53');

-- --------------------------------------------------------

--
-- Table structure for table `coupon_code_products`
--

CREATE TABLE `coupon_code_products` (
  `id` int NOT NULL,
  `coupon_code_id` int NOT NULL,
  `product_id` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupon_code_products`
--

INSERT INTO `coupon_code_products` (`id`, `coupon_code_id`, `product_id`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(55, 4, 27, 1, 0, '2025-08-21 14:37:53', '2025-08-21 14:37:53'),
(56, 4, 14, 1, 0, '2025-08-21 14:37:53', '2025-08-21 14:37:53'),
(57, 4, 23, 1, 0, '2025-08-21 14:37:53', '2025-08-21 14:37:53'),
(58, 4, 35, 1, 0, '2025-08-21 14:37:53', '2025-08-21 14:37:53'),
(59, 4, 22, 1, 0, '2025-08-21 14:37:53', '2025-08-21 14:37:53'),
(60, 4, 21, 1, 0, '2025-08-21 14:37:53', '2025-08-21 14:37:53'),
(61, 8, 30, 1, 0, '2025-08-27 13:18:59', '2025-08-27 13:18:59'),
(62, 8, 41, 1, 0, '2025-08-27 13:18:59', '2025-08-27 13:18:59'),
(63, 8, 27, 1, 0, '2025-08-27 13:18:59', '2025-08-27 13:18:59'),
(64, 8, 14, 1, 0, '2025-08-27 13:18:59', '2025-08-27 13:18:59'),
(65, 8, 25, 1, 0, '2025-08-27 13:18:59', '2025-08-27 13:18:59'),
(66, 8, 23, 1, 0, '2025-08-27 13:18:59', '2025-08-27 13:18:59'),
(67, 8, 38, 1, 0, '2025-08-27 13:18:59', '2025-08-27 13:18:59'),
(68, 9, 37, 1, 0, '2025-08-27 14:41:29', '2025-08-27 14:41:29'),
(158, 10, 41, 1, 0, '2025-10-20 11:03:37', '2025-10-20 11:03:37'),
(159, 10, 27, 1, 0, '2025-10-20 11:03:37', '2025-10-20 11:03:37'),
(160, 10, 14, 1, 0, '2025-10-20 11:03:37', '2025-10-20 11:03:37'),
(161, 10, 25, 1, 0, '2025-10-20 11:03:37', '2025-10-20 11:03:37');

-- --------------------------------------------------------

--
-- Table structure for table `coupon_code_usage_logs`
--

CREATE TABLE `coupon_code_usage_logs` (
  `id` bigint NOT NULL,
  `coupon_code_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `order_id` int NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `items_in_order` int NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupon_code_usage_logs`
--

INSERT INTO `coupon_code_usage_logs` (`id`, `coupon_code_id`, `customer_id`, `order_id`, `discount_amount`, `items_in_order`, `used_at`) VALUES
(1, 16, 19, 19, 139.80, 1, '2025-10-16 06:53:45');

-- --------------------------------------------------------

--
-- Table structure for table `customer_addresses`
--

CREATE TABLE `customer_addresses` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contact` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `sub_district` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `district` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `province` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `is_primary` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_addresses`
--

INSERT INTO `customer_addresses` (`id`, `customer_id`, `name`, `contact`, `address`, `sub_district`, `district`, `province`, `postal_code`, `is_primary`) VALUES
(3, 19, 'นางสาวมลฑล ณ วิศัย', '082-888-9688', '21 ม.5 ถ.เพชรเกษม', 'นาท่ามเหนือ', 'เมือง', 'ตรัง', '92190', 0),
(12, 19, 'นายฉันท์ชยา ภิญโญssss', '082-223-9898', '21/8', 'นาท่ามเหนือ', 'เมือง', 'ตรัง', '92190', 1),
(15, 19, 'นางอัญมณี คงสี', '089-456-654', '77/88 ถ.ทางตรง', 'อย่างซิ่ง', 'แซงหมด', 'ไม่มีเลี้ยว', '88888', 0),
(17, 48, 'ฉันท์ชยา ภิญโญ', '0828398430', '21 ม.5 ถ.เพชรเกษม', 'นาท่ามเหนือ', 'เมือง', 'ตรัง', '92190', 1),
(18, 75, 'นายฉันท์ชยา ภิญโญ', '0828398430', '21 ม.5 ถ.เพชรเกษม', 'นาท่ามเหนือ', 'เมือง', 'ตรัง', '92190', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customer_list`
--

CREATE TABLE `customer_list` (
  `id` int NOT NULL,
  `firstname` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `middlename` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `lastname` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `gender` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contact` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `avatar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `last_login` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_list`
--

INSERT INTO `customer_list` (`id`, `firstname`, `middlename`, `lastname`, `gender`, `contact`, `email`, `password`, `avatar`, `last_login`, `date_created`, `date_updated`) VALUES
(2, 'jame', 'a', 'huansin', 'Male', '-', 'jame@gmail.com', 'fcea920f7412b5da7be0cf42b8c93759', 'uploads/customers/2.png?v=1704643304', NULL, '2024-01-07 11:25:47', '2024-01-07 23:01:44'),
(4, 'jame', 'a', 'asd', 'Male', '12', 'asdsa@rr.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, '2024-01-07 11:33:40', '2024-01-07 11:33:40'),
(5, 'Beam', 'Beam', 'Beam', 'Female', '0110055', 'Beam@Beam.com', 'e10adc3949ba59abbe56e057f20f883e', 'uploads/customers/5.png?v=1707833626', NULL, '2024-02-13 21:13:46', '2024-02-13 21:13:46'),
(6, 'แมทิวlnwza', '', 'นาโนวา007', 'Male', '099-999-9999', 'matew999@gmail.com', 'fa246d0262c3925617b0c72bb20eeb1d', 'uploads/customers/6.png?v=1747647314', NULL, '2025-05-19 16:19:24', '2025-05-19 16:35:14'),
(8, 'user', '', '1', 'Male', '077-011-1122', 'user1@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/8.png?v=1747741055', NULL, '2025-05-20 18:37:35', '2025-06-14 10:58:36'),
(9, 'นัทตี้', '', 'แต๋วแตก', 'ชาย', '055-555-5656', 'nutty@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/9.png?v=1748624417', NULL, '2025-05-31 00:00:17', '2025-05-31 00:00:17'),
(10, 'Staff', '', '2', 'Male', '011-777-9999', 'address@gmail.com', '$2y$10$jTeJkwk8jpYmWNRXIQJ5ouQkTrUV9NPVcmKleIo5eRcyl.DlSYSjG', 'uploads/customers/10.png?v=1749005687', NULL, '2025-06-04 09:54:47', '2025-10-06 11:03:54'),
(11, 'Address', '', '2', 'Male', '011-999-7777', 'address2@gmail.com', '$2y$10$xfRemmE5bt35LI0Frp3DkeG5upK6o/PhxRqYt/5BUbbBfPeMhXyfC', 'uploads/customers/11.png?v=1757650691', NULL, '2025-06-04 10:02:51', '2025-09-17 13:36:22'),
(12, 'หมาป่าเดียวดาย', '', 'สมาธิ', 'Male', '777-888-9999', 'user2@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/12.png?v=1749007549', NULL, '2025-06-04 10:25:49', '2025-06-24 14:39:48'),
(14, 'นายหมาในดำใดดง', 'ณ', 'ป่ามะขาม', 'Male', '011-557-8686', 'wolf@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/14.png?v=1749117170', NULL, '2025-06-05 16:52:50', '2025-06-05 16:52:50'),
(15, 'นายเอกวัน', '', 'สองไม่รองใคร', 'Male', '022-555-9898', 'user3@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', NULL, NULL, '2025-06-17 12:58:09', '2025-06-17 13:00:37'),
(16, 'เอกไม', '', 'ไมค์ทองคำ', 'Male', '023-858-9988', 'user4@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', NULL, NULL, '2025-06-17 13:17:23', '2025-06-17 13:17:23'),
(17, 'นางสาวสามารถ', '', 'ทำได้ดี', 'Male', '068-888-9999', 'user5@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/17.png?v=1750141789', NULL, '2025-06-17 13:29:27', '2025-06-17 13:29:49'),
(18, 'นายฉันท์ชยา', '', 'ภิญโญ', 'Male', '0828398430', 'faritre5566@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/18.png?v=1751264120', NULL, '2025-06-26 15:42:39', '2025-06-30 13:15:20'),
(19, 'นางอัญมณี', 'ณ', 'คงสี', 'Female', '088-115-5458', 'faritre1@gmail.com', '$2y$10$R3opIXZef/UbI19/0VFVLOTkCn8wwPqteLnFNdEIGQFEYdQwEzG3K', 'uploads/customers/19.png?v=1758781935', '2025-10-22 10:23:36', '2025-06-26 15:48:41', '2025-10-22 10:23:36'),
(20, 'แมวหลาม', '', 'ซาบะ', 'Male', '011-555-6687', 'faritre4@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/20.png?v=1752809365', NULL, '2025-07-18 10:29:25', '2025-07-18 10:29:25'),
(25, 'แชมเบอร์', '', 'สามช่า', 'Male', '011-999-7777', '1@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/25.png?v=1757393791', NULL, '2025-09-09 11:56:31', '2025-09-09 11:56:31'),
(29, 'ปาล์ม', 'อมัจจ์', 'เดชสงคราม', 'Male', '0980624633', 'amat123450zx@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/29.png?v=1757673364', NULL, '2025-09-12 17:36:04', '2025-09-12 17:40:36'),
(30, 'Tipwadee', '', 'Pattana', 'Male', '0952713291', 'tipwadee5818@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, '2025-09-12 18:14:40', '2025-09-12 18:14:40'),
(44, 'นางอัญมณีasdasd', '', 'คงสีaaa', 'Male', '0881155458', '333@gmail.com', '$2y$10$B.PUf0DE4PNa24ZHh46.QewXS9gkS9mmB3BmwARJYFR1RV4dsHme.', NULL, NULL, '2025-09-16 15:29:33', '2025-09-16 15:29:33'),
(45, '1', '1', '1', 'Male', '01231456789', 'test1@gmail.com', '$2y$10$Wck6K95uN7w5SXA4qxnAz.QRI8rZQc7PjO28aODBB3lR9NpTbJKWW', 'uploads/customers/45.png?v=1758014169', NULL, '2025-09-16 16:16:09', '2025-09-16 16:16:09'),
(46, '1', '1', '1', 'Male', '111111111111111', 'test2@gmail.com', '$2y$10$.o/t6M68vXIp30YGuzfafufu07sgSYVEJku/0ADtTVWHn.AqxJLgS', 'uploads/customers/46.png?v=1758014264', '2025-09-16 16:23:48', '2025-09-16 16:17:44', '2025-09-16 16:23:48'),
(48, 'ฉันท์ชยา', '', 'ภิญโญ', 'Male', '0828398430', 'test111111@gmail.com', '$2y$10$k8YvnBcdP/FdaUZ4KYuoYe9PMkWu.cG0hHJXIETOeXrUW4HOE8MMq', 'uploads/customers/48.png?v=1759286668', '2025-10-01 10:49:50', '2025-10-01 09:44:28', '2025-10-15 15:10:25'),
(73, 'มีนา', 'นะ', '88', 'Female', '0858987878', 'test12345@gmail.com', '$2y$10$nh1UWZTw5puqJL7rpxVPu.lqGTzFJ9dkzWCDjbeLt0q8LY3Msu0Gi', 'uploads/customers/73.png?v=1759718686', '2025-10-06 09:46:27', '2025-10-06 09:44:46', '2025-10-06 09:46:27'),
(74, 'Blue', 'Sky', 'Hair', 'Female', '0856547541', 'test123456@gmail.com', '$2y$10$gmxuIh9F5LFvdPGMxsMn0Okh8H2Ix5Jh0LqIRoAT6IDy/e7zBFj2e', 'uploads/customers/74.png?v=1759725070', '2025-10-06 11:54:23', '2025-10-06 11:31:10', '2025-10-06 11:54:23'),
(75, 'ฉันท์ชยา', '', 'ภิญโญ', 'Male', '0828398430', 'chanchayapinyo@gmail.com', '$2y$10$U2jQSatqyKuViKG1pbbrLOEl38jpszOawjtOFfpwFkD5zwox5MRSm', 'uploads/customers/75.png?v=1760515704', '2025-10-16 11:52:51', '2025-10-15 15:07:40', '2025-10-16 11:52:51');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `promotion_id` int DEFAULT NULL,
  `coupon_code_id` int DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `price` float(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_id`, `product_id`, `promotion_id`, `coupon_code_id`, `quantity`, `price`) VALUES
(1, 41, 16, NULL, 3, 181.00),
(2, 24, NULL, NULL, 4, 553.00),
(3, 60, 20, NULL, 5, 699.00),
(4, 33, NULL, NULL, 1, 242.00),
(5, 36, 16, NULL, 1, 26960.00),
(6, 22, NULL, NULL, 1, 403.00),
(7, 14, NULL, NULL, 1, 11298.00),
(8, 61, NULL, NULL, 1, 144.00),
(9, 23, NULL, NULL, 1, 279.00),
(10, 23, NULL, NULL, 1, 279.00),
(11, 33, NULL, NULL, 1, 242.00),
(12, 14, NULL, NULL, 1, 11298.00),
(13, 25, NULL, NULL, 1, 594.00),
(14, 40, NULL, NULL, 20, 59.00),
(15, 33, 10, 16, 2, 242.00),
(16, 61, NULL, NULL, 4, 144.00),
(17, 61, 20, 16, 38, 144.00),
(18, 14, NULL, NULL, 2, 11298.00),
(18, 33, NULL, NULL, 3, 242.00),
(18, 36, NULL, NULL, 1, 26960.00),
(19, 60, 20, 16, 1, 699.00),
(20, 40, NULL, NULL, 10, 59.00),
(21, 64, NULL, NULL, 13, 107.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_list`
--

CREATE TABLE `order_list` (
  `id` int NOT NULL,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `customer_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contact` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `delivery_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `total_amount` float(12,2) NOT NULL DEFAULT '0.00',
  `promotion_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `coupon_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tracking_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipping_methods_id` int DEFAULT NULL,
  `shipping_prices_id` int DEFAULT NULL,
  `promotion_id` int DEFAULT NULL,
  `coupon_code_id` int DEFAULT NULL,
  `payment_status` tinyint(1) NOT NULL DEFAULT '0',
  `delivery_status` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `is_seen` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_list`
--

INSERT INTO `order_list` (`id`, `code`, `customer_id`, `name`, `contact`, `delivery_address`, `total_amount`, `promotion_discount`, `coupon_discount`, `tracking_id`, `shipping_methods_id`, `shipping_prices_id`, `promotion_id`, `coupon_code_id`, `payment_status`, `delivery_status`, `status`, `is_seen`, `date_created`, `date_updated`) VALUES
(1, '2025091500001', 19, NULL, NULL, '44 หมู่ 8, ต.นาท่ามเหนือ, อ.เมือง, จ.ตรัง, 92190', 400.80, 217.20, 0.00, NULL, 3, 4, 16, NULL, 6, 9, 0, 1, '2025-09-15 16:00:36', '2025-09-16 09:16:39'),
(2, '2025092600001', 19, NULL, NULL, 'นางสาวมลฑล วิศัย, 082-888-9688, 21 ม.5 ถ.เพชรเกษม, ต.นาท่ามเหนือ, อ.เมือง, จ.ตรัง, 92190', 2252.00, 0.00, 0.00, NULL, 3, 1, NULL, NULL, 0, 0, 0, 1, '2025-09-26 09:16:01', '2025-09-26 09:16:13'),
(3, '2025092600002', 19, NULL, NULL, '21 ม.5 ถ.เพชรเกษม นาท่ามเหนือ เมือง ตรัง 92190', 3535.00, 0.00, 0.00, NULL, 3, 1, 20, NULL, 0, 0, 0, 1, '2025-09-26 09:24:29', '2025-09-26 09:24:35'),
(4, '2025092600003', 19, NULL, NULL, '21 ม.5 ถ.เพชรเกษม นาท่ามเหนือ เมือง ตรัง 92190', 282.00, 0.00, 0.00, NULL, 3, 1, NULL, NULL, 0, 0, 0, 1, '2025-09-26 09:27:09', '2025-09-26 09:34:08'),
(5, '2025092600004', 19, NULL, NULL, '21 ม.5 ถ.เพชรเกษม นาท่ามเหนือ เมือง ตรัง 92190', 16216.00, 10784.00, 0.00, NULL, 3, 1, 16, NULL, 4, 6, 0, 1, '2025-09-26 09:29:59', '2025-10-01 09:16:22'),
(6, '2025092600005', 19, NULL, NULL, '21 ม.5 ถ.เพชรเกษม นาท่ามเหนือ เมือง ตรัง 92190', 443.00, 0.00, 0.00, NULL, 3, 1, NULL, NULL, 4, 6, 0, 1, '2025-09-26 09:33:00', '2025-10-01 09:04:31'),
(7, '2025100100001', 19, NULL, NULL, '21/8 นาท่ามเหนือ เมือง ตรัง 92190', 11348.00, 0.00, 0.00, NULL, 3, 2, NULL, NULL, 4, 6, 0, 1, '2025-10-01 09:00:46', '2025-10-06 08:57:03'),
(8, '2025100600001', 19, NULL, NULL, '21/8 นาท่ามเหนือ เมือง ตรัง 92190', 184.00, 0.00, 0.00, NULL, 3, 1, NULL, NULL, 0, 0, 0, 1, '2025-10-06 08:57:44', '2025-10-06 08:57:59'),
(9, '2025100600002', 19, NULL, NULL, 'นายฉันท์ชยา ภิญโญssss 21/8 นาท่ามเหนือ เมือง ตรัง 92190', 319.00, 0.00, 0.00, NULL, 3, 1, NULL, NULL, 0, 0, 0, 1, '2025-10-06 09:02:51', '2025-10-06 09:30:57'),
(10, '2025100600003', 19, 'นายฉันท์ชยา ภิญโญssss', NULL, '21/8 นาท่ามเหนือ เมือง ตรัง 92190', 319.00, 0.00, 0.00, NULL, 3, 1, NULL, NULL, 0, 0, 0, 1, '2025-10-06 09:06:31', '2025-10-06 09:30:57'),
(11, '2025100600004', 19, 'นายฉันท์ชยา ภิญโญssss', '082-223-9898', '21/8 นาท่ามเหนือ เมือง ตรัง 92190', 282.00, 0.00, 0.00, NULL, 3, 1, NULL, NULL, 0, 0, 0, 1, '2025-10-06 09:18:29', '2025-10-06 09:30:57'),
(12, '2025100600005', 19, 'นางอัญมณี คงสี', '089-456-654', '77/88 ถ.ทางตรง อย่างซิ่ง แซงหมด ไม่มีเลี้ยว 88888', 11348.00, 0.00, 0.00, NULL, 3, 2, NULL, NULL, 0, 0, 0, 1, '2025-10-06 09:29:52', '2025-10-06 09:30:57'),
(13, '2025100600006', 19, 'นายฉันท์ชยา ภิญโญssss', '082-223-9898', '21/8 นาท่ามเหนือ เมือง ตรัง 92190', 634.00, 0.00, 0.00, NULL, 3, 1, NULL, NULL, 2, 3, 0, 1, '2025-10-06 09:33:07', '2025-10-10 09:34:58'),
(14, '2025100600007', 19, 'นางสาวมลฑล ณ วิศัย', '082-888-9688', '21 ม.5 ถ.เพชรเกษม นาท่ามเหนือ เมือง ตรัง 92190', 1350.00, 0.00, 0.00, NULL, 3, 12, NULL, NULL, 6, 10, 0, 1, '2025-10-06 09:57:20', '2025-10-10 09:34:58'),
(15, '2025100600008', 19, 'นายฉันท์ชยา ภิญโญssss', '082-223-9898', '21/8 นาท่ามเหนือ เมือง ตรัง 92190', 342.00, 145.20, 96.80, NULL, 40, 126, 10, 16, 2, 4, 0, 1, '2025-10-06 14:59:05', '2025-10-09 16:31:04'),
(16, '2025100900001', 19, 'นายฉันท์ชยา ภิญโญssss', '082-223-9898', '21/8 นาท่ามเหนือ เมือง ตรัง 92190', 626.00, 0.00, 0.00, NULL, 3, 2, NULL, NULL, 2, 4, 0, 1, '2025-10-09 16:26:46', '2025-10-10 09:34:58'),
(17, '2025101000001', 19, 'นายฉันท์ชยา ภิญโญssss', '082-223-9898', '21/8 นาท่ามเหนือ เมือง ตรัง 92190', 4377.60, 0.00, 1094.40, NULL, 7, 82, 20, 16, 0, 0, 0, 1, '2025-10-10 15:04:02', '2025-10-14 14:51:48'),
(18, '2025101500001', 75, 'นายฉันท์ชยา ภิญโญ', '0828398430', '21 ม.5 ถ.เพชรเกษม นาท่ามเหนือ เมือง ตรัง 92190', 50372.00, 0.00, 0.00, NULL, 7, 68, NULL, NULL, 2, 3, 0, 0, '2025-10-15 15:16:39', '2025-10-15 15:26:07'),
(19, '2025101600001', 19, 'นายฉันท์ชยา ภิญโญssss', '082-223-9898', '21/8 นาท่ามเหนือ เมือง ตรัง 92190', 559.20, 0.00, 139.80, 'TH202510201001', 3, 1, 20, 16, 0, 0, 0, 1, '2025-10-16 13:53:45', '2025-10-20 15:44:45'),
(20, '2025102100001', 19, 'นายฉันท์ชยา ภิญโญssss', '082-223-9898', '21/8 นาท่ามเหนือ เมือง ตรัง 92190', 690.00, 0.00, 0.00, NULL, 3, 6, NULL, NULL, 0, 0, 0, 1, '2025-10-21 13:53:09', '2025-10-21 13:55:17'),
(21, '2025102100002', 19, 'นายฉันท์ชยา ภิญโญssss', '082-223-9898', '21/8 นาท่ามเหนือ เมือง ตรัง 92190', 1431.00, 0.00, 0.00, NULL, 3, 1, NULL, NULL, 0, 0, 0, 1, '2025-10-21 13:55:20', '2025-10-21 13:55:32');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `customer_id`, `token`, `expires_at`) VALUES
(1, 19, '705084ef355b714f11b1990e063a10532311d366dc003f995be45231a807c198', '2025-09-17 17:57:06'),
(2, 19, '35822721fffadf9155fccd0cb22aa3dcbf4a989aa78947161cc6ef82f21c6832', '2025-09-19 10:44:04'),
(3, 19, '59c25758fede9a59332aea6548df31016279dab07720ffef0fffa00a37ff4540', '2025-09-19 10:56:45'),
(6, 19, 'dea88dfd8001ece1c463b874e9fe6a8a3befe91393e3c51c7333daf392fc8c7d', '2025-09-19 11:48:06'),
(7, 19, 'b0f34364f39de9f2881b379ac6a4f55d1fbf38c40cd87616aa1e46061de8d0d8', '2025-09-19 11:48:33'),
(8, 19, '74316a3295a0e11614b53fcb7a7f330cf22bad75c0f2bf9d2e200009b002d97c', '2025-09-19 11:49:12'),
(12, 19, 'a2a9d3d162ebf6da086e36413c2d962ec0fdad46dd0abe7aee9d24b0fa7d4279', '2025-09-30 12:54:06'),
(14, 75, '2be7a95ad3cc617d01f7a9490d385e8e3db8cb0be2ef8babc5b2f21b51c4c768', '2025-10-15 16:30:50'),
(15, 75, '2ecad0698253ef043e47f078d87ace43958703177451aed589d986282f3fe995', '2025-10-15 16:34:15'),
(16, 75, '739bf93e6d9d2344183b3e78c9eee6ddb2029ce0dcffa89a6f20013106e30eff', '2025-10-15 16:34:19'),
(17, 19, '81a66d916fd1ddb4a24ed8340115c3f75d99eda485d38bcb7da61e00a2c308fb', '2025-10-15 16:34:45');

-- --------------------------------------------------------

--
-- Table structure for table `product_image_path`
--

CREATE TABLE `product_image_path` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_create` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_image_path`
--

INSERT INTO `product_image_path` (`id`, `product_id`, `image_path`, `date_create`, `date_update`) VALUES
(1, 29, 'uploads/products/gallery_68a7f1eaa737b4.31929796.png', '2025-08-22 04:28:26', '2025-08-22 04:28:26'),
(2, 29, 'uploads/products/gallery_68a7f1eaa7aed9.95225766.jpg', '2025-08-22 04:28:26', '2025-08-22 04:28:26'),
(3, 29, 'uploads/products/gallery_68a7f446884ed2.75739990.png', '2025-08-22 04:38:30', '2025-08-22 04:38:30'),
(4, 29, 'uploads/products/gallery_68a7f44688b117.14022107.png', '2025-08-22 04:38:30', '2025-08-22 04:38:30'),
(5, 29, 'uploads/products/gallery_68a7f4580a2707.22311849.png', '2025-08-22 04:38:48', '2025-08-22 04:38:48'),
(7, 29, 'uploads/products/gallery_68a7f46be23257.58459504.png', '2025-08-22 04:39:07', '2025-08-22 04:39:07'),
(8, 29, 'uploads/products/gallery_68a7f46be29458.30263319.png', '2025-08-22 04:39:07', '2025-08-22 04:39:07'),
(9, 29, 'uploads/products/gallery_68a7f46be2f288.35057746.png', '2025-08-22 04:39:07', '2025-08-22 04:39:07'),
(12, 36, 'uploads/products/gallery_68a80cf1726ba1.14858542.png', '2025-08-22 06:23:45', '2025-08-22 06:23:45'),
(14, 36, 'uploads/products/gallery_68a80d1e52f0e3.39353617.png', '2025-08-22 06:24:30', '2025-08-22 06:24:30'),
(16, 36, 'uploads/products/gallery_68a80d38d59ba0.07250275.png', '2025-08-22 06:24:56', '2025-08-22 06:24:56'),
(17, 36, 'uploads/products/gallery_68a80d452d2a31.74009945.png', '2025-08-22 06:25:09', '2025-08-22 06:25:09'),
(18, 37, 'uploads/products/gallery_68a81175a63fb6.14070080.png', '2025-08-22 06:43:01', '2025-08-22 06:43:01'),
(20, 37, 'uploads/products/gallery_68a8123ea04620.40207586.png', '2025-08-22 06:46:22', '2025-08-22 06:46:22'),
(21, 37, 'uploads/products/gallery_68a8125c931538.88011388.png', '2025-08-22 06:46:52', '2025-08-22 06:46:52'),
(22, 37, 'uploads/products/gallery_68a81267d0c5e5.31663235.png', '2025-08-22 06:47:03', '2025-08-22 06:47:03'),
(23, 38, 'uploads/products/gallery_68a81383e65785.08091827.png', '2025-08-22 06:51:47', '2025-08-22 06:51:47'),
(24, 38, 'uploads/products/gallery_68a8139b7fc591.20792535.png', '2025-08-22 06:52:11', '2025-08-22 06:52:11'),
(25, 38, 'uploads/products/gallery_68a813b0c94a81.51459873.png', '2025-08-22 06:52:32', '2025-08-22 06:52:32'),
(27, 39, 'uploads/products/gallery_68a8151787e532.15015990.png', '2025-08-22 06:58:31', '2025-08-22 06:58:31'),
(28, 39, 'uploads/products/gallery_68a81535dec803.05179435.png', '2025-08-22 06:59:01', '2025-08-22 06:59:01'),
(29, 39, 'uploads/products/gallery_68a81545f22349.31279426.png', '2025-08-22 06:59:17', '2025-08-22 06:59:17'),
(30, 39, 'uploads/products/gallery_68a8155dd64bf7.18021382.png', '2025-08-22 06:59:41', '2025-08-22 06:59:41'),
(31, 40, 'uploads/products/gallery_68a8170e4749f2.92200924.png', '2025-08-22 07:06:54', '2025-08-22 07:06:54'),
(32, 40, 'uploads/products/gallery_68a81739be3db9.17844491.png', '2025-08-22 07:07:37', '2025-08-22 07:07:37'),
(33, 40, 'uploads/products/gallery_68a8177b22d432.76719975.png', '2025-08-22 07:08:43', '2025-08-22 07:08:43'),
(36, 35, 'uploads/products/gallery_68ba9442b7e0c5.78597932.png', '2025-09-05 07:41:54', '2025-09-05 07:41:54'),
(37, 44, 'uploads/products/gallery_68ba9555335d83.18250013.png', '2025-09-05 07:46:29', '2025-09-05 07:46:29'),
(38, 30, 'uploads/products/gallery_68ba9e9f98b809.40516801.png', '2025-09-05 08:26:07', '2025-09-05 08:26:07'),
(39, 30, 'uploads/products/gallery_68ba9e9f9a4627.72937002.jpg', '2025-09-05 08:26:07', '2025-09-05 08:26:07'),
(40, 30, 'uploads/products/gallery_68ba9eace8e9f8.78406570.png', '2025-09-05 08:26:20', '2025-09-05 08:26:20'),
(41, 30, 'uploads/products/gallery_68ba9eacea9b65.66640147.png', '2025-09-05 08:26:20', '2025-09-05 08:26:20'),
(42, 30, 'uploads/products/gallery_68ba9ebdb821d6.23601480.png', '2025-09-05 08:26:37', '2025-09-05 08:26:37'),
(43, 45, 'uploads/products/gallery_68baa9c2bb9988.42626453.png', '2025-09-05 09:13:38', '2025-09-05 09:13:38'),
(44, 45, 'uploads/products/gallery_68baa9c2bc5315.64363886.jpg', '2025-09-05 09:13:38', '2025-09-05 09:13:38'),
(45, 48, 'uploads/products/gallery_68c24990a6b355.08068634.png', '2025-09-11 04:01:20', '2025-09-11 04:01:20'),
(46, 48, 'uploads/products/gallery_68c24990a75b54.85869622.png', '2025-09-11 04:01:20', '2025-09-11 04:01:20'),
(47, 48, 'uploads/products/gallery_68c24990a7d5d4.66741853.png', '2025-09-11 04:01:20', '2025-09-11 04:01:20'),
(48, 49, 'uploads/products/gallery_68c24b1ba51f65.54552795.png', '2025-09-11 04:07:55', '2025-09-11 04:07:55'),
(49, 49, 'uploads/products/gallery_68c24b1ba5a623.25125467.png', '2025-09-11 04:07:55', '2025-09-11 04:07:55'),
(50, 49, 'uploads/products/gallery_68c24b1ba627f9.89543114.png', '2025-09-11 04:07:55', '2025-09-11 04:07:55'),
(51, 50, 'uploads/products/gallery_68c24b81021035.81403531.png', '2025-09-11 04:09:37', '2025-09-11 04:09:37'),
(52, 50, 'uploads/products/gallery_68c24b81028968.20894907.png', '2025-09-11 04:09:37', '2025-09-11 04:09:37'),
(53, 50, 'uploads/products/gallery_68c24b8103ac48.64564178.png', '2025-09-11 04:09:37', '2025-09-11 04:09:37'),
(54, 60, 'uploads/products/gallery_68c2bdaae94095.12357150.png', '2025-09-11 12:16:42', '2025-09-11 12:16:42'),
(55, 60, 'uploads/products/gallery_68c2bdb7622119.06859683.png', '2025-09-11 12:16:55', '2025-09-11 12:16:55'),
(56, 60, 'uploads/products/gallery_68c2bdb762c376.27679823.png', '2025-09-11 12:16:55', '2025-09-11 12:16:55'),
(57, 60, 'uploads/products/gallery_68c2bdb7633e93.50788497.png', '2025-09-11 12:16:55', '2025-09-11 12:16:55'),
(58, 60, 'uploads/products/gallery_68c2bdb763ba02.17844028.png', '2025-09-11 12:16:55', '2025-09-11 12:16:55'),
(59, 60, 'uploads/products/gallery_68c2bdb76448e3.30522974.png', '2025-09-11 12:16:55', '2025-09-11 12:16:55'),
(60, 60, 'uploads/products/gallery_68c2bdb764c406.74289575.png', '2025-09-11 12:16:55', '2025-09-11 12:16:55'),
(61, 60, 'uploads/products/gallery_68c2bdb7654742.35476007.png', '2025-09-11 12:16:55', '2025-09-11 12:16:55'),
(63, 61, 'uploads/products/gallery_68c2bf78a06cb9.49661144.png', '2025-09-11 12:24:24', '2025-09-11 12:24:24'),
(64, 61, 'uploads/products/gallery_68c2bfb0b821e0.52243551.png', '2025-09-11 12:25:20', '2025-09-11 12:25:20'),
(65, 61, 'uploads/products/gallery_68c2bfb0b8a7a1.60386044.png', '2025-09-11 12:25:20', '2025-09-11 12:25:20'),
(66, 61, 'uploads/products/gallery_68c2bfb0b93438.01100024.png', '2025-09-11 12:25:20', '2025-09-11 12:25:20'),
(67, 61, 'uploads/products/gallery_68c2bfb0b9bef3.03105405.png', '2025-09-11 12:25:20', '2025-09-11 12:25:20'),
(68, 61, 'uploads/products/gallery_68c2bfb0bac6f8.89679937.png', '2025-09-11 12:25:20', '2025-09-11 12:25:20'),
(69, 61, 'uploads/products/gallery_68c2bfb0bb5ac4.10738830.png', '2025-09-11 12:25:20', '2025-09-11 12:25:20'),
(70, 61, 'uploads/products/gallery_68c2bfb0bbd365.91716162.png', '2025-09-11 12:25:20', '2025-09-11 12:25:20'),
(71, 62, 'uploads/products/gallery_68c2c07a5accd0.58854962.png', '2025-09-11 12:28:42', '2025-09-11 12:28:42'),
(72, 62, 'uploads/products/gallery_68c2c097794404.29817753.png', '2025-09-11 12:29:11', '2025-09-11 12:29:11'),
(73, 62, 'uploads/products/gallery_68c2c09779e598.82675611.png', '2025-09-11 12:29:11', '2025-09-11 12:29:11'),
(74, 63, 'uploads/products/gallery_68c2c11aa57614.75691405.png', '2025-09-11 12:31:22', '2025-09-11 12:31:22'),
(75, 63, 'uploads/products/gallery_68c2c11aa60fb8.29816022.png', '2025-09-11 12:31:22', '2025-09-11 12:31:22');

-- --------------------------------------------------------

--
-- Table structure for table `product_links`
--

CREATE TABLE `product_links` (
  `product_id` int NOT NULL,
  `shopee_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `lazada_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tiktok_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_links`
--

INSERT INTO `product_links` (`product_id`, `shopee_url`, `lazada_url`, `tiktok_url`, `date_created`, `date_updated`) VALUES
(14, '', '', '', '2025-06-12 10:51:24', '2025-09-02 11:12:25'),
(20, 'https://shopee.co.th/Madcatz-MAD-60-68-HE-%E0%B8%84%E0%B8%B5%E0%B8%A2%E0%B9%8C%E0%B8%9A%E0%B8%AD%E0%B8%A3%E0%B9%8C%E0%B8%94%E0%B8%AA%E0%B8%A7%E0%B8%B4%E0%B8%95%E0%B8%8A%E0%B9%8C%E0%B9%81%E0%B8%A1%E0%B9%88%E0%B9%80%E0%B8%AB%E0%B8%A5%E0%B9%87%E0%B8%81%E0%B8%AA%E0%B9%8D%E0%B8%B2%E0%B8%AB%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%80%E0%B8%A5%E0%B9%88%E0%B8%99%E0%B9%80%E0%B8%81%E0%B8%A1-61-68-%E0%B8%84%E0%B8%B5%E0%B8%A2%E0%B9%8C-Latency-%E0%B8%95%E0%B9%88%E0%B9%8D%E0%B8%B2-Full-Key-Hot-Swap-i.145792167.26366613520', '', '', '2025-06-17 13:40:27', '2025-09-11 16:21:44'),
(21, 'https://shopee.co.th/iHAVECPU-HEADSET-(%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87)-iHAVECPU-MUSES-WITH-MIC-(BLACK-RED)-i.15422060.25650552218', '', '', '2025-06-17 13:43:16', '2025-09-02 16:49:14'),
(22, 'https://shopee.co.th/-%E0%B8%9B%E0%B8%A3%E0%B8%B0%E0%B8%81%E0%B8%B1%E0%B8%99%E0%B8%A8%E0%B8%B9%E0%B8%99%E0%B8%A2%E0%B9%8C%E0%B9%84%E0%B8%97%E0%B8%A2-FiiO-JD10-%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87-IEMs-%E0%B9%84%E0%B8%94%E0%B8%A3%E0%B9%80%E0%B8%A7%E0%B8%AD%E0%B8%A3%E0%B9%8C-Dynamic-%E0%B8%AA%E0%B8%B8%E0%B8%94%E0%B8%84%E0%B8%B8%E0%B9%89%E0%B8%A1-%E0%B8%AA%E0%B8%B3%E0%B8%AB%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%80%E0%B8%A5%E0%B9%88%E0%B8%99%E0%B9%80%E0%B8%81%E0%B8%A1-%E0%B8%A3%E0%B8%AD%E0%B8%87%E0%B8%A3%E0%B8%B1%E0%B8%9A-Hi-Res-i.1819391.29563989283', '', '', '2025-06-17 13:48:50', '2025-09-02 16:48:49'),
(23, 'https://shopee.co.th/(%E0%B9%81%E0%B8%9E%E0%B9%87%E0%B8%81-12)-Muek-Groob-%E0%B8%AB%E0%B8%A1%E0%B8%B6%E0%B8%81%E0%B8%81%E0%B8%A3%E0%B8%B8%E0%B8%9A-%E0%B9%80%E0%B8%AA%E0%B9%89%E0%B8%99%E0%B8%9A%E0%B8%B8%E0%B8%81%E0%B8%9B%E0%B8%A3%E0%B8%B8%E0%B8%87%E0%B8%A3%E0%B8%AA%E0%B8%AB%E0%B8%A1%E0%B9%88%E0%B8%B2%E0%B8%A5%E0%B9%88%E0%B8%B2-%E0%B8%AA%E0%B8%B9%E0%B8%95%E0%B8%A3%E0%B8%94%E0%B8%B1%E0%B9%89%E0%B8%87%E0%B9%80%E0%B8%94%E0%B8%B4%E0%B8%A1-%E0%B8%AB%E0%B8%A1%E0%B8%B6%E0%B8%81%E0%B8%81%E0%B8%A3%E0%B8%B8%E0%B8%9A%E0%B8%8B%E0%B8%B1%E0%B8%99%E0%B8%8B%E0%B8%B8-sunsu-i.214411437.25928257606', '', '', '2025-06-17 13:52:22', '2025-09-02 16:47:54'),
(24, 'https://shopee.co.th/Llamito-%E0%B8%9C%E0%B8%87%E0%B8%A1%E0%B8%B1%E0%B8%97%E0%B8%89%E0%B8%B0-%E0%B8%AD%E0%B8%AD%E0%B8%A3%E0%B9%8C%E0%B9%81%E0%B8%81%E0%B8%99%E0%B8%B4%E0%B8%84-(Matcha-Powder)-%E0%B8%82%E0%B8%99%E0%B8%B2%E0%B8%94-250g-i.18117499.10946816382', '', '', '2025-06-17 14:22:06', '2025-09-02 16:49:55'),
(25, 'https://shopee.co.th/%F0%9F%94%A5%E0%B8%AA%E0%B8%B4%E0%B8%99%E0%B8%84%E0%B9%89%E0%B8%B2%E0%B8%82%E0%B8%B2%E0%B8%A2%E0%B8%94%E0%B8%B5%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%AA%E0%B8%B8%E0%B8%94%F0%9F%94%A5-Monster-XKT02-BT-5.3-%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87-%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87%E0%B8%9A%E0%B8%A5%E0%B8%B9%E0%B8%97%E0%B8%B9%E0%B8%98-%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87%E0%B9%84%E0%B8%A3%E0%B9%89%E0%B8%AA%E0%B8%B2%E0%B8%A2-%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87-monster-HIFI-i.886198470.20357703411?sp_atk=c97ab49a-a5e3-4e76-8dc9-7da9c262f62f&xptdk=c97ab49a-a5e3-4e76-8dc9-7da9c262f62f', '', '', '2025-06-17 14:25:31', '2025-09-02 16:47:23'),
(26, 'https://shopee.co.th/Jeep-JP-EW011-%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87%E0%B8%9A%E0%B8%A5%E0%B8%B9%E0%B8%97%E0%B8%B9%E0%B8%98%E0%B9%84%E0%B8%A3%E0%B9%89%E0%B8%AA%E0%B8%B2%E0%B8%A2-HiFi-HD-Call-%E0%B8%A5%E0%B8%94%E0%B9%80%E0%B8%AA%E0%B8%B5%E0%B8%A2%E0%B8%87%E0%B8%A3%E0%B8%9A%E0%B8%81%E0%B8%A7%E0%B8%99-%E0%B8%88%E0%B8%B1%E0%B8%9A%E0%B8%84%E0%B8%B9%E0%B9%88%E0%B9%80%E0%B8%A3%E0%B9%87%E0%B8%A7%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87%E0%B8%9A%E0%B8%A5%E0%B8%B9%E0%B8%97%E0%B8%B9%E0%B8%98-%E0%B8%9E%E0%B8%A3%E0%B9%89%E0%B8%AD%E0%B8%A1%E0%B9%84%E0%B8%A1%E0%B9%82%E0%B8%84%E0%B8%A3%E0%B9%82%E0%B8%9F%E0%B8%99-i.1049006465.23762574879?sp_atk=26742576-54f0-444a-8da6-7efd6255b5ad&xptdk=26742576-54f0-444a-8da6-7efd6255b5ad', '', '', '2025-06-17 14:27:14', '2025-09-02 16:49:34'),
(27, '', '', '', '2025-06-17 14:30:06', '2025-09-05 16:53:16'),
(29, '', '', '', '2025-06-25 09:56:39', '2025-09-02 16:50:43'),
(30, '', '', '', '2025-06-30 09:37:04', '2025-09-05 15:26:37'),
(31, '', '', '', '2025-07-08 10:57:13', '2025-09-03 09:44:21'),
(33, '', '', '', '2025-07-18 09:20:38', '2025-09-02 16:50:32'),
(34, '', '', '', '2025-07-22 18:40:44', '2025-09-02 16:50:49'),
(35, '', '', '', '2025-08-21 09:31:50', '2025-09-05 14:41:54'),
(36, '', '', '', '2025-08-22 13:22:38', '2025-09-02 16:50:26'),
(37, '', '', '', '2025-08-22 13:43:01', '2025-09-02 16:50:38'),
(38, '', '', '', '2025-08-22 13:51:20', '2025-09-02 16:48:01'),
(39, '', '', '', '2025-08-22 13:57:18', '2025-09-02 16:48:11'),
(40, '', '', '', '2025-08-22 14:05:46', '2025-09-02 16:50:53'),
(41, '', '', '', '2025-08-22 14:12:21', '2025-10-21 14:16:02'),
(43, '', '', '', '2025-08-28 18:50:57', '2025-09-02 16:48:06'),
(44, '', '', '', '2025-09-05 14:46:28', '2025-09-05 14:46:28'),
(45, '', '', '', '2025-09-05 16:13:38', '2025-09-05 16:37:15'),
(46, '', '', '', '2025-09-05 16:37:33', '2025-09-05 16:49:59'),
(47, '', '', '', '2025-09-05 16:39:56', '2025-09-05 16:39:56'),
(48, '', '', '', '2025-09-11 11:01:19', '2025-09-11 11:01:34'),
(49, '', '', '', '2025-09-11 11:07:55', '2025-09-11 11:07:55'),
(50, '', '', '', '2025-09-11 11:09:37', '2025-09-11 11:09:37'),
(51, '', '', '', '2025-09-11 11:21:43', '2025-09-11 11:21:43'),
(52, '', '', '', '2025-09-11 11:22:59', '2025-09-11 11:22:59'),
(53, '', '', '', '2025-09-11 11:25:10', '2025-09-11 11:25:10'),
(54, '', '', '', '2025-09-11 11:42:08', '2025-09-11 11:42:08'),
(55, '', '', '', '2025-09-11 11:45:05', '2025-09-11 11:45:05'),
(56, '', '', '', '2025-09-11 11:45:52', '2025-09-11 11:45:52'),
(57, '', '', '', '2025-09-11 11:46:55', '2025-09-11 11:46:55'),
(58, '', '', '', '2025-09-11 11:49:53', '2025-09-11 11:49:53'),
(59, '', '', '', '2025-09-11 11:50:31', '2025-09-11 11:50:31'),
(60, '', '', '', '2025-09-11 19:16:36', '2025-09-11 19:20:12'),
(61, '', '', '', '2025-09-11 19:22:45', '2025-09-11 19:25:20'),
(62, '', '', '', '2025-09-11 19:28:42', '2025-09-11 19:29:11'),
(63, '', '', '', '2025-09-11 19:31:22', '2025-09-11 19:31:22'),
(64, '', '', '', '2025-10-21 13:54:34', '2025-10-21 14:01:37');

-- --------------------------------------------------------

--
-- Table structure for table `product_list`
--

CREATE TABLE `product_list` (
  `id` int NOT NULL,
  `category_id` int NOT NULL,
  `brand` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` float(12,2) NOT NULL,
  `vat_percent` tinyint NOT NULL DEFAULT '7',
  `vat_price` int NOT NULL DEFAULT '0',
  `min_stock` int NOT NULL DEFAULT '5',
  `image_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `discount_type` enum('amount','percent') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `discount_value` float DEFAULT NULL,
  `discounted_price` float DEFAULT NULL,
  `product_width` float(10,2) DEFAULT NULL,
  `product_length` float(10,2) DEFAULT NULL,
  `product_height` float(10,2) DEFAULT NULL,
  `product_weight` float(10,2) DEFAULT NULL,
  `slow_prepare` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_list`
--

INSERT INTO `product_list` (`id`, `category_id`, `brand`, `name`, `description`, `price`, `vat_percent`, `vat_price`, `min_stock`, `image_path`, `status`, `delete_flag`, `date_created`, `date_updated`, `discount_type`, `discount_value`, `discounted_price`, `product_width`, `product_length`, `product_height`, `product_weight`, `slow_prepare`) VALUES
(14, 1, ' GALAX(แกลแล็กซ์)', 'VGA GALAX GEFORCE RTX 4060 1-CLICK 2X V3 OC - 8GB GDDR6', 'Brand : GALAX\r\n\r\nModel : 1-CLICK 2X V3 OC\r\n\r\nGPU : NVIDIA GeForce RTX 4060\r\n\r\nCUDA Core / Stream Processors : 3072\r\n\r\nCore Clock : 2475 MHz\r\n\r\n1-Click OC Clock: 2490MHz*\r\n\r\n*(by installing Xtreme Tuner Plus Software and using 1-Click OC)\r\n\r\nMemory Clock : 17 Gbps\r\n\r\nMemory Size : 8 GB\r\n\r\nMemory Type : GDDR6\r\n\r\nMemory Interface : 128 bit\r\n\r\nBus Interface : PCI-E 4.0\r\n\r\nHDMI : 1 port\r\n\r\nDisplayPort : 3 port\r\n\r\nDVI : None\r\n\r\nD-Sub (VGA) : None\r\n\r\nMini HDMI : None\r\n\r\nMini DisplayPort : None\r\n\r\nUSB : None\r\n\r\nMicrosoft DirectX Support : 12 Ultimate\r\n\r\nOpenGL : 4.6\r\n\r\nMaximum Resolution : 7680x4320\r\n\r\nPower Input : 1 x 8-pin\r\n\r\nPower Supply Requirement : 550W\r\n\r\nWindows Support : 10/11\r\n\r\nVGA Length : 251mm\r\n\r\nDimension (W x D x H) : 13.30 x 25.10 x 4.10 cm\r\n\r\nNet Weight : 0.00\r\n\r\nPackage Dimension (W x D x H) : 0.00 x 0.00 x 0.00 cm\r\n\r\nGross Weight : 0.00\r\n\r\nVolume : 0.00\r\n\r\nประกัน : 3 ปี', 11731.00, 7, 12553, 5, 'uploads/product//1_Screenshot 2025-06-12 105117.png?v=1750127763', 1, 0, '2025-06-12 10:51:24', '2025-09-02 10:49:06', 'percent', 10, 11298, 15.00, 30.00, 15.00, 1500.00, 0),
(20, 6, 'Madcatz', 'Madcatz MAD 60/68 HE คีย์บอร์ดสวิตช์แม่เหล็กสําหรับเล่นเกม 61/68 คีย์ Latency ต่ํา Full Key Hot Swap', '🎉สวัสดีค่ะ. ยินดีต้อนรับสู่ &quot;สถานีอวกาศ&quot; ยินดีให้บริการครับ\r\n👉ของแท้100%! รับประกันหนึ่งปี!\r\n🎁เช็คสินค้าก่อนส่ง! แผ่นกันกระแทก! มาพร้อมของขวัญเล็กๆ น้อยๆ!\r\n🚚 จัดส่งภายใน 24 ชั่วโมง! มาถึงประมาณ 1-2 วัน!\r\n📣ราคาโปรโมชั่น + คูปอง = คุ้มกว่า\r\n❤ติดตามร้านค้าของเรา! เป็นแฟนของเราและเพลิดเพลินกับส่วนลด 2% เมื่อทำการสั่งซื้อ! และเพลิดเพลินกับการรับประกันนาน 2 ปี\r\n✨【โปรดยืนยันว่าสินค้าอยู่ในสภาพดีก่อนที่จะคลิก &quot;ยืนยันการรับ&quot; มิฉะนั้นหน้าต่างการคืนสินค้าจะถูกปิด】\r\n\r\nข้อกำหนดเบื้องต้น\r\nยี่ห้อ Madcatz ยี่ห้อ: Madcatz\r\nชื่อสินค้า: US-Canada MMAD 60 / 68HE Electric:\r\nเอฟเฟกต์แสงพื้นหลัง: ไม่มีด้าน\r\nจำนวนปุ่ม: 61-70 ปุ่ม / RGB\r\nการเชื่อมต่ออุปกรณ์พร้อมกัน: 1 เครื่อง\r\nประเภทปลั๊ก: Hot-swap พร้อมปุ่มทั้งหมด\r\nระบบที่รองรับ: Windows, MacOS\r\nการเชื่อมต่อ: มีสาย\r\nประเภท: แป้นพิมพ์เชิงกลที่กำหนดเอง', 2215.00, 7, 2371, 5, 'uploads/product//Screenshot 2025-06-17 134019.png?v=1750142427', 1, 0, '2025-06-17 13:40:27', '2025-09-02 16:50:10', 'amount', 1575, 796, NULL, NULL, NULL, 2.20, 0),
(21, 7, 'iHAVECPU', 'iHAVECPU HEADSET (หูฟัง) iHAVECPU MUSES WITH MIC (BLACK/RED)', 'iHAVECPU HEADSET (หูฟัง) iHAVECPU MUSES WITH MIC (BLACK/RED)\r\n\r\n iHAVECPU MUSES WITH MIC หูฟังรุ่น Exclusive จาก iHAVECPU   โดยได้รับแรงบันดาลใจจากเทพธิดา &quot;มิวส์&quot; ผู้ขับร้องบทเพลงอันแสนไพเราะที่แม้แต่เทพเจ้ายังต้องเงี่ยโสดสดับฟัง\r\n\r\nคุณสมบัติสินค้า\r\n\r\n● Headset Brand : KZ\r\n● Color : BLACK / RED\r\n● Connector : 3.5 mm.\r\n● Driver Unit : 10mm.\r\n● Frequency Response : 20Hz ~ 40000 Hz\r\n● Sensitivity : 103+/-3dB\r\n● Input Impedance : 23 Ohms\r\n● Mic. Sensitivity : 112dB\r\n● Warranty : 3 Months\r\n\r\n#เก็บเงินปลายทางได้ครับ\r\n\r\niHAVECPU ถ้าคุณชอบคอมพิวเตอร์ เราคือเพื่อนกัน\r\nบริษัท ไอ แฮฟ ซีพียู จำกัด', 290.00, 7, 311, 5, 'uploads/product//Screenshot 2025-06-17 134311.png?v=1750142596', 1, 0, '2025-06-17 13:43:16', '2025-09-02 16:49:14', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, 0),
(22, 7, 'FiiO', '[ประกันศูนย์ไทย] FiiO JD10 หูฟัง IEMs ไดรเวอร์ Dynamic สุดคุ้ม สำหรับเล่นเกม รองรับ Hi-Res', 'FiiO JD10\r\n\r\nหูฟัง IEMs ไดรเวอร์ Dynamic สุดคุ้ม สำหรับเล่นเกม ประกันศูนย์ไทย\r\nประกันศูนย์ไทย 1 ปี\r\n&quot;ตามเงื่อนไขการรับประกัน&quot;\r\nสินค้าประเภท : IEMs, Inear, หูฟังอินเอียร์\r\nไดรเวอร์ : Dynamic 10 mm\r\nสายหูฟัง : ทองแดง OFC\r\nขั้วหูฟัง : 2พิน0.78\r\nรุ่นที่มีจำหน่าย : 3.5 ไมค์ (ถอดสายได้)/ TypeC (ถอดสายไม่ได้)\r\nไมค์ = แจ๊คขนาด 3.5mm แต่มีไมค์โครโฟนสำหรับคุยสาย\r\nType C = แจ๊คแบบ Type C และ มีไมค์โครโฟน (สำหรับมือถือ Type C เท่านั้น)\r\n\r\nFiiO JD10\r\nหูฟัง IEMs ไดรเวอร์ Dynamic สุดคุ้มราคาจับต้องได้ สำหรับคอ HiFi หรือสำหรับการเล่นเกม\r\nหูฟังเลือกใช้ดรเวอร์ Dynamic ขนาด 10 มม. ไดอะแฟรมโพลีเมอร์คอมโพสิตความแข็งสูง\r\nให้เสียงเบสที่หนักแน่นและเต็มอิ่ม ใช้คอยล์เสียง CCAW ของญี่ปุ่นขนาด 0.033 มม.\r\nที่เบากว่า แข็งแกร่งกว่า ให้เสียงสูงได้ง่ายขึ้น ตอบสนองได้ไวขึ้น ใช้แม่เหล็กนีโอไดเมียม\r\nโบรอนเหล็กประสิทธิภาพสูง N52 ช่วยให้ไดรเวอร์ทำงานได้อย่างเต็มประสิทธิภาพ\r\nใช้การออกแบบอะคูสติกแบบช่องคู่ โดยมีระบบควบคุมท่อนำเสียงในแต่ละโพรง\r\nช่วยลดการบิดเบือนของเสียงช่วยเพิ่มการเคลื่อนไหวของไดอะแฟรม การออกแบบนี้\r\nจะช่วยให้ JD10 สามารถจับรายละเอียดต่างๆ ได้มากขึ้น ส่งผลให้ได้เบสที่รวดเร็ว\r\nผสมผสานกับเสียงกลางและเสียงแหลมได้อย่างลงตัวเพื่อให้ได้เสียงที่น่าฟังอย่างแท้จริง\r\n\r\nข้อมูลสินค้าเบื้องต้น\r\nไดรเวอร์ Dynamic 10 มม.\r\nไดอะแฟรมโพลีเมอร์คอมโพสิต\r\nการออกแบบช่องคู่\r\nคอยล์เสียง CCAW ของญี่ปุ่นขนาด 0.033 มม.\r\nแม่เหล็กนีโอไดเมียมโบรอนเหล็กประสิทธิภาพสูง N52\r\nกำลังขับ 32 Ohm@1kHz\r\nตอบสนองความถี่ 20 - 40 kHz\r\nความไวเสียง 105 dB/mW@1kHz\r\nสายหูฟัง OFC ปราศจากออกซิเจนความบริสุทธิ์สูง\r\nรองรับการควบคุมแบบ ln-ine พร้อมไมค์ในตัว\r\nชิปถอดรหัส DSP รองรับ 24bit/384kHz (เฉพาะรุ่น TypeC)\r\nพร้อมตั้งค่าจูนในตัว 6 แบบ (เฉพาะรุ่นTypeC)\r\nรองรับทุกแพลตฟอร์มและอุปกรณ์ (เฉพาะรุ่นTypeC)\r\nน้ำหนักเบาเพียง 3.7 กรัม (ไม่รวมสาย\r\nรองรับ Hi-Res\r\n\r\nJD10 ถือเป็นมาตรฐานสำหรับคุณภาพเสียงระดับเริ่มต้น โดยมีไดอะแฟรมโพลีเมอร์\r\nคอมโพสิตขนาด 10 มม. ที่ได้รับการคัดสรรมาอย่างพิถีพิถัน ไดอะแฟรมนี้มีความแข็งสูง\r\nความผิดเพี้ยนต่ำ และมีการหน่วงเสียงที่เหมาะสม ช่วยให้เสียงเบสที่หนักแน่นและเต็มอิ่ม\r\nซึ่งผสมผสานกับเสียงกลางและเสียงแหลมได้อย่างลงตัวเพื่อให้ได้เสียงที่น่าฟัง\r\nอย่างแท้จริง นอกจากนี้ ด้วยการปรับแต่งเสียงอย่างพิถีพิถันซึ่งใช้ประโยชน์จากความแข็ง\r\nของไดอะแฟรม ทำให้ JD10 มีความละเอียดสูงในลักษณะที่ไม่ค่อยพบเห็นในหูฟังรุ่นเดียวกัน\r\nสามารถสร้างรายละเอียดเสียงที่ละเอียดอ่อนได้\r\nเสียงเบสที่เร้าใจ ประสบการณ์ที่น่าตื่นตาตื่นใจ การออกแบบช่องคู่\r\nJD10 ใช้การออกแบบอะคูสติกแบบช่องคู่ โดยมีระบบควบคุมท่อนำเสียงในแต่ละโพรง\r\nการควบคุมการหน่วงการไหลของอากาศที่แม่นยำนี้ช่วยลดการบิดเบือนของเสียง\r\nช่วยเพิ่มการเคลื่อนไหวของไดอะแฟรมและการขยายเสียงเบสได้อย่างมีประสิทธิภาพ\r\nเมื่อฟังเพลงที่ซับซ้อน การออกแบบนี้จะช่วยให้ JD10 สามารถจับรายละเอียดต่างๆ\r\nในโน้ตเบสได้มากขึ้น ส่งผลให้ได้เบสที่รวดเร็วและมีรายละเอียดที่สดชื่น\r\nคอยล์เสียง CCAW ของญี่ปุ่นขนาด 0.033 มม.\r\nเบากว่า แข็งแกร่งกว่า\r\nเพื่อส่งมอบเสียงที่มีคุณภาพสูงขึ้นจากไดรเวอร์ JD10 จึงใช้คอยล์เสียง CCAW แบบพิเศษ\r\nของญี่ปุ่นที่มีความละเอียดพิเศษ โดยมีเส้นผ่านศูนย์กลางลวดเพียงประมาณ 0.033 มม.\r\nคอยล์เสียงนี้ช่วยลดมวลรวมของไดรเวอร์ได้อย่างมากเนื่องจากความบางของสาย\r\nและคุณสมบัติของ CCAW ส่งผลให้ไดรเวอร์สามารถผลิตเสียงที่มีระดับเสียงสูงขึ้นได้ง่ายขึ้น\r\nและยังช่วยปรับปรุงการตอบสนองชั่วขณะอีกด้วย ผลลัพธ์ที่ได้คือเสียงที่สะอาดและน่าฟังยิ่งขึ้น\r\nแม่เหล็กประสิทธิภาพสูงภายนอก N52\r\nคุณภาพเสียงอันทรงพลังที่แข็งแกร่ง\r\nJD10 ใช้การออกแบบแม่เหล็กภายนอก ซึ่งทำให้สามารถใช้แม่เหล็กขนาดใหญ่ขึ้นได้\r\nซึ่งจะห่อหุ้มคอยล์เสียงได้อย่างมีประสิทธิภาพมากขึ้น และทำให้เสียงมีพลังมากขึ้น\r\nแม่เหล็กนีโอไดเมียมโบรอนเหล็กประสิทธิภาพสูง N52 ได้รับการเลือกใช้เนื่องจาก\r\nมีพลังงานมากเพียงพอและสามารถให้แรงกระทำได้ทั่วถึงในพื้นที่กว้างอย่างสม่ำเสมอ\r\nช่วยให้ไดรเวอร์ทำงานได้อย่างเต็มประสิทธิภาพ\r\nมีชิปถอดรหัสในตัว (เฉพาะรุ่นTypeC)\r\nชิป DSP อิสระ\r\nJD10 TC มีชิป DSP อิสระประสิทธิภาพสูงในตัว* ช่วยให้คุณฟังเสียงความละเอียดสูง\r\nได้อย่างง่ายดายและสะดวกสบายโดยไม่ต้องใช้อุปกรณ์อื่นใด\r\nรองรับการถอดรหัสเสียงแบบไม่สูญเสียข้อมูล 384kHz/24bit\r\nJD10 TC รองรับการถอดรหัส 384kHz/24bit ซึ่งถือว่าดีที่สุดในระดับเดียวกัน\r\n\r\nคุณสามารถรับฟังเสียงความละเอียดสูงได้อย่างแท้จริงแม้ในอุปกรณ์ระดับเริ่มต้นนี้\r\nสายเคเบิลระดับออดิโอไฟล์ เส้นทางตรงสู่ระบบ HiFi\r\nแกนลวดตัวนำทำจากทองแดงปราศออกซิเจนที่มีความบริสุทธิ์สูง\r\nสามารถส่งสัญญาณเสียงที่สมบูรณ์ได้โดยไม่เกิดข้อผิดพลาด\r\nช่วยให้คุณดื่มด่ำกับโลกแห่งเสียงที่มีความเที่ยงตรงสูงได้ง่ายขึ้น\r\n*เวอร์ชัน JD10 TC ไม่รองรับการออกแบบสายเคเบิลแบบถอดเปลี่ยนได้ 2 พิน 0.78 มม.\r\n\r\nสินค้าประกอบไปด้วย\r\n1. กล่องสินค้า\r\n2. บัตรรับประกัน\r\n3. สายหูฟัง OFC ขั้ว 2พิน 0.78 (เฉพาะรุ่นปกติ)\r\n4. จุกหูฟัง 3 คู่ เล็ก/กลาง/ใหญ่ (ติดตั้งขนาดกลางให้ล่วงหน้า)\r\n\r\nหากลูกค้าสงสัยเงื่อนไขการรับประกันสินค้าข้อใด สามารถสอบถามเข้ามาได้\r\n\r\n#FiiO', 752.00, 7, 805, 5, 'uploads/product//Screenshot 2025-06-17 134844.png?v=1750142930', 1, 0, '2025-06-17 13:48:50', '2025-09-02 16:48:49', 'percent', 50, 403, NULL, NULL, NULL, 20.00, 0),
(23, 8, ' Sunsu(ซันซุ)', '(แพ็ก 12) Muek Groob หมึกกรุบ เส้นบุกปรุงรสหม่าล่า สูตรดั้งเดิม หมึกกรุบซันซุ sunsu', 'สินค้าประกอบด้วย \r\n\r\n1.หมึกกรุบ เส้นบุกปรุงรสหม่าล่า สูตรดั้งเดิม จำนวน 12 ซอง\r\n___________________\r\n\r\n&quot;หมึกกรุบ ชุบมื้ออร่อยให้ตัวคุณ&quot;\r\n🔥 เส้นบุกปรุงรสหม่าล่า\r\n🔥 อร่อย กินเพลิน ได้รสชาติหม่าล่าแท้ๆ\r\n🔥 ทุกสายต้องมีหมึกกรุบ ไม่ว่าจะเป็น สายกินเดี่ยวๆ สายดื่ม สายด่วนกินแบบเร่งรีบ สายดูดเส้น สายไหนก็กินเพลิน\r\n🔥 เพิ่มรสชาติให้มื้ออาหาร กินเดี่ยวอร่อยกรึบ กินคู่อร่อยกรุบ\r\n🔥 ทั้งซองเพียง 25 กิโลแคล หรือประมาณ 4 กิโลแคลต่อซองเล็ก เท่านั้น !!\r\n🔥 ไม่มี คลอเรสโตรอล ไขมันต่ำ\r\n🔥 Size Mini ทานได้สะดวก พร้อมอร่อยได้ทุกที่ ทุกเวลา\r\n\r\nรูปแบบสินค้า\r\n▪️ 1 ซอง บรรจุ 6 ซองเล็ก\r\n▪️ 1 ซอง น้ำหนักสุทธิ 50 กรัม (6 ชิ้น x 8.3 กรัม)\r\n▪️ สินค้ามีอายุ 9 เดือน นับจากวันที่ผลิต\r\n▪️ Product of SUNSU\r\n\r\n#หมึกกรุบ #ชุบมื้ออร่อยให้ตัวคุณ #กินเดี่ยวอร่อยกรึบกินคู่อร่อยกรุบ #ความสุขที่ไม่รู้สึกผิด #SUNSU\r\n___________________\r\n\r\nคำถามที่พบบ่อย\r\n\r\n1) การแก้ไขข้อมูลจัดส่ง และการยกเลิกออเดอร์\r\nทางร้านไม่สามารถแก้ไขข้อมูลชื่อ ที่อยู่ เบอร์โทรคุณลูกค้า หลังได้รับคำสั่งซื้อเข้ามาในระบบ กรุณาตรวจสอบข้อมูลให้ถูกต้องก่อนการกดสั่งซื้อสินค้าทุกครั้ง หากข้อมูลผิด รบกวนทำการยกเลิกออเดอร์พร้อมแจ้งผ่านแชทร้านค้า เพื่อให้ทางร้านตรวจสอบสถานะการจัดออเดอร์กับทางคลังสินค้า หากได้รับการอนุมัติคำขอ จึงค่อยทำการสั่งซื้อเข้ามาใหม่นะคะ\r\n2) ระยะเวลาการจัดส่งสินค้า\r\nใช้เวลาจัดส่งสินค้า กทม. - ปริมณฑล 2-3 วัน, ต่างจังหวัด 3-5 วัน นับจากวันที่สั่งซื้อสินค้าเข้ามาในระบบเรียบร้อย (ไม่รวมวันหยุดเสาร์-อาทิตย์ และวันหยุดนักขัตฤกษ์)\r\n3) การออกใบกำกับภาษี/ใบเสร็จรับเงิน\r\nคุณลูกค้าสามารถแจ้งความประสงค์ขอรับใบกำกับภาษี/ใบเสร็จรับเงิน ผ่านแชทร้านค้า โดยหากต้องการใบกำกับภาษีเต็มรูป &quot;กรุณาแจ้งภายใน 15 วันหลังสั่งซื้อสินค้า และกดยอมรับสินค้าในระบบ&quot;\r\nทางบริษัทจะออกใบกำกับภาษี/ใบเสร็จรับเงิน ให้กับทางลูกค้าภายใน 14 วันทำการ นับจากวันที่แจ้งขอเข้ามา (จะออกใบกำกับภาษีหลังจากลูกค้ากดรับสินค้าแล้วเท่านั้นนะคะ)\r\n\r\nขอบคุณค่ะ🙏', 300.00, 7, 321, 5, 'uploads/product//Screenshot 2025-06-17 135217.png?v=1750143142', 1, 0, '2025-06-17 13:52:22', '2025-09-02 16:47:54', 'percent', 13, 279, NULL, NULL, NULL, 50.00, 0),
(24, 9, 'Llamito', 'Llamito ผงมัทฉะ ออร์แกนิค (Matcha Powder) ขนาด 250g', 'ผงมัทฉะ ออร์แกนิค ตรา ยามิโตะ\r\nOrganic Matcha Powder (Llamito Brand)\r\n\r\nรายละเอียดสินค้า \r\nชื่อผลิตภัณฑ์ (ไทย) : ผงมัทฉะ ออร์แกนิค\r\nชื่อผลิตภัณฑ์ (อังกฤษ) : Organic Matcha Powder\r\nขนาดบรรจุ : 250 กรัม\r\nส่วนประกอบสำคัญ : ผงมัทฉะ ออร์แกนิค (Organic Matcha Powder) 100%\r\nเลขอย. : 1240095960030\r\n\r\nวิธีการรับประทาน  \r\nชงดื่มครั้งละ 1-2 ช้อน (สามารถปรับปริมาณความเข้มได้ตามชอบ) ในน้ำประมาณ 150-200 ml\r\nปั่นพร้อมกับผลไม้หรือสมูทตี้\r\nนำมาเขย่าในขวด Shake ทำให้ละลายได้อย่างทั่วถึง\r\nสามารถชงดื่มได้ตอนท้องว่าง ทั้งช่วงเช้าหรือเย็น\r\n\r\nวิธีการเก็บรักษา: หลังเปิดใช้งาน ควรปิดฝาให้สนิทและเก็บไว้ในตู้เย็น หรือในที่แห้ง หลีกเลี่ยงแสงแดดและความชื้น\r\n\r\nLlamito “Make your lifestyle healthier”', 900.00, 7, 963, 5, 'uploads/product//Screenshot 2025-06-17 142159.png?v=1750144926', 1, 0, '2025-06-17 14:22:06', '2025-09-02 16:49:55', 'amount', 410, 553, NULL, NULL, NULL, 50.00, 0),
(25, 7, ' Monster', '🔥สินค้าขายดีที่สุด🔥 Monster XKT02 BT 5.3 หูฟัง หูฟังบลูทูธ หูฟังไร้สาย หูฟัง monster HIFI', 'ยี่ห้อ Monster รุ่น: XKT02\r\nเวอร์ชันบลูทู ธ: 5.3\r\nประเภทสินค้า:หูฟังบลูทู ธ\r\nอินเทอร์เฟซการชาร์จแบบชาร์จไฟ: Type-C\r\nระยะการส่งสัญญาณบลูทู ธ: 10M\r\nความจุแบตเตอรี่ของหูฟัง: 40mAh\r\nความจุแบตเตอรี่ของห้องชาร์จ: 300mAh\r\nเกรดกันน้ำ: IPX54\r\nฟังก์ชั่นรีโมทคอนโทรล: การควบคุมแบบไร้สาย\r\n\r\nในรายการรวมด้วย:\r\n1 * หูฟัง\r\n1 * คู่มือการใช้งาน \r\n1 * สายชาร์จ\r\n1 * กล่องชาร์จ\r\n\r\nหากสินค้ามีปัญหาทักหาร้านค้าก่อนให้คะแนน\r\nไม่มีกล่องสินค้าทางร้านไม่รับผิดชอบทุกกรณี\r\nโปรดเก็บกล่องสินค้า/ใบเคลมไว้ตลอดระยะเวลาที่สินค้ามีประกัน', 1110.00, 7, 1188, 5, 'uploads/product//Screenshot 2025-06-17 142524.png?v=1750145131', 1, 0, '2025-06-17 14:25:31', '2025-09-02 16:47:23', 'percent', 50, 594, 5.00, 10.00, 13.00, 500.00, 0),
(26, 7, 'Jeep', 'Jeep JP-EW011 หูฟังบลูทูธไร้สาย HiFi HD Call ลดเสียงรบกวน จับคู่เร็วหูฟังบลูทูธ พร้อมไมโครโฟน', 'ข้อมูลจำเพาะ:\r\n\r\nรถจี๊ปยี่ห้อ Jeep\r\n\r\nรุ่น: JP EW011\r\n\r\nเวอร์ชันไร้สาย: V5.3\r\n\r\nขนาดลำโพง: Φ13mm\r\n\r\nความไว: 118 ± 3dB\r\n\r\nความต้านทาน: 250\r\n\r\nการตอบสนองความถี่ 50HZ-20KHZ\r\n\r\nแรงดันไฟฟ้าของเซลล์: 3.7V\r\n\r\nเวลาเล่นเพลง: ประมาณ 4H (ระดับเสียง 80%)\r\n\r\nเวลาสแตนด์บาย: ประมาณ 50 ชม\r\n\r\nเวลาในการชาร์จ: ประมาณ 2 ชม\r\n\r\nความจุแบตเตอรี่หูฟังหูฟัง: 30mAh / 3.7V\r\n\r\nความจุแบตเตอรี่ช่องชาร์จ: 300mAh / 3.7V\r\n\r\nขนาดสินค้า: 61.5x25x45.5 มม\r\n\r\n\r\n\r\nสิ่งที่อยู่ในกล่อง\r\n\r\nหูฟังบลูทู ธ 2 x หูฟังบลูทู ธ\r\n\r\nกรณีชาร์จ 1 x เคสชาร์จ\r\n\r\nสายชาร์จ 1 x\r\n\r\n1 x คู่มือการใช้งาน', 787.00, 7, 843, 5, 'uploads/product//Screenshot 2025-06-17 142707.png?v=1750145234', 0, 1, '2025-06-17 14:27:14', '2025-09-11 16:19:45', 'amount', 568, 275, NULL, NULL, NULL, 20.00, 0),
(27, 7, ' Basspro Max(เบสโปร แม็ก)', 'Basspro Power 2 หูฟังบลูทูธ ทัชสกรีน แบบ in-ear ระบบ ANC + ENC แท้ 100% เบสหนัก เสียงใส กันน้ำ', ' 🎈 สินค้ารับประกัน 1ปี 🎈\r\n\r\nเรามีฝ่ายบริการลูกค้าหลังการขาย ไว้ใจให้เราดูแลนะคะ\r\n\r\nข้อมูลเฉพาะ: ฟังเพลง ได้ทุกที่ทุกเวลา ด้วยหูฟังบลูทูธยี่ห้อ Basspro Power 2 เบสหนัก มาพร้อมระบบ Active Noise Cancelling ตัดเสียงรบกวน ใส่ออกกำลังกาย สบายๆ ไดร์เวอร์ตัวเดียวกับหูฟังราคาแพง\r\nบลูทูธ 5.4\r\nเป็นหูฟังแบบ in-ear\r\nเชื่อมต่อง่าย\r\n\r\nหูฟังบลูทูธยี่ห้อ Basspro Power ออกแบบมาให้ขนาดเล็ก น้ำหนักเบา เก็บไว้ในกระเป๋ากางเกงได้ไม่แกะกะ มาพร้อมกับระบบ Fast Charge USB Type-C\r\nจุดเด่นอีกอย่างคือตัวหูฟังสามารถ กันน้ำได้ระดับ IPX7\r\n ออกกำลังกายได้\r\n\r\n❤️-------------------- เงื่อนไขการเคลมสินค้า----------------❤️\r\n\r\n💎ช่วงเวลาให้บริการของเรา ทุกวัน เวลา 9.00 - 18.00 น.✅\r\n💎สินค้าในร้านจัดส่งจากไทยและใช้เวลาในการจัดส่งประมาณ 2-4วัน ต้องขออภัยหากมีการล่าช้าเกิดขึ้น\r\n📢ข้อมูลควรทราบในกรณีลูกค้ามีความประสงค์ต้องการส่งสินค้าคืนร้านเพื่อ ตรวจสอบ / เคลม📢\r\n❗️เมื่อตรวจสอบเเล้วสินค้าชำรุด บกพร่อง ทางร้านจะนำส่งสินค้าชิ้นใหม่พร้อมทั้ง ชำระค่าส่งพัสดุและค่าบริการให้ทันที\r\n❗️หากสินค้ามีปัญหารบกวนทักเข้ามาแจ้งทางร้านก่อนนะคะ สินค้ามีการรับประกันทุกชิ้น\r\n❗️หากสินค้ามีปัญหาและคุณลูกค้ากดรับสินค้าแล้ว ทางร้านขออนุญาตไม่รับผิดชอบค่าส่งในการส่งเคลมกับทางร้าน\r\n❗️ได้รับสินค้าแล้วรบกวนถ่ายวิดีโอก่อนแกะสินค้า หากไม่มีวิดีโอทางร้านขออนุญาตไม่รับเคลมทุกกรณี\r\n\r\n          🎈 ได้รับสินค้าแล้วรบกวนรีวิว 5 ดาวให้ทางร้านด้วยนะคะ🎈\r\n                     ร้าน IT Union ยินดีให้บริการค่ะ🎁\r\n             🎈 สินค้ารับประกัน 1 ปี  🎈\r\n\r\nThank you very much for using our service.🎉\r\nThank you for shopping with us today.🎀\r\nThank you for your purchase. Please let us know if we can do anything to improve our service.🌈', 699.00, 7, 748, 5, 'uploads/product//Screenshot 2025-06-17 142959.png?v=1750145406', 1, 0, '2025-06-17 14:30:06', '2025-09-05 16:53:16', NULL, NULL, NULL, NULL, NULL, NULL, 25000.00, 0),
(29, 5, 'testpic', 'testpicccccc', 'testpic', 200.00, 7, 214, 5, 'uploads/product/testproducts1.jpg?v=1750821164', 0, 1, '2025-06-25 09:56:39', '2025-09-09 19:24:16', NULL, NULL, NULL, NULL, NULL, NULL, 0.50, 1),
(30, 5, '', 'Test', '', 100.00, 7, 107, 5, 'uploads/product/jyumk0mc.png?v=1757060797', 0, 1, '2025-06-30 09:37:04', '2025-09-05 15:36:55', NULL, NULL, NULL, NULL, NULL, NULL, 1000.00, 0),
(31, 8, 'MINE', 'เหลี่ยม', 'เหลี่ยม', 1000.00, 7, 1070, 5, 'uploads/product/minecraft-icon-0.png?v=1751947033', 1, 0, '2025-07-08 10:57:13', '2025-09-03 09:44:21', 'percent', 3, 1038, 30.00, 30.00, 30.00, 500.00, 0),
(33, 23, 'SPYxFAMILY', 'SPYxFAMILY เล่ม 9-14 (แพ็คชุด)', 'รายละเอียดสินค้า\r\nไฮไลท์\r\nสายลับสุดลับต้องสร้างครอบครัวปลอมเพื่อภารกิจสำคัญ แต่ลูกสาวดันอ่านใจได้ ส่วนภรรยาก็เป็นนักฆ่า! เมื่อทุกคนต่างปิดบังตัวตน ความฮาและความลุ้นระทึกจึงเกิดขึ้นไม่หยุด!\r\nรายละเอียด\r\nสุดยอดสปาย &lt;สนธยา&gt; ได้รับคำสั่งให้สร้าง “ครอบครัว” เพื่อลอบเข้าไปในโรงเรียนชื่อดัง แต่ “ลูกสาว” ที่เขาได้พบดันเป็นผู้มีพลังจิตอ่านใจคน! “ภรรยา” เป็นมือสังหาร!?\r\n\r\nโฮมคอเมดี้สุดฮาของครอบครัวปลอมๆที่ต่างฝ่ายต่างปกปิดตัวจริงของกันและกัน ที่ต้องเผชิญหน้ากับการสอบเข้าและปกป้องโลก!!', 610.00, 7, 653, 5, 'uploads/product/hznn7keo.png?v=1752805394', 1, 0, '2025-07-18 09:20:38', '2025-09-02 16:50:32', 'amount', 411, 242, 20.00, 20.00, 10.00, 200.00, 0),
(34, 26, 'เทคนิคตรัง', 'สมุดโน๊ตสีดำ', 'สมุดโน๊ตสีดำ 40 หน้า', 20.00, 7, 22, 5, NULL, 1, 0, '2025-07-22 18:40:43', '2025-09-02 16:50:49', 'percent', 5, 21, NULL, NULL, NULL, 500.00, 0),
(35, 24, 'DOG', 'DOG', 'DOG', 100000.00, 7, 107000, 5, 'uploads/product/e8d9faef1c23e3d2fb00c8d3262dcdd1.jpg?v=1755743510', 0, 1, '2025-08-21 09:31:50', '2025-09-05 15:37:45', NULL, NULL, NULL, 10.00, 10.00, 10.00, 10000.00, 0),
(36, 24, 'Sony', 'Sony ZV-E10 kit 16-50mm. zve10 มือ1 ประกันศูนย์ แถมเม็ม32gb ฟิล์มกันรอย กระเป๋า', 'Sony ZV-E10 + 16-50mm kit  (ประกันศูนย์ไทย)\r\n\r\nสินค้าใหม่ กล่องซีล \r\n\r\nรับประกันศูนย์ Sony ประเทศไทย 1 ปี\r\n\r\nแถมเม็ม32gb ฟิล์มกันรอย กระเป\r\n\r\n\r\n\r\nเซนเซอร์ APS-C 24 ล้านพิกเซล\r\n\r\nเลนส์ kit Sony 16-50mm f/3.5-5.6\r\n\r\nXAVC-S 4k 30p 100mbps\r\n\r\nFHD 120p\r\n\r\nกันสั่น 2 โหมด Standard/Active\r\n\r\nมีโหมด Auto exposure และ face priority\r\n\r\nโหมด Rroduct Showcase\r\n\r\nปุ่ม Bokeh Switch\r\n\r\n\r\n\r\nกล้องสาย Vlog ที่ขยับมาใช้เซนเซอร์ APS-C เปลี่ยนเลนส์ได้ เซนเซอร์ APS-C 24 ล้านพิกเซล เลนส์ kit Sony 16-50mm f/3.5-5.6 XAVC-S 4k 25p 100mbps FHD 100p กันสั่น 2 โหมด Standard/Active มีโหมด Auto exposure และ face priority โหมด Product Showcase ปุ่ม Bokeh Switch มีปุ่มเฉพาะสำหรับสลับโหมด S&amp;Q/ภาพนิ่ง/วิดีโอ รองรับ USB-C streaming กับ PC แบบเสียบแล้วใช้ได้ทันที จอ LCD ฟลิบหมุนได้รอบทิศทาง ตัวบอดีคล้ายกับซีรีส์ A6xxx แต่ไม่มีช่องมองภาพ EVF\r\n\r\n\r\n\r\nกล้องเลนส์แบบเปลี่ยนได้สำหรับการทำ Vlog\r\n\r\nเซนเซอร์ Exmor™ CMOS25 ขนาดใหญ่ APS-C 24.2 ล้านเมกะพิกเซล\r\n\r\nไมโครโฟนแบบทิศทาง 3 แคปซูลพร้อมตัวตัดเสียงลม\r\n\r\nคุณสมบัติที่ออกแบบมาเพื่อ Vlogger – การตั้งค่า Product Showcase, สวิตช์โบเก้, ปุ่มภาพนิ่ง/ภาพยนตร์/S&amp;Q\r\n\r\nการเชื่อมต่อที่ยืดหยุ่นเพื่อการแชร์ที่ง่ายดาย\r\n\r\nประเภทเซนเซอร์\r\n\r\nเซนเซอร์ Exmor CMOS ชนิด APS-C (23.5 x 15.6 มม.)\r\n\r\nจำนวนพิกเซล (ที่ใช้งานจริงประมาณ 24.2 ล้านพิกเซล\r\n\r\nความไวแสง ISO (RECOMMENDED EXPOSURE INDEX)\r\n\r\n[ภาพนิ่ง] ISO 100-32000 (สามารถตั้งเลข ISO ตั้งแต่ ISO 50 จนถึง ISO 51200 ให้เป็นช่วง ISO แบบขยาย), AUTO (ISO 100-6400, เลือกขีดจำกัดล่างและขีดจำกัดบนได้), [ภาพยนตร์] เทียบเท่า ISO 100-32000, AUTO (ISO 100-6400, เลือกขีดจำกัดล่างและขีดจำกัดบนได้)\r\n\r\nระยะเวลาการใช้งานแบตเตอรี่ (ภาพนิ่ง) ประมาณ 440 ภาพ (จอภาพ LCD) (มาตรฐาน CIPA)26\r\n\r\nประเภทจอภาพ TFT ชนิด 7.5 ซม. (3.0-type)\r\n\r\n\r\n\r\nอุปกรณ์ภายในกล่อง\r\n\r\n\r\n\r\nSony ZV-E10 camera body\r\n\r\nSony E 16-50mm 3.5-5.6/PZ OSS lens\r\n\r\nShoulder strap\r\n\r\nWind screen for microphone\r\n\r\n1x Sony NP-FW50 battery\r\n\r\nUSB-A to USB-C cable\r\n\r\nAC adapter\r\n\r\nStartup guide and documentation\r\n\r\n\r\n\r\n⚠️⚠️เงื่อนไขในการรับประกัน⚠️', 27990.00, 7, 29950, 5, 'uploads/product/Screenshot 2025-08-22 131850.png?v=1755843758', 1, 0, '2025-08-22 13:22:38', '2025-09-02 16:50:26', 'amount', 2990, 26960, 20.00, 20.00, 20.00, 1000.00, 0),
(37, 24, 'Strawberry Tuesdae', '💛หมอนพิมพ์ลาย3Dรูปทุเรียนหมอนทองลูกโตๆใบใหญ่มากกก✨', '💛หมอนพิมพ์ลาย3Dรูปทุเรียนหมอนทองลูกโตๆใบใหญ่มากกก✨ ขนาด 40*60 เซนติเมตร\r\n\r\nพิมพ์ลายชัด สีสวย สด สามารถซักทำความสะอาดได้ \r\n\r\n\r\n\r\n✨ผลิตจากผ้าและใยเกรดดี ไม่เก็บฝุ่น สามารถซักทำความสะอาดได้บ่อยครั้ง ใยคืนตัวไม่เป็นก้อน\r\n\r\n✨ภาพถ่ายสินค้าจริง (รับสินค้าเองจากโรงงาน)\r\n\r\n✨สินค้าทุกชิ้นสามารถจัดส่งแบบเก็บปลายทาง(COD) ได้ \r\n\r\n✨มีบริการเขียนการ์ดฟรี 💌 สำหรับให้เป็นของขวัญ \r\n\r\nเพื่อน คนรัก คนสนิท โดยไม่มีราคาขั้นต่ำ (รบกวนทักแชท)\r\n\r\n\r\n\r\n🚛การจัดส่ง shipping\r\n\r\n•ร้านจัดส่งเฉพาะวันจันทร์-อาทิตย์ (หยุดวันพุธ) \r\n\r\nตัดรอบเวลา 10.00น.\r\n\r\n•ทุกวันจันทร์-อาทิตย์ สั่งสินค้าก่อน 10.00น. จะจัดส่งภายในวันนั้นเลย แต่ถ้าสั่งเลยจาก 10.00น.ร้านจะจัดส่งในวันถัดไป นับจากวันที่สั่งซื้อ\r\n\r\n•หากลูกค้าสั่งซื้อสินค้าในวันอังคารหลัง 10.00น.หรือวันพุธ ร้านจะจัดส่งสินค้าในวันพฤหัสบดี\r\n\r\n\r\n\r\n📬หากต้องการติดต่อ สอบถามพูดคุย สามารถกดปุ่ม แชทเลย พูดคุย 24 ช.ม. สามารถทิ้งข้อความไว้ ทางร้านจะรีบตอบกลับโดยเร็วที่สุดค่ะ 👍🏻😊\r\n\r\n𝗦𝘁𝗿𝗮𝘄𝗯𝗲𝗿𝗿𝘆 𝗧𝘂𝗲𝘀𝗱𝗮𝗲 ยินดีให้บริการ ! 💕', 149.00, 7, 160, 5, 'uploads/product/Screenshot 2025-08-22 134024.png?v=1755844981', 1, 0, '2025-08-22 13:43:01', '2025-09-02 16:50:38', NULL, NULL, NULL, 60.00, 50.00, 70.00, 500.00, 0),
(38, 24, 'AUV', '[ พร้อมส่ง ] ร่มกันแดด กันยูวีUV ร่มกันฝน สีพื้นไม่มีลาย แบบพับ 3 ตอน', '☂️พร้อมส่งทุกสีค่า ร่มเป็นสีพื้นไม่มีลาย\r\n\r\n▶️ร่มพับ 3 ตอน พับเก็บได้ง่าย \r\n\r\n✔️กันแดด กันฝน กันUVค่า\r\n\r\n\r\n\r\n🌈ร้านเราส่งสินค้าทุกวันจันทร์-เสาร์\r\n\r\nหยุดวันอาทิตย์และวันนักขัตฤกษ์นะคะ\r\n\r\n🚚ตัดรอบส่ง 15.00น. สั่งหลังจากนั้นส่งวันถัดไปค่ะ\r\n\r\n\r\n\r\nสินค้าทุกรายการในร้านพร้อมส่งจากกรุงเทพค่ะ\r\n\r\n\r\n\r\n▶️หากสินค้ามีปัญหา/ชำรุด/เสียหาย/ผิดแบบ\r\n\r\nลูกค้าทักหาแอดมินเพื่อช่วยแก้ไขปัญหาก่อนกดดาวนะคะ🙏\r\n\r\n\r\n\r\n🚩เงื่อนไขการรับเคลมสินค้า🚩\r\n\r\n-ลูกค้าถ่ายวิดีโอ รูปภาพสินค้าเมื่อแกะพัสดุ\r\n\r\n-ห้ามดัดแปลงสภาพสินค้าเช่น แกะ ถอดเปลี่ยนสินค้า \r\n\r\nในกรณีนี้ทางร้านขอไม่รับเคลมหรือเปลี่ยนนะคะ', 55.00, 7, 59, 5, 'uploads/product/Screenshot 2025-08-22 134931.png?v=1755845480', 1, 0, '2025-08-22 13:51:20', '2025-09-02 10:23:18', NULL, NULL, NULL, 30.00, 60.00, 30.00, 200.00, 0),
(39, 24, 'CIVAGO', 'CIVAGO（26oz） แก้วกาแฟสแตนเลสซับเซรามิกพร้อมฝาปิดขวดสูญญากาศสามารถเก็บความร้อนและความเย็น', '• ซับในเซรามิค\r\n\r\n• สแตนเลส 304\r\n\r\n• ฉนวนสุญญากาศ\r\n\r\n• หลักฐานการรั่วไหล\r\n\r\n• ปลอดสาร Bpa\r\n\r\n• ร้อน 24 ชั่วโมง• ร้อน 12 ชั่วโมง\r\n\r\n• ทนทาน\r\n\r\n• สะดวกในการใช้งาน', 999.00, 7, 1069, 5, 'uploads/product/Screenshot 2025-08-22 135352.png?v=1755845838', 1, 0, '2025-08-22 13:57:18', '2025-09-02 16:48:11', 'amount', 610, 459, 22.00, 22.00, 30.00, 600.00, 0),
(40, 24, '海苔脆片', '🥗สาหร่ายทะเลปรุงรส (100ซองมี200ชิ้น) สาหร่ายซูชิ สาหร่ายห่อข้าว เด็กๆอร่อยถูกใจ 海苔脆片', '🥗สาหร่ายทะเลปรุงรส 100ซอง สาหร่ายซูชิ สาหร่ายห่อข้าว เด็กๆอร่อยถูกใจ 海苔脆片\r\n\r\n\r\n\r\nขนาด: 100ซอง (1ซองมี2แผ่น)\r\n\r\n\r\n\r\n#สาหร่ายทะเลปรุงรส #สาหร่ายซูชิ #สาหร่ายห่อข้าว #海苔脆片\r\n\r\n\r\n\r\n---------------------------------------------------------\r\n\r\n\r\n\r\n‼️หากสินค้ามีปัญหา‼️\r\n\r\nทักแชทหาร้านก่อน ร้านยินดีช่วยรับผิดชอบและแก้ไขปัญหาพบเจอค่ะ อย่าเพิ่งรีวิวให้ดาวนะคะ 🥰\r\n\r\n\r\n\r\n👩🏻‍💻ร้านเรามีแอดมินคอยตอบแชททุกวัน เวลา 08.00-18.00 ค่ะ\r\n\r\n\r\n\r\n🛑หลังจากได้รับพัสดุ รบกวนถ่ายวิดีโอตอนแกะพัสดุด้วยนะคะ กรณีมีสินค้าผิด หรือมีปัญหาจะได้นำหลักฐานมาเครมได้ ขอบคุณค่ะ  🙏🏼🙏🏼', 100.00, 7, 107, 5, 'uploads/product/Screenshot 2025-08-22 140352.png?v=1755846346', 0, 1, '2025-08-22 14:05:46', '2025-10-21 14:33:10', 'percent', 45, 59, 60.00, 60.00, 60.00, 600.00, 0),
(41, 24, '', 'ขนมปี๊บ ขนมปังปี๊บ ตราศรีกรุง SK ปี๊บเล็ก 400 กรัม - 1.2 กิโล อ่านก่อนกดสั่งซื้อ', 'ขนมปังปี๊บ ปี๊บเล็กบรรจุ 1.2 กก. อ่านก่อนกดสั่งซื้อ\r\n\r\n\r\n\r\n***กรุณาอ่านก่อนสั่งซื้อ  สินค้านี้งดคืน งดเคลมทุกกรณี หากสั่งซื้อแล้วถือว่าเป็นไปตามเงื่อนไขนี้ 🙏🏻*** \r\n\r\n1. ขนมเป็นสินค้าที่สามารถแตกหักได้ระหว่างการขนส่งไม่ว่าจะแพ็คป้องกันอย่างไรก็สามารถกระทบกระเทือนได้ กรุณาพิจารณาก่อนสั่งซื้อ และต้องยอมรับสภาพสินค้าที่ได้รับ โดยเฉพาะขนมแตกหรือกล่องบุบ ทางร้านงดคืน งดเคลมทุกกรณี หากคุณลูกค้านำไปจำหน่ายต่อกรุณาสั่งซื้อชนิดสินค้าที่เหมาะสมค่ะ เนื่องจากมีความเสี่ยงสูงที่ขนมจะแตกหรือหักได้จากการขนส่งซึ่งอยู่เหนือการควบคุม ทางร้านจึงงดคืน/งดเคลมค่ะ\r\n\r\n2. บรรจุภัณฑ์มีวันผลิตและหมดอายุระบุ ทางร้านจำหน่ายสินค้าที่มีอายุการเก็บรักษาไม่ต่ำกว่า 6 เดือน ซึ่งขนมมีอายุ 1 ปีหลังจากวันผลิต ทางร้านจึงงดคืน/งดเคลมเนื่องจากขนมเหม็นหืน รสชาติผิดเพี้ยน\r\n\r\n3. ขนมงดรับคืนในเรื่องของรสชาติทุกรณี เนื่องจากความชอบรสชาติ ของแต่ละท่านไม่เหมือนกัน\r\n\r\n#ขนมปังปี๊บ\r\n\r\n#ขนมปี๊บ\r\n\r\n#ขนม#ปี๊บ\r\n\r\n#ทานเล่น\r\n\r\n#ของฝาก\r\n\r\n#Vfoods\r\n\r\n#ศรีกรุง\r\n\r\n#ของฝาก\r\n\r\n#ขาไก่', 169.00, 7, 181, 5, 'uploads/product/Screenshot 2025-08-22 141022.png?v=1755846741', 1, 0, '2025-08-22 14:12:21', '2025-09-02 09:38:01', NULL, NULL, NULL, 60.00, 60.00, 60.00, 1200.00, 0),
(43, 28, 'CAR', 'รถของเล่น4ล้อ', 'รถของเล่น4ล้อ', 1000.00, 7, 1070, 5, NULL, 0, 1, '2025-08-28 18:50:57', '2025-09-05 16:33:08', 'percent', 90, 107, 60.00, 60.00, 60.00, 500.00, 1),
(44, 5, 'ทดสอบ', 'ทดสอบ', 'ทดสอบ', 20.00, 7, 22, 5, 'uploads/product/144643956_10136603.jpg?v=1757058389', 0, 1, '2025-09-05 14:46:28', '2025-09-05 15:47:07', NULL, NULL, NULL, 20.00, 20.00, 20.00, 50.00, 0),
(45, 7, 'asdasd', 'asdad', 'adasd', 20.00, 7, 22, 5, 'uploads/product/happy-4488260_1280.jpg?v=1757063618', 0, 1, '2025-09-05 16:13:38', '2025-09-09 19:23:53', NULL, NULL, NULL, 20.00, 20.00, 20.00, 20.00, 1),
(46, 6, '', 'sdasdasd', 'asda', 3434.00, 7, 3675, 5, NULL, 0, 1, '2025-09-05 16:37:33', '2025-09-09 19:24:00', NULL, NULL, NULL, NULL, NULL, NULL, 333.00, 0),
(47, 7, 'sadasd', 'asdasd', 'asdad', 2.00, 7, 3, 5, NULL, 0, 1, '2025-09-05 16:39:56', '2025-09-09 19:24:08', NULL, NULL, NULL, NULL, NULL, NULL, 2222.00, 0),
(48, 15, 'Dinosaur', 'Dinosaur', 'Dinosaur', 1200.00, 7, 1284, 5, 'uploads/product/Copilot_20250620_155156.png?v=1757563280', 0, 1, '2025-09-11 11:01:19', '2025-09-11 16:19:31', 'amount', 84, 1200, NULL, NULL, NULL, 200.00, 0),
(49, 15, 'SHARK', 'Shark', 'SHARK', 467.00, 7, 500, 5, 'uploads/product/u1f988_u1f431.png?v=1757563675', 0, 1, '2025-09-11 11:07:55', '2025-09-11 11:08:57', NULL, NULL, NULL, NULL, NULL, NULL, 100.00, 0),
(50, 15, 'SHARK', 'SHARK', 'SHARK', 467.00, 7, 500, 5, 'uploads/product/1_u1f988_u1f431.png?v=1757563777', 0, 1, '2025-09-11 11:09:37', '2025-09-11 11:20:38', NULL, NULL, NULL, NULL, NULL, NULL, 100.00, 0),
(51, 8, 'FF', 'เฟรนซ์ไฟ', 'เฟรนซ์ไฟ', 23.00, 7, 25, 5, 'uploads/product/French_Fries.jpg?v=1757564503', 0, 1, '2025-09-11 11:21:43', '2025-09-11 11:22:33', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, 0),
(52, 8, 'FF', 'เฟรนไฟ', 'เฟรนไฟ', 23.00, 7, 25, 5, 'uploads/product/1_French_Fries.jpg?v=1757564579', 0, 1, '2025-09-11 11:22:59', '2025-09-11 11:24:50', NULL, NULL, NULL, NULL, NULL, NULL, 30.00, 0),
(53, 8, 'FF', 'FF', 'FF', 23.00, 7, 25, 5, 'uploads/product/2_French_Fries.jpg?v=1757564710', 0, 1, '2025-09-11 11:25:10', '2025-09-11 16:19:37', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, 0),
(54, 4, 'ฟหก', 'ฟหก', 'ฟหก', 5.00, 7, 6, 5, NULL, 0, 1, '2025-09-11 11:42:08', '2025-09-11 11:42:19', NULL, NULL, NULL, NULL, NULL, NULL, 200.00, 0),
(55, 7, 'ฟฟฟ', 'ฟฟฟ', 'ฟฟฟ', 11.00, 7, 12, 5, NULL, 0, 1, '2025-09-11 11:45:05', '2025-09-11 16:19:43', NULL, NULL, NULL, NULL, NULL, NULL, 111.00, 0),
(56, 7, 'ฟฟฟ', 'ฟฟฟq', 'ฟฟฟ', 11.00, 7, 12, 5, NULL, 0, 1, '2025-09-11 11:45:52', '2025-09-11 16:19:59', NULL, NULL, NULL, NULL, NULL, NULL, 111.00, 0),
(57, 7, 'qqq', 'qqq', 'qqq', 111.00, 7, 119, 5, NULL, 0, 1, '2025-09-11 11:46:55', '2025-09-11 16:19:51', NULL, NULL, NULL, NULL, NULL, NULL, 11.00, 0),
(58, 7, 'aaa', 'aaa', 'aaa', 111.00, 7, 119, 5, NULL, 0, 1, '2025-09-11 11:49:53', '2025-09-11 16:19:25', NULL, NULL, NULL, NULL, NULL, NULL, 111.00, 0),
(59, 7, 'sdasd', 'asdad', 'asdasd', 23123.00, 7, 24742, 5, NULL, 0, 1, '2025-09-11 11:50:31', '2025-09-11 16:19:55', NULL, NULL, NULL, NULL, NULL, NULL, 2323.00, 0),
(60, 24, 'KYSONA', 'KYSONA Jupiter Ultra Dual-8K Hz เมาส์สําหรับเล่นเกมไร้สายน้ําหนักเบาเป็นพิเศษ 46g PAW3950 เซ็นเซอร์สูงสุด 30000 DPI', '🚀เกี่ยวกับรายการนี้:\r\n\r\n🔥น้ำหนักเบาเป็นพิเศษ: 46 กรัม: เทคโนโลยีชั้นนำของอุตสาหกรรมในการลดน้ำหนักเมาส์ เปลือกนอกที่แข็งแรงการออกแบบภายในที่สมดุลจับได้ง่ายข้อมือที่ปราศจากความเครียด\r\n\r\n🔥PIXART 3950 เซ็นเซอร์ระดับเรือธงชั้นยอด: ประสิทธิภาพระดับ Esports มีมากถึง 30000 DPI; การติดตามความเร็วสูง 750 IPS การเร่งสูงสุด 50G ความแม่นยำความไวความเสถียรที่เหนือชั้น\r\n\r\n🔥MCU ขอบตัด Nordic 52840: ชิปควบคุมหลักของ Esports การออกแบบที่ประหยัดพลังงานการตรวจสอบแบตเตอรี่ที่แม่นยำการเล่นเกมที่มีความหน่วงต่ำ\r\n\r\n🔥Ergo สมมาตร: รายละเอียดด้านแบนไม่มีการปั่นไม่ลื่นไถลสะดวกสบายสำหรับเกมเมอร์มือใหญ่กลางเหมาะสำหรับฝ่ามือก้ามปูหรือที่จับปลายนิ้ว\r\n\r\n🔥การเคลือบผิวสัมผัสด้วยน้ำแข็ง: ประสบการณ์การสัมผัสระดับเฟิร์สเรตการเคลือบผิวพิเศษสัมผัสเย็นทนต่อการสึกหรอป้องกันเหงื่อมือไม่ลื่นง่าย\r\n\r\n🔥การอัพเดทตำแหน่ง 8000Hz: เทคโนโลยี Hyperpolling; Jupiter Ultra ส่งการอัปเดตตำแหน่ง 8000 ตำแหน่งต่อวินาที ลดเวลาในการเคลื่อนไหวของเมาส์ที่เคอร์เซอร์เข้าและออก เมื่อยล้าเร็วกว่าเมาส์สำหรับเล่นเกม 1000Hz ถึง 8 เท่า\r\n\r\n🔥พาราคอร์ด 8K น้ำหนักเบา 8K: Dongle ความเร็วสูง 8K ที่ได้รับการปรับปรุง ออกแบบมาสำหรับอัตราการสำรวจ 8K การส่งข้อมูลที่เสถียรมากขึ้นประสิทธิภาพไร้สายชั้นบนสุด\r\n\r\n🔥ไดรเวอร์คู่ปรับแต่งขั้นสูงสุด: KYSONA HUB / Web Driver ปรับแต่ง DPI, อัตราการสำรวจ, LOD, มาโครและอื่น ๆ ไม่จำเป็นต้องดาวน์โหลด เข้าสู่ระบบเข้าสู่หน้าเว็บของเราเพื่อเข้าถึงได้ทันที มีประสิทธิภาพสะดวกและใช้งานง่าย\r\n\r\n🔥Omron Optical Micro Switch: ตอบสนองเร็วกว่าสวิตช์เชิงกลทั่วไป 3 เท่าหลีกเลี่ยงการคลิกสองครั้งทนทานมากขึ้นถึง 70 ล้านคลิกอายุการใช้งาน\r\n\r\n🔥การเชื่อมต่อ Tri-Mode ไร้สายความเร็วสูง: เข้ากันได้กับอุปกรณ์ต่างๆการสลับอย่างง่ายการตอบสนองทันทีอเนกประสงค์สำหรับสถานการณ์การประยุกต์ใช้แอปพลิเคชันที่หลากหลาย\r\n\r\n\r\n\r\n👍ข้อมูลจำเพาะ:\r\n\r\n🛒เซ็นเซอร์: PAW 3950 Gaming Optical Sensor: PAW 3950 Gaming Optical Sensor\r\n\r\n🛒อัตราการสำรวจ: 8000Hz\r\n\r\n🛒DPl ที่ปรับได้: DPI ที่ปรับได้ 0n-the-Fly (800-30000)\r\n\r\n🛒การเร่งความเร็วสูงสุด: การเร่งความเร็วสูงสุด 750IPS / 50G\r\n\r\n🛒คลิกตลอดอายุการใช้งาน: Omron Optical Micro Switch; วงจรชีวิต 70 ล้านคลิก\r\n\r\n🛒ความจุแบตเตอรี่สูงสุด: 300mAh (สูงสุด 50 ชั่วโมง)\r\n\r\n🛒เวลาในการชาร์จ: ประมาณ 2 ชั่วโมง\r\n\r\n🛒ขนาด 128 มม. * 64 มม. * 40 มม. ขนาด: 128 มม. * 64 มม. * 40 มม\r\n\r\n🛒สายไฟ: 1.6 ม. Type-C Paracord\r\n\r\n🛒น้ำหนัก: 55g\r\n\r\n\r\n\r\n🎁สิ่งที่อยู่ในกล่อง (KYSONA Jupiter Ultra Gaming Mouse - พร้อมเท้าแก้ว)\r\n\r\n1. เมาส์เกมมิ่งอัลตร้า KYSONA Jupiter Ultra Gaming Mouse X 1\r\n\r\n2. คู่มือการใช้งาน X 11 คู่มือผู้ใช้\r\n\r\n3. สายเคเบิล Paracord Type-C 6M 8K X 1\r\n\r\n4. ดองเกิล X 11 8K\r\n\r\n5. ด้ามจับกันลื่น X 1 ชุด\r\n\r\n6. เมาส์แก้ว Feets X 1 ชุด\r\n\r\n\r\n\r\n🎁เมาส์เกมมิ่ง KYSONA Jupiter Ultra - ไม่มีเท้าแก้ว)\r\n\r\n1. เมาส์เกมมิ่งอัลตร้า KYSONA Jupiter Ultra Gaming Mouse X 1\r\n\r\n2. คู่มือการใช้งาน X 11 คู่มือผู้ใช้\r\n\r\n3. สายเคเบิล Paracord Type-C 6M 8K X 1\r\n\r\n4. ดองเกิล X 11 8K\r\n\r\n5. ด้ามจับกันลื่น X 1 ชุด\r\n\r\n\r\n\r\n💯เรื่องราวของแบรนด์:\r\n\r\nKYSONA ก่อตั้งขึ้นในปี 2004 เชี่ยวชาญในการจัดหาอุปกรณ์เสริมคอมพิวเตอร์และมือถือ OEM และ ODM สำหรับลูกค้าทั่วโลกผลิตภัณฑ์ของเรามีจำหน่ายในกว่า 100 ประเทศและภูมิภาค\r\n\r\n\r\n\r\nด้วยการวิจัยและพัฒนาที่แข็งแกร่งความสามารถในการผลิตการตลาดและการจัดการ KYSONA ได้สร้างระบบโรงงานที่ครอบคลุมและผ่านการรับรองมาตรฐาน ISO9001: 2015, BSCI, GRS, FSC และ ICS เพื่อรับประกันคุณภาพของผลิตภัณฑ์\r\n\r\n\r\n\r\n💯การรับประกัน:\r\n\r\n&gt; หลังจากได้รับสินค้าโปรดตรวจสอบว่าบรรจุภัณฑ์สมบูรณ์หรือไม่และคุณภาพของผลิตภัณฑ์มีปัญหาหรือไม่\r\n\r\nปัญหาด้านคุณภาพภายในหนึ่งปี:\r\n\r\n1. ให้บริการต่ออายุภายในหนึ่งปีนับจากวันที่ซื้อ\r\n\r\n2. หากผลิตภัณฑ์มีปัญหาด้านความเสียหายและคุณภาพที่ไม่ใช่ของเทียม KYSONA จะให้บริการต่ออายุฟรี', 1080.00, 7, 1156, 5, 'uploads/product/Screenshot 2025-09-11 191056.png?v=1757592996', 1, 0, '2025-09-11 19:16:36', '2025-09-11 19:16:36', 'percent', 39.5, 699, 12.80, 6.40, 44.00, 55.00, 0),
(61, 24, 'Americano Cup', 'Americano Cup แก้วกาแฟ ถ้วยน้ำความ 710ML 316 สแตนเลส', 'คุณสมบัติเด่น: - สแตนเลส 316 ทนทาน: ถ้วยเก็บความร้อนขนาด 710ML ทำจากสแตนเลส 316 ที่มีความทนทานสูง - ฝาปิดป้องกันการรั่วซึม: มาพร้อมฝาปิดที่มีแหวนยางปิดผนึกเพื่อป้องกันการรั่วซึม รับประกันจากผู้ผลิตนาน 6 เดือน มีหลากหลายสีให้คุณเลือก เลือกสีที่คุณชื่นชอบและเพลิดเพลินกับการใช้งานถ้วยเก็บความร้อนที่มีคุณภาพสูงและดีไซน์ที่สวยงาม!\r\n\r\nรายละเอียดสินค้าสร้างโดย AI ดูรายละเอียดสินค้าต้นฉบับ', 447.00, 7, 479, 5, 'uploads/product/Screenshot 2025-09-11 192047.png?v=1757593365', 1, 0, '2025-09-11 19:22:45', '2025-09-11 19:22:45', 'percent', 70, 144, 10.00, 30.00, 30.00, 500.00, 0),
(62, 24, 'Lemona', 'กระเป๋าเดินทางล้อลาก Lemona ขนาด 20 นิ้ว ABS+PC', '🌟 คุณสมบัติเด่นของกระเป๋าเดินทาง LEMONA รุ่น HUGE014 🌟\r\n\r\n- วัสดุ ABS+PC ทนทานต่อแรงกระแทกและน้ำหนักเบา\r\n\r\n- ล้อหมุนอิสระ 360 องศา ลากลื่น เงียบ ควบคุมง่าย\r\n\r\n- น้ำหนักเบาและพกพาสะดวก\r\n\r\n\r\n\r\n🎨 สีสันและรุ่นที่มีให้เลือก 🎨\r\n\r\n- สีฟ้า, สีขาว (พร้อมกระเป๋าพับได้ 1 ชิ้น), สีเขียว, สีม่วง, สีชมพู, สีดำ\r\n\r\n- รุ่น: Huge014\r\n\r\n\r\n\r\n🧳 รายละเอียดเพิ่มเติม 🧳\r\n\r\nกระเป๋าเดินทางขนาด 20 นิ้ว มาพร้อมกับคันชักอลูมิเนียมที่แข็งแรงและปรับระดับได้ ภายในแบ่งสัมภาระด้วยซิปเปิด-ปิดรอบด้าน พร้อมล็อกตั้งรหัส 3 หลัก มาตรฐานสากล เหมาะสำหรับการเดินทางทุกประเภท\r\n\r\nรายละเอียดสินค้าสร้างโดย AI ดูรายละเอียดสินค้าต้นฉบับ', 391.00, 7, 419, 5, 'uploads/product/Screenshot 2025-09-11 192614.png?v=1757593722', 1, 0, '2025-09-11 19:28:42', '2025-09-11 19:28:42', NULL, NULL, NULL, 60.00, 60.00, 60.00, 1200.00, 0),
(63, 24, 'MOEYE', 'โมอายกล่องใส่กระดาษทิชชู่แบบตั้งโชว์สไตล์มินิมอลตกแต่งบ้านและใช้งานใส่หูฟังได้', 'กล่องใส่ทิชชู่ ที่ใส่กระดาษทิชชู่โมเดล โมอาย กล่องทิชชู่ กล่องใส่กระดาษทิชชู่ กล่องใส่ทิชชู่ กล่องทิชชู่มินิมอล\r\n\r\n\r\n\r\n**ไม่มีฝาใต้โมอาย วิดีโอและภาพใช้เพื่อประกอบสินค้าเท่านั้น ดูสินค้าจริงจากรีวิว หรือทักแชทหาร้านได้เลย***  \r\n\r\n\r\n\r\nสินค้าลดราคา จะมีรอยพิมพ์บ้างเล็กน้อยและมีฟองอากาศ พิจารณาก่อนสั่งซื้อนะคะ --สั่งแล้วไม่รับคืน--\r\n\r\n\r\n\r\n\r\n\r\n[พร้อมส่งจากกรุงเทพ] กล่องใส่กระดาษทิชชู่รูปโมอาย ที่ใส่ทิชชู่ โมเดลโมอาย โมอาย\r\n\r\nมีให้เลือก 3สี\r\n\r\n1.สีดำ\r\n\r\n2.สีขาว\r\n\r\n3.สีเทา\r\n\r\nขนาด สูง25cm กว้าง 16.5cm ยาว18cm\r\n\r\n\r\n\r\nวัสดุ\r\n\r\n-เรซิ่นผสมปาสเตอร์\r\n\r\n-งานพ่นสี\r\n\r\n-งานแฮนด์เมด\r\n\r\n-ไม่มีฝาปิด\r\n\r\n\r\n\r\nวิธีการใช้งาน\r\n\r\n-เทียบขนาดทิชชู่ก่อนใช้งานจริง\r\n\r\n-ใส่กระดาษทิชชู่ใต้โมอาย\r\n\r\n-ใช้ตั้งโชว์เพื่อความสวยงาม\r\n\r\n-เป็นของขวัญสุดพิเศษ\r\n\r\n-ใช้ตกแต่งบ้าน\r\n\r\n-ใส่ได้เฉพาะทิชชู่ขนาดเล็กเท่านั้น หรือขนาดสี่เหลี่ยมจตุรัสและแบบม้วนเล็ก หรือวัดขนาดก่อนใช้งาน**\r\n\r\n\r\n\r\n🚚 ส่งสินค้า จันทร์-เสาร์ \r\n\r\nร้านหยุด วันอาทิตย์  หากลูกค้ามีคำสั่งซื้อในวันเสาร์ จะถูกจัดส่งวันจันทร์\r\n\r\nทางร้านตัดรอบบิล19:00pm หากสั่งหลังจากนี้จะถูกส่งในวันถัดไป \r\n\r\n 🛑เช่น สั่งวันจันทร์ 19:01 จะถูกจัดส่งอีกทีในวันพุธ 🛑\r\n\r\n**กรณีสินค้าจัดโปรโมชั่นลดราคา หรือช่วงเทศกาลเช่น11.11 ทางร้านจะจัดส่งช้า 5-7 วัน\r\n\r\n\r\n\r\n📲 แอดมินตอบแชทช้า ลูกค้าที่ทักเข้ามาแล้ว กรุณารออย่างใจเย็นนะคะ', 176.00, 7, 189, 5, 'uploads/product/Screenshot 2025-09-11 192950.png?v=1757593882', 1, 0, '2025-09-11 19:31:22', '2025-09-11 19:31:22', NULL, NULL, NULL, 30.00, 30.00, 30.00, 200.00, 0),
(64, 8, 'MA', 'ม้า100', 'ม้า', 100.00, 7, 107, 5, 'uploads/product/u1f493_u1f40e.png?v=1761029674', 1, 0, '2025-10-21 13:54:34', '2025-10-21 14:01:37', NULL, NULL, NULL, NULL, NULL, NULL, 30.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_type`
--

CREATE TABLE `product_type` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `other` tinyint(1) DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_type`
--

INSERT INTO `product_type` (`id`, `name`, `description`, `other`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, 'เครื่องเล่นaaa', 'เหล่าเครื่องเล่นต่าง ๆ มากมายย', 0, 1, 0, '2025-07-16 11:21:36', '2025-09-12 11:39:31'),
(2, 'ของเล่น', 'ของเล่นต่าง ๆ มากมาย', 0, 1, 0, '2025-07-16 11:32:02', '2025-07-16 16:03:50'),
(3, 'หนังสือ', 'หนังสือต่าง ๆ มากมาย', 0, 1, 0, '2025-07-16 13:03:25', '2025-07-16 16:06:23'),
(4, 'สื่อ', 'รวบรวมสื่อต่าง ๆ มากมาย', 0, 1, 0, '2025-07-16 14:01:10', '2025-07-16 16:06:56'),
(5, 'ชุดประเมิน', 'ชุดประเมินความรู้ทั้งหลาย', 0, 1, 0, '2025-07-16 16:07:20', '2025-07-16 16:07:20'),
(6, 'อื่น ๆ', 'อื่น ๆ', 1, 1, 0, '2025-07-16 16:07:33', '2025-08-25 10:40:05'),
(7, 'ทดสอบระบบ', 'ทดสอบระบบ', 0, 1, 0, '2025-07-17 13:38:04', '2025-07-17 13:38:04'),
(8, 'ทดสอบระบบ/', 'ทดสอบระบบ/', 0, 1, 1, '2025-07-17 13:38:16', '2025-09-05 16:06:31'),
(9, 'ทดสอบระบบ-', 'ทดสอบระบบ-', 0, 1, 1, '2025-07-17 13:38:26', '2025-09-05 16:05:19'),
(10, 'ทดสอบระบบภ', 'ทดสอบระบบภ', 0, 1, 1, '2025-07-17 13:38:41', '2025-09-05 15:47:14'),
(11, 'ทดสอบลบ', 'nnnnnn', 1, 1, 1, '2025-07-22 09:44:31', '2025-09-05 15:47:32'),
(12, 'สมุด', '', 0, 1, 0, '2025-07-22 18:37:35', '2025-07-22 18:37:35'),
(13, 'ทดสอบ', 'ทดสอบ', 0, 1, 1, '2025-07-30 15:06:32', '2025-09-05 16:03:47'),
(14, '1', '1', 0, 1, 1, '2025-08-28 18:23:22', '2025-08-29 09:44:33'),
(15, 'ล้อยาง', 'ล้อยาง', 0, 1, 0, '2025-08-28 18:47:37', '2025-08-28 18:47:37'),
(16, 'sadasd;laskdlaksdasdsdd', 'sdas;dlkasdlaks&#039;;laksdsadasdasdsdasd', 1, 1, 1, '2025-09-05 15:48:27', '2025-09-05 15:51:09'),
(17, 'adad', 'adadadad', 1, 1, 1, '2025-09-05 16:12:08', '2025-09-05 16:12:13'),
(18, '1', '1', 0, 1, 1, '2025-09-11 11:28:25', '2025-09-11 11:28:30'),
(19, 'ทดสอบ', '', 0, 1, 1, '2025-09-12 11:39:51', '2025-09-12 11:39:59'),
(20, 'ทดสอบ', 'ทดสอบ', 0, 1, 0, '2025-10-15 09:38:44', '2025-10-15 09:38:44'),
(21, 'ทดสอบ1', 'ทดสอบ', 0, 1, 0, '2025-10-15 09:38:52', '2025-10-15 09:38:52'),
(22, 'ทดสอบ2', 'ทดสอบ', 0, 1, 0, '2025-10-15 09:38:59', '2025-10-15 09:38:59'),
(23, 'ทดสอบ3', 'ทดสอบ', 0, 1, 0, '2025-10-15 09:39:07', '2025-10-15 09:39:07'),
(24, '1', '1', 0, 1, 0, '2025-10-20 10:47:40', '2025-10-20 10:47:40');

-- --------------------------------------------------------

--
-- Table structure for table `promotions_list`
--

CREATE TABLE `promotions_list` (
  `id` int NOT NULL,
  `promotion_category_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` enum('fixed','percent','free_shipping','code') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'fixed',
  `discount_value` float DEFAULT '0',
  `minimum_order` float DEFAULT '0',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotions_list`
--

INSERT INTO `promotions_list` (`id`, `promotion_category_id`, `name`, `description`, `image_path`, `type`, `discount_value`, `minimum_order`, `start_date`, `end_date`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(10, 10, 'โปรโมชั่น 8.8 !', 'พบกับส่วนลดพิเศษทั้งร้านค้าต้อนรับ 8.8 ! ลดหนัก ลดกันไปเลย 30 % !', 'uploads/promotions/promo_689db31d6e8fb_1755165469.png?v=1755165469', 'percent', 30, 300, '2025-08-06 09:18:00', '2025-10-31 09:18:00', 1, 0, '2025-08-06 09:18:27', '2025-10-10 14:03:14'),
(11, 10, 'ลดล้างสต๊อก', 'ลดล้างสต๊อก\r\nลดล้างสต๊อกลดล้างสต๊อกลดล้างสต๊อก', 'uploads/promotions/promo_689daed0bca7e_1755164368.png?v=1755164368', 'percent', 90, 0, '2025-08-06 16:01:00', '2025-08-07 16:01:00', 0, 0, '2025-08-06 16:01:33', '2025-08-27 11:31:27'),
(12, 10, 'ส่งฟรีไม่มีขั้นต่ำ', 'ส่งฟรีไม่มีขั้นต่ำ', 'uploads/promotions/promo_689db326cfea5_1755165478.png?v=1755165478', 'free_shipping', 0, 0, '2025-08-07 13:56:00', '2025-08-21 13:56:00', 0, 0, '2025-08-07 13:56:10', '2025-08-27 11:31:27'),
(13, 10, 'ลดราคา 100 บาท', 'สั่งซื้อครบ 20 บาท ลดเลย 100 บาท !', 'uploads/promotions/promo_689db32dcf831_1755165485.png?v=1755165485', 'fixed', 100, 20, '2025-08-08 09:31:00', '2025-08-22 09:31:00', 0, 0, '2025-08-08 09:31:56', '2025-08-27 11:31:27'),
(14, 10, 'ส่งฟรีขั้นต่ำ 200 บาท', 'ส่งฟรีขั้นต่ำ 200 บาท', 'uploads/promotions/promo_689db33b5db62_1755165499.png?v=1755165499', 'free_shipping', 0, 200, '2025-08-08 09:49:00', '2025-08-22 09:49:00', 0, 0, '2025-08-08 09:49:49', '2025-08-27 11:31:27'),
(15, 10, 'ลดหนังสือนิทาน การ์ตูน ฯลฯ ทั้งร้าน !', 'ลดหนังสือนิทาน การ์ตูน มังงะ ทั้งร้าน ! ลดหนัก จัดหนักกันไปเลย ! ลดถึง 20 % !', 'uploads/promotions/promo_689db2eceddb6_1755165420.png?v=1755165420', 'percent', 20, 60, '2025-08-14 16:46:00', '2025-08-30 16:46:00', 0, 0, '2025-08-14 16:46:56', '2025-09-02 09:02:14'),
(16, 10, 'ลดเลยทันที 40% !!!', 'ลดหนัก ๆ จัดกันจุก ๆ ลดทันที 40% !!!', 'uploads/promotions/promo_68a3ebac39e7f_1755573164.png?v=1755573164', 'percent', 40, 0, '2025-08-19 10:12:00', '2025-10-01 10:12:00', 0, 0, '2025-08-19 10:12:44', '2025-10-01 10:12:14'),
(17, 11, 'NEWYEAR', 'NEWYEAR', 'uploads/promotions/promo_68b03dfa5a685_1756380666.jpg?v=1756380666', 'free_shipping', 0, 0, '2025-08-28 18:31:00', '2025-08-29 18:31:00', 0, 0, '2025-08-28 18:31:06', '2025-08-29 18:31:14'),
(18, 12, 'วันเด็กแสนซน', 'วันเด็กแสนซน ส่งฟรีทุกสินค้า', 'uploads/promotions/promo_68b0432ee5c92_1756381998.png?v=1756381999', 'free_shipping', 0, 0, '2025-08-28 18:53:00', '2025-08-29 18:53:00', 0, 0, '2025-08-28 18:53:19', '2025-08-29 18:53:14'),
(20, 21, 'สินค้าใหม่', 'สินค้าใหม่', 'uploads/promotions/promo_68cd0b9590957_1758268309.png?v=1758268310', 'free_shipping', 0, 0, '2025-10-10 14:51:00', '2025-10-31 14:51:00', 1, 0, '2025-09-19 14:51:50', '2025-10-10 15:03:19'),
(21, 21, 'ทดสอบ', '', 'uploads/promotions/promo_68e8baef9648b_1760082671.png?v=1760082672', 'fixed', 500, 500, '2025-10-10 14:50:00', '2025-10-31 14:50:00', 1, 0, '2025-10-10 14:51:12', '2025-10-10 14:51:12');

-- --------------------------------------------------------

--
-- Table structure for table `promotion_category`
--

CREATE TABLE `promotion_category` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotion_category`
--

INSERT INTO `promotion_category` (`id`, `name`, `description`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(2, 'ทดสอบโปรโมชั่น', 'ทดสอบ', 1, 1, '2025-07-30 08:13:12', '2025-09-11 06:49:21'),
(3, 'ทดสอบ2', '', 1, 1, '2025-08-01 02:10:13', '2025-09-05 10:03:25'),
(4, 'ทดสอบ3', '', 1, 1, '2025-08-01 04:29:50', '2025-09-11 03:37:10'),
(5, 'ทดสอบ4', 'ทดสอบ4', 1, 1, '2025-08-01 04:30:01', '2025-09-11 06:49:18'),
(6, 'ทดสอบ5', 'ทดสอบ5', 1, 1, '2025-08-01 04:31:20', '2025-09-11 06:49:24'),
(7, 'ทดสอบ6', 'ทดสอบ6', 1, 1, '2025-08-01 04:36:29', '2025-09-11 06:49:26'),
(8, 'ทดสอบ7', 'ทดสอบ7', 1, 1, '2025-08-01 04:36:35', '2025-09-11 06:49:29'),
(9, 'ทดสอบ8', 'ทดสอบ8', 1, 1, '2025-08-01 04:36:44', '2025-09-11 06:49:31'),
(10, 'โปรโมชั่นยอดฮิท !', 'โปรโมชั่นยอดฮิท!', 1, 0, '2025-08-06 02:17:40', '2025-08-06 02:17:40'),
(11, 'NEWYEAR', 'NEWYEAR', 1, 0, '2025-08-28 11:29:57', '2025-08-28 11:29:57'),
(12, 'วันเด็ก', '', 1, 0, '2025-08-28 11:51:53', '2025-08-28 11:51:53'),
(13, 'ada', 'dadad', 1, 1, '2025-09-05 09:57:37', '2025-09-11 06:48:54'),
(14, 'กฟ', 'กฟกฟก', 1, 1, '2025-09-05 10:04:17', '2025-09-11 06:48:57'),
(15, '1', '1', 0, 1, '2025-09-11 04:29:04', '2025-09-11 06:49:00'),
(16, '2', '3', 0, 1, '2025-09-11 04:30:26', '2025-09-11 06:49:03'),
(17, '/', '/', 0, 1, '2025-09-11 04:38:57', '2025-09-11 06:49:05'),
(18, 'ฟหก', 'ฟหก', 0, 1, '2025-09-11 04:44:23', '2025-09-11 06:49:09'),
(19, 'ห', 'ห', 0, 1, '2025-09-11 04:44:32', '2025-09-11 06:49:11'),
(20, 'ๅ', 'ๅ', 0, 1, '2025-09-11 05:57:46', '2025-09-11 06:49:14'),
(21, 'สินค้าใหม่', '', 1, 0, '2025-09-19 04:44:33', '2025-10-10 07:50:16'),
(22, 'ทดสอบ', '', 1, 0, '2025-10-10 07:50:32', '2025-10-10 07:50:32');

-- --------------------------------------------------------

--
-- Table structure for table `promotion_products`
--

CREATE TABLE `promotion_products` (
  `id` int NOT NULL,
  `promotion_id` int NOT NULL,
  `product_id` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotion_products`
--

INSERT INTO `promotion_products` (`id`, `promotion_id`, `product_id`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(40, 12, 29, 1, 0, '2025-08-07 13:56:49', '2025-08-07 13:56:49'),
(50, 13, 27, 1, 0, '2025-08-08 09:32:39', '2025-08-08 09:32:39'),
(51, 13, 14, 1, 0, '2025-08-08 09:32:39', '2025-08-08 09:32:39'),
(52, 14, 30, 1, 0, '2025-08-08 09:50:05', '2025-08-08 09:50:05'),
(53, 14, 25, 1, 0, '2025-08-08 09:50:05', '2025-08-08 09:50:05'),
(57, 16, 41, 1, 0, '2025-08-26 10:50:47', '2025-08-26 10:50:47'),
(58, 16, 38, 1, 0, '2025-08-26 10:50:47', '2025-08-26 10:50:47'),
(59, 16, 39, 1, 0, '2025-08-26 10:50:47', '2025-08-26 10:50:47'),
(60, 16, 36, 1, 0, '2025-08-26 10:50:47', '2025-08-26 10:50:47'),
(61, 16, 37, 1, 0, '2025-08-26 10:50:47', '2025-08-26 10:50:47'),
(62, 16, 40, 1, 0, '2025-08-26 10:50:47', '2025-08-26 10:50:47'),
(64, 18, 43, 1, 0, '2025-08-28 18:56:02', '2025-08-28 18:56:02'),
(72, 20, 61, 1, 0, '2025-09-19 14:56:32', '2025-09-19 14:56:32'),
(73, 20, 60, 1, 0, '2025-09-19 14:56:32', '2025-09-19 14:56:32'),
(74, 20, 62, 1, 0, '2025-09-19 14:56:32', '2025-09-19 14:56:32'),
(76, 20, 63, 1, 0, '2025-09-19 14:56:32', '2025-09-19 14:56:32'),
(84, 10, 23, 1, 0, '2025-10-20 11:02:55', '2025-10-20 11:02:55'),
(85, 10, 22, 1, 0, '2025-10-20 11:02:55', '2025-10-20 11:02:55'),
(86, 10, 21, 1, 0, '2025-10-20 11:02:55', '2025-10-20 11:02:55'),
(87, 10, 24, 1, 0, '2025-10-20 11:02:55', '2025-10-20 11:02:55'),
(88, 10, 20, 1, 0, '2025-10-20 11:02:55', '2025-10-20 11:02:55'),
(89, 10, 31, 1, 0, '2025-10-20 11:02:55', '2025-10-20 11:02:55'),
(90, 10, 33, 1, 0, '2025-10-20 11:02:55', '2025-10-20 11:02:55');

-- --------------------------------------------------------

--
-- Table structure for table `promotion_summaries`
--

CREATE TABLE `promotion_summaries` (
  `promotion_id` int NOT NULL,
  `used_by_count` int DEFAULT '0',
  `usage_count` int DEFAULT '0',
  `ordered_items_count` int DEFAULT '0',
  `total_discount` decimal(15,2) DEFAULT '0.00',
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotion_usage_logs`
--

CREATE TABLE `promotion_usage_logs` (
  `id` bigint NOT NULL,
  `promotion_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `order_id` int NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `items_in_order` int NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotion_usage_logs`
--

INSERT INTO `promotion_usage_logs` (`id`, `promotion_id`, `customer_id`, `order_id`, `discount_amount`, `items_in_order`, `used_at`) VALUES
(1, 20, 19, 19, 40.00, 1, '2025-10-16 06:53:45');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_methods`
--

CREATE TABLE `shipping_methods` (
  `id` int NOT NULL,
  `provider_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `cost` decimal(10,2) DEFAULT '0.00',
  `cod_enabled` tinyint(1) DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  `delete_flag` tinyint(1) DEFAULT '0',
  `volumetric_divider` int DEFAULT '5000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_methods`
--

INSERT INTO `shipping_methods` (`id`, `provider_id`, `name`, `description`, `cost`, `cod_enabled`, `status`, `delete_flag`, `volumetric_divider`) VALUES
(3, 2, 'Flash Express', 'บริการราคาประหยัดทั่วประเทศ', 40.00, 1, 1, 0, 5000),
(7, 3, 'ไปรษณีย์ไทย', 'สามารถเก็บเงินปลายทางได้', 40.00, 1, 1, 0, 5000),
(40, 4, 'JT', 'JT', 100.00, 1, 1, 0, 5000),
(41, 1, 'Kerry', 'Kerry', 40.00, 1, 1, 0, 5000);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_prices`
--

CREATE TABLE `shipping_prices` (
  `id` int NOT NULL,
  `shipping_methods_id` int NOT NULL,
  `min_weight` int NOT NULL,
  `max_weight` int NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_prices`
--

INSERT INTO `shipping_prices` (`id`, `shipping_methods_id`, `min_weight`, `max_weight`, `price`) VALUES
(1, 3, 0, 1000, 40.00),
(2, 3, 1001, 2000, 50.00),
(3, 3, 2001, 3000, 65.00),
(4, 3, 3001, 4000, 75.00),
(5, 3, 4001, 5000, 90.00),
(6, 3, 5001, 6000, 100.00),
(7, 3, 6001, 7000, 120.00),
(8, 3, 7001, 8000, 135.00),
(9, 3, 8001, 9000, 140.00),
(10, 3, 9001, 10000, 150.00),
(11, 3, 10001, 11000, 160.00),
(12, 3, 11001, 12000, 170.00),
(13, 3, 12001, 13000, 180.00),
(14, 3, 13001, 14000, 190.00),
(15, 3, 14001, 15000, 200.00),
(16, 3, 15001, 16000, 210.00),
(17, 3, 16001, 17000, 220.00),
(18, 3, 17001, 18000, 230.00),
(19, 3, 18001, 19000, 240.00),
(20, 3, 19001, 20000, 250.00),
(21, 3, 20001, 25000, 400.00),
(64, 7, 0, 1000, 40.00),
(65, 7, 1001, 2000, 50.00),
(66, 7, 2001, 3000, 65.00),
(67, 7, 3001, 4000, 75.00),
(68, 7, 4001, 5000, 90.00),
(69, 7, 5001, 6000, 100.00),
(70, 7, 6001, 7000, 120.00),
(71, 7, 7001, 8000, 135.00),
(72, 7, 8001, 9000, 140.00),
(73, 7, 9001, 10000, 150.00),
(74, 7, 10001, 11000, 160.00),
(75, 7, 11001, 12000, 170.00),
(76, 7, 12001, 13000, 180.00),
(77, 7, 13001, 14000, 190.00),
(78, 7, 14001, 15000, 200.00),
(79, 7, 15001, 16000, 210.00),
(80, 7, 16001, 17000, 220.00),
(81, 7, 17001, 18000, 230.00),
(82, 7, 18001, 19000, 240.00),
(83, 7, 19001, 20000, 250.00),
(84, 7, 20001, 25000, 400.00),
(126, 40, 0, 1000, 100.00),
(127, 40, 1001, 2000, 150.00),
(130, 41, 0, 1000, 40.00),
(131, 41, 1001, 2000, 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_providers`
--

CREATE TABLE `shipping_providers` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_providers`
--

INSERT INTO `shipping_providers` (`id`, `name`, `description`, `logo`, `status`) VALUES
(1, 'Kerry Express', 'บริการด่วนพิเศษ 1-2 วัน', NULL, 1),
(2, 'Flash Express', 'บริการราคาประหยัดทั่วประเทศ', NULL, 1),
(3, 'ไปรษณีย์ไทย', 'บริการ EMS, ลงทะเบียน, พัสดุ', NULL, 1),
(4, 'J&T Express', 'บริการด่วนต้นทุนต่ำ', NULL, 1),
(5, 'SCG Express', 'เหมาะสำหรับของเย็นหรือชิ้นใหญ่', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `stock_list`
--

CREATE TABLE `stock_list` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `sku` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` float(12,2) NOT NULL DEFAULT '0.00',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_list`
--

INSERT INTO `stock_list` (`id`, `product_id`, `sku`, `quantity`, `date_created`, `date_updated`) VALUES
(1, 38, 'AUV1001', 12000.00, '2025-09-15 14:13:32', '2025-09-15 14:20:41'),
(2, 41, 'SN0001', 1000.00, '2025-09-15 15:00:06', '2025-09-15 15:00:06'),
(3, 27, 'BASSPRO-POWER-20001', 1000.00, '2025-09-15 15:00:26', '2025-09-15 15:00:26'),
(4, 14, 'GALAX1001', 1000.00, '2025-09-15 15:00:39', '2025-09-15 15:00:39'),
(5, 25, 'XKT-02-BT0001', 1000.00, '2025-09-15 15:01:01', '2025-09-15 15:01:01'),
(6, 23, 'SSMG101', 1000.00, '2025-09-15 15:01:11', '2025-09-15 15:01:11'),
(8, 39, 'CIVAGO1001', 1000.00, '2025-09-15 15:01:25', '2025-09-15 15:01:25'),
(9, 22, 'FIIO101', 1000.00, '2025-09-15 15:01:35', '2025-09-15 15:01:35'),
(10, 21, 'IHCPUH101', 1000.00, '2025-09-15 15:01:42', '2025-09-15 15:01:42'),
(11, 60, 'KYSONA', 1000.00, '2025-09-15 15:01:50', '2025-09-15 15:01:50'),
(12, 62, 'Lemona', 1000.00, '2025-09-15 15:01:57', '2025-09-15 15:01:57'),
(13, 24, 'LMT101', 1000.00, '2025-09-15 15:02:06', '2025-09-15 15:02:06'),
(14, 20, 'MADCATZ-MAD-60-68-HE1001', 1000.00, '2025-09-15 15:02:14', '2025-09-15 15:02:14'),
(15, 31, 'MINE0001', 1000.00, '2025-09-15 15:02:26', '2025-09-15 15:02:26'),
(16, 63, 'MOEYE0001', 1000.00, '2025-09-15 15:02:36', '2025-09-15 15:02:36'),
(17, 36, 'SNZV-E10001', 1000.00, '2025-09-15 15:02:45', '2025-09-15 15:02:45'),
(18, 33, 'SF-9-140001', 1000.00, '2025-09-15 15:03:02', '2025-09-15 15:03:02'),
(19, 37, 'TR010001', 10.00, '2025-09-15 15:03:11', '2025-10-21 13:51:37'),
(20, 34, 'NTE-001', 1000.00, '2025-09-15 15:03:28', '2025-09-15 15:03:28'),
(27, 61, 'AC1001', 53.00, '2025-10-21 09:27:16', '2025-10-21 09:27:32'),
(29, 64, 'MA-S', 13.00, '2025-10-21 13:54:47', '2025-10-21 14:31:50'),
(30, 40, 'sw001', 50.00, '2025-10-21 13:56:41', '2025-10-21 14:32:48'),
(31, 64, 'MA-S', 20.00, '2025-10-21 14:32:05', '2025-10-21 14:32:12');

-- --------------------------------------------------------

--
-- Table structure for table `stock_out`
--

CREATE TABLE `stock_out` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `stock_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_out`
--

INSERT INTO `stock_out` (`id`, `order_id`, `stock_id`, `quantity`, `date_created`) VALUES
(1, 1, 2, 3, '2025-09-15 16:00:36'),
(2, 2, 13, 4, '2025-09-26 09:16:01'),
(3, 3, 11, 5, '2025-09-26 09:24:29'),
(4, 4, 18, 1, '2025-09-26 09:27:09'),
(5, 5, 17, 1, '2025-09-26 09:29:59'),
(6, 6, 9, 1, '2025-09-26 09:33:00'),
(7, 7, 4, 1, '2025-10-01 09:00:46'),
(9, 9, 6, 1, '2025-10-06 09:02:51'),
(10, 10, 6, 1, '2025-10-06 09:06:31'),
(11, 11, 18, 1, '2025-10-06 09:18:29'),
(12, 12, 4, 1, '2025-10-06 09:29:52'),
(13, 13, 5, 1, '2025-10-06 09:33:07'),
(15, 15, 18, 2, '2025-10-06 14:59:05'),
(18, 18, 4, 2, '2025-10-15 15:16:39'),
(19, 18, 18, 3, '2025-10-15 15:16:39'),
(20, 18, 17, 1, '2025-10-15 15:16:39'),
(21, 19, 11, 1, '2025-10-16 13:53:45'),
(23, 21, 29, 13, '2025-10-21 13:55:20');

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int NOT NULL,
  `meta_field` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `meta_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Media Shopping Group'),
(6, 'short_name', ''),
(11, 'logo', 'uploads/logo.png?v=1747713664'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/cover.png?v=1758256902'),
(17, 'phone', '075-051070'),
(18, 'mobile', '0828398430 / 0945632122'),
(19, 'email', 'AdminMSG@gmail.com'),
(20, 'address', '21 ม.5 ถ.เพชรเกษม ต.นาท่ามเหนือ อ.เมือง จ.ตรัง 92190'),
(21, 'office_hours', 'วันจันทร์ - วันเสาร์ เวลา 08.30 น. - 17.00 น. (หยุดวันนักขัตฤกษ์)'),
(22, 'Line', 'https://line.me'),
(23, 'Facebook', 'https://fb.com'),
(24, 'TikTok', 'https://tiktok.com'),
(25, 'Synopsis', 'เรายินดีรับฟังทุกความคิดเห็น ข้อเสนอแนะ หรือคำถามของคุณ</br>\r\nหากคุณต้องการติดต่อกับเรา กรุณาใช้ข้อมูลด้านล่าง หรือติดต่อผ่านช่องทางโซเชียลมีเดีย');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `firstname` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `middlename` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `lastname` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `avatar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='2';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `middlename`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`) VALUES
(1, 'Admin', '4', 'Administrator', 'admin', '$2y$10$3AGnyajq0J3ui0/DIlsMd.7mk3napfyKKnxug8UL38Abz3lWociq2', 'uploads/avatars/1.png', '2025-10-21 13:55:53', 1, '2021-01-20 14:02:37', '2025-10-21 13:55:53'),
(8, 'Claire', '', 'Blake', 'cblake', 'cd74fae0a3adf459f73bbf187607ccea', 'uploads/avatars/8.png?v=1675047323', NULL, 2, '2023-01-30 10:55:23', '2023-01-30 10:55:23'),
(9, 'Staff', '', 'Staff', 'staff1', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/avatars/9.png?v=1757391282', NULL, 2, '2025-05-19 16:21:57', '2025-09-17 11:56:43'),
(10, 'Staffฟๆ', '', '2', 'staff2', '$2y$10$dkGh3arZQPGctYHUAmJ.VOSEd2BzAoUL6EKLIx3REijCBeQhEWWp2', 'uploads/avatars/10.png', '2025-09-17 13:18:30', 2, '2025-09-09 11:39:26', '2025-09-17 13:18:30'),
(11, 'Staff', '', 'Yellow', 'staff', '$2y$10$VaxawPZ9HmrvLuuBr/yzwOnSZ8opRXyM10zoICEe26p94Q9TdSfPS', 'uploads/avatars/11.png', '2025-10-10 09:16:13', 2, '2025-09-17 11:59:39', '2025-10-10 09:16:13'),
(12, 'Admin', '', 'Teng', 'adminTeng', '$2y$10$yfpbHcpfPOHicaA8qC8lS.60mJnfabN0oiuAwDFVPBAQLvvfLP/zq', 'uploads/avatars/12.png', '2025-09-19 11:26:32', 1, '2025-09-19 11:25:27', '2025-09-19 11:27:09'),
(13, 'Admin', 'a', 'Moeyes', 'adminmoeye', '$2y$10$rqhcKidEIy3A0WKRHF98eeUBFgyXXWarinMcZ2osmsyMxsEaK7wPu', 'uploads/avatars/13.png', NULL, 1, '2025-09-19 11:28:36', '2025-10-06 10:53:50'),
(14, 'Staff', '', 'Main', 'staff0', '$2y$10$FFPCoIsQ8uBARGoT4A5b7.ghoj0BTpQaTE9cu14hkS55cCoAl4jka', 'uploads/avatars/14.png', '2025-10-01 11:09:13', 2, '2025-10-01 11:08:50', '2025-10-01 11:09:13'),
(15, 'staff', '1', 'test', 'stafftest1', '$2y$10$sdw3ENxCahgDuQAGWKhhOeCs9u9PPL/uY4pmM6oJqQUdanYCJX4k6', 'uploads/avatars/15.png', '2025-10-06 11:58:00', 2, '2025-10-06 11:42:09', '2025-10-06 11:58:00'),
(16, 'Admin', '2', 'Test', 'admintest2', '$2y$10$z6VTRlJtRB9bPyhehGq8hewmjjAmBqsc8xVxatiLDnIuDtF7cWjWu', 'uploads/avatars/16.png', NULL, 1, '2025-10-06 11:43:06', '2025-10-06 11:43:06'),
(17, 'Admin', 'test', '3', 'admintest3', '$2y$10$n1ViA/3vGqeMRh2jenFpS.iAm6vhtpS1m0uKCSE2W.vJRKP9nrhXq', 'uploads/avatars/17.png', '2025-10-06 11:57:09', 1, '2025-10-06 11:56:21', '2025-10-06 11:57:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_list`
--
ALTER TABLE `cart_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `category_list`
--
ALTER TABLE `category_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_product_type` (`product_type_id`);

--
-- Indexes for table `coupon_code_list`
--
ALTER TABLE `coupon_code_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupon_code` (`coupon_code`);

--
-- Indexes for table `coupon_code_products`
--
ALTER TABLE `coupon_code_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coupon_code_id` (`coupon_code_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `coupon_code_usage_logs`
--
ALTER TABLE `coupon_code_usage_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coupon_code_id` (`coupon_code_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `customer_list`
--
ALTER TABLE `customer_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_promotion_id` (`promotion_id`),
  ADD KEY `fk_coupon_code_id` (`coupon_code_id`);

--
-- Indexes for table `order_list`
--
ALTER TABLE `order_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `fk_shipping_methods` (`shipping_methods_id`),
  ADD KEY `fk_promotions_list` (`promotion_id`),
  ADD KEY `fk_coupon_code_list` (`coupon_code_id`),
  ADD KEY `fk_shipping_prices` (`shipping_prices_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `product_image_path`
--
ALTER TABLE `product_image_path`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_links`
--
ALTER TABLE `product_links`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_list`
--
ALTER TABLE `product_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_type`
--
ALTER TABLE `product_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promotions_list`
--
ALTER TABLE `promotions_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_promotion_category_id` (`promotion_category_id`);

--
-- Indexes for table `promotion_category`
--
ALTER TABLE `promotion_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promotion_products`
--
ALTER TABLE `promotion_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotion_id` (`promotion_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `promotion_summaries`
--
ALTER TABLE `promotion_summaries`
  ADD PRIMARY KEY (`promotion_id`);

--
-- Indexes for table `promotion_usage_logs`
--
ALTER TABLE `promotion_usage_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotion_id` (`promotion_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `shipping_prices`
--
ALTER TABLE `shipping_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shipping_method_id` (`shipping_methods_id`);

--
-- Indexes for table `shipping_providers`
--
ALTER TABLE `shipping_providers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_list`
--
ALTER TABLE `stock_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stock_out`
--
ALTER TABLE `stock_out`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `stock_id` (`stock_id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_list`
--
ALTER TABLE `cart_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `category_list`
--
ALTER TABLE `category_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `coupon_code_list`
--
ALTER TABLE `coupon_code_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `coupon_code_products`
--
ALTER TABLE `coupon_code_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `coupon_code_usage_logs`
--
ALTER TABLE `coupon_code_usage_logs`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `customer_list`
--
ALTER TABLE `customer_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `order_list`
--
ALTER TABLE `order_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `product_image_path`
--
ALTER TABLE `product_image_path`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `product_list`
--
ALTER TABLE `product_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `product_type`
--
ALTER TABLE `product_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `promotions_list`
--
ALTER TABLE `promotions_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `promotion_category`
--
ALTER TABLE `promotion_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `promotion_products`
--
ALTER TABLE `promotion_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `promotion_usage_logs`
--
ALTER TABLE `promotion_usage_logs`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `shipping_prices`
--
ALTER TABLE `shipping_prices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `shipping_providers`
--
ALTER TABLE `shipping_providers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stock_list`
--
ALTER TABLE `stock_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `stock_out`
--
ALTER TABLE `stock_out`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_list`
--
ALTER TABLE `cart_list`
  ADD CONSTRAINT `customer_id_fk_cl` FOREIGN KEY (`customer_id`) REFERENCES `customer_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_id_fk_cl` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `category_list`
--
ALTER TABLE `category_list`
  ADD CONSTRAINT `fk_category_product_type` FOREIGN KEY (`product_type_id`) REFERENCES `product_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `coupon_code_products`
--
ALTER TABLE `coupon_code_products`
  ADD CONSTRAINT `coupon_code_products_ibfk_1` FOREIGN KEY (`coupon_code_id`) REFERENCES `coupon_code_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_code_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coupon_code_usage_logs`
--
ALTER TABLE `coupon_code_usage_logs`
  ADD CONSTRAINT `coupon_code_usage_logs_ibfk_1` FOREIGN KEY (`coupon_code_id`) REFERENCES `coupon_code_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_code_usage_logs_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_code_usage_logs_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `order_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD CONSTRAINT `customer_addresses_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer_list` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_coupon_code_id` FOREIGN KEY (`coupon_code_id`) REFERENCES `coupon_code_list` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_promotion_id` FOREIGN KEY (`promotion_id`) REFERENCES `promotions_list` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `order_id_fk_oi` FOREIGN KEY (`order_id`) REFERENCES `order_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_id_fk_oi` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_list`
--
ALTER TABLE `order_list`
  ADD CONSTRAINT `fk_coupon_code_list` FOREIGN KEY (`coupon_code_id`) REFERENCES `coupon_code_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_customer_list` FOREIGN KEY (`customer_id`) REFERENCES `customer_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_promotions_list` FOREIGN KEY (`promotion_id`) REFERENCES `promotions_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_shipping_methods` FOREIGN KEY (`shipping_methods_id`) REFERENCES `shipping_methods` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_shipping_prices` FOREIGN KEY (`shipping_prices_id`) REFERENCES `shipping_prices` (`id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_image_path`
--
ALTER TABLE `product_image_path`
  ADD CONSTRAINT `product_image_path_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`);

--
-- Constraints for table `product_links`
--
ALTER TABLE `product_links`
  ADD CONSTRAINT `product_links_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_list`
--
ALTER TABLE `product_list`
  ADD CONSTRAINT `category_id_fk_pl` FOREIGN KEY (`category_id`) REFERENCES `category_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promotions_list`
--
ALTER TABLE `promotions_list`
  ADD CONSTRAINT `fk_promotion_category_id` FOREIGN KEY (`promotion_category_id`) REFERENCES `promotion_category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `promotion_products`
--
ALTER TABLE `promotion_products`
  ADD CONSTRAINT `promotion_products_ibfk_1` FOREIGN KEY (`promotion_id`) REFERENCES `promotions_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promotion_summaries`
--
ALTER TABLE `promotion_summaries`
  ADD CONSTRAINT `promotion_summaries_ibfk_1` FOREIGN KEY (`promotion_id`) REFERENCES `promotions_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promotion_usage_logs`
--
ALTER TABLE `promotion_usage_logs`
  ADD CONSTRAINT `promotion_usage_logs_ibfk_1` FOREIGN KEY (`promotion_id`) REFERENCES `promotions_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_usage_logs_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_usage_logs_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `order_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  ADD CONSTRAINT `shipping_methods_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `shipping_providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shipping_prices`
--
ALTER TABLE `shipping_prices`
  ADD CONSTRAINT `shipping_prices_ibfk_1` FOREIGN KEY (`shipping_methods_id`) REFERENCES `shipping_methods` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_list`
--
ALTER TABLE `stock_list`
  ADD CONSTRAINT `product_id_fk_sl` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_out`
--
ALTER TABLE `stock_out`
  ADD CONSTRAINT `order_id_fk_so` FOREIGN KEY (`order_id`) REFERENCES `order_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_id_fk_so` FOREIGN KEY (`stock_id`) REFERENCES `stock_list` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `auto_update_promotion_status` ON SCHEDULE EVERY 1 MINUTE STARTS '2025-08-27 11:34:14' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE promotions_list
SET status = CASE
    WHEN start_date <= NOW() AND end_date >= NOW() THEN 1
    ELSE 0
END$$

CREATE DEFINER=`root`@`localhost` EVENT `auto_update_coupon_code_status` ON SCHEDULE EVERY 1 MINUTE STARTS '2025-08-28 09:22:50' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE coupon_code_list
SET status = CASE
    WHEN start_date <= NOW() AND end_date >= NOW() AND delete_flag = 0 THEN 1
    ELSE 0
END
WHERE delete_flag = 0$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
