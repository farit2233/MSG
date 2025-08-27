-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 27, 2025 at 04:38 AM
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
(187, 19, 25, 2),
(240, 19, 29, 2),
(250, 19, 23, 2),
(255, 19, 22, 2),
(256, 19, 24, 2),
(265, 20, 35, 2),
(266, 18, 35, 1),
(276, 19, 21, 1),
(284, 20, 30, 2),
(290, 19, 39, 3);

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
(5, 11, 'เทส', 'เทส', 0, 1, 0, '2025-06-12 09:57:50', '2025-08-01 09:31:22'),
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
(25, 11, 'ทดสอบลบ', '', 0, 1, 0, '2025-07-22 10:00:11', '2025-07-22 10:00:11'),
(26, 12, 'สมุดโน็ต', '', 0, 1, 0, '2025-07-22 18:38:08', '2025-07-22 18:38:08');

-- --------------------------------------------------------

--
-- Table structure for table `coupon_code_customer`
--

CREATE TABLE `coupon_code_customer` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `coupon_code_id` int NOT NULL,
  `delete_flag` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'TEST-01', 'สั่งซื้อลดไปเลย 20%!!!', 'ลดกันไปเลยจุก ๆ ไม่มีขั้นต่ำ ลดถึง 20%!!! ใช้ได้ไม่จำกัดครั้ง!!!', 'percent', 0, 20, 0, 0, 1, NULL, 1, 1, '2025-08-08 17:01:00', '2025-08-29 17:01:00', 1, 0, '2025-08-08 17:01:16', '2025-08-26 10:11:06'),
(2, 'TEST-02', '    ส่งฟรีไม่มีขั้นต่ำ', 'ส่งฟรีไม่มีขั้นต่ำ', 'free_shipping', 1, 0, 0, 3, 0, 9999, 0, 1, '2025-08-13 14:30:00', '2025-08-27 14:30:00', 1, 0, '2025-08-13 14:31:01', '2025-08-18 16:22:28'),
(3, 'TEST-03', '   สั่งซื้อครบลดไปเลย 1000 บาท!!!', 'สั่งซื้อครบ 500.- รับส่วนลดทันที 1000 บาท!!!', 'fixed', 1, 1000, 500, NULL, 1, NULL, 1, 1, '2025-08-14 11:02:00', '2025-09-06 11:02:00', 1, 0, '2025-08-14 11:02:18', '2025-08-21 14:14:03'),
(4, 'TEST-04', ' ซื้อครบลดไป 15%!!!', 'ซื้อครบ 500.- ลดไป 15%!!!', 'percent', 1, 15, 500, 4, 0, 5, 0, 0, '2025-08-14 11:02:00', '2025-08-22 11:03:00', 1, 0, '2025-08-14 11:03:05', '2025-08-21 15:34:38'),
(5, 'FRESHP702025', ' ส่งฟรีมีขั้นต่ำ', 'คูปองส่งฟรีมีขั้นต่ำ 70 บาท.-', 'free_shipping', 1, 0, 0, 1, 0, NULL, 1, 1, '2025-08-15 11:16:00', '2025-08-30 11:16:00', 1, 0, '2025-08-15 11:16:47', '2025-08-20 15:29:31'),
(6, 'TESTTT1', '              ทดสอบ1', ' ทดสอบ1', 'percent', 1, 20, 10, 3, 0, 7, 0, 1, '2025-08-20 16:21:00', '2025-08-28 16:21:00', 1, 0, '2025-08-20 16:21:22', '2025-08-21 10:53:33'),
(7, 'TESTAMO-01', ' ทดสอบจำนวน', ' ทดสอบจำนวน', 'percent', 1, 99, 0, 10, 0, 4, 0, 1, '2025-08-21 10:59:00', '2025-09-04 10:59:00', 1, 0, '2025-08-21 10:59:14', '2025-08-21 11:50:12');

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
(60, 4, 21, 1, 0, '2025-08-21 14:37:53', '2025-08-21 14:37:53');

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
(31, 1, 19, 53, 2350.00, 2, '2025-08-26 03:40:45'),
(32, 1, 19, 54, 5085.60, 5, '2025-08-26 03:53:08'),
(33, 1, 19, 55, 79.60, 1, '2025-08-26 06:17:05'),
(34, 1, 19, 56, 2346.20, 1, '2025-08-26 06:17:48');

-- --------------------------------------------------------

--
-- Table structure for table `customer_list`
--

CREATE TABLE `customer_list` (
  `id` int NOT NULL,
  `firstname` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `middlename` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `lastname` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `gender` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contact` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sub_district` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `district` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `province` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `postal_code` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `avatar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_list`
--

INSERT INTO `customer_list` (`id`, `firstname`, `middlename`, `lastname`, `gender`, `contact`, `email`, `address`, `sub_district`, `district`, `province`, `postal_code`, `password`, `avatar`, `date_created`, `date_updated`) VALUES
(1, 'Mark', 'D', 'Cooper', 'Male', '0912356498', 'mcooper@mail.com', NULL, NULL, NULL, NULL, NULL, 'c7162ff89c647f444fcaa5c635dac8c3', 'uploads/customers/1.png?v=1675045908', '2023-01-30 10:31:48', '2023-01-30 10:49:25'),
(2, 'jame', 'a', 'huansin', 'Male', '-', 'jame@gmail.com', NULL, NULL, NULL, NULL, NULL, 'fcea920f7412b5da7be0cf42b8c93759', 'uploads/customers/2.png?v=1704643304', '2024-01-07 11:25:47', '2024-01-07 23:01:44'),
(4, 'jame', 'a', 'asd', 'Male', '12', 'asdsa@rr.com', NULL, NULL, NULL, NULL, NULL, 'e10adc3949ba59abbe56e057f20f883e', NULL, '2024-01-07 11:33:40', '2024-01-07 11:33:40'),
(5, 'Beam', 'Beam', 'Beam', 'Female', '0110055', 'Beam@Beam.com', NULL, NULL, NULL, NULL, NULL, 'e10adc3949ba59abbe56e057f20f883e', 'uploads/customers/5.png?v=1707833626', '2024-02-13 21:13:46', '2024-02-13 21:13:46'),
(6, 'แมทิวlnwza', '', 'นาโนวา007', 'Male', '099-999-9999', 'matew999@gmail.com', NULL, NULL, NULL, NULL, NULL, 'fa246d0262c3925617b0c72bb20eeb1d', 'uploads/customers/6.png?v=1747647314', '2025-05-19 16:19:24', '2025-05-19 16:35:14'),
(7, 'สั้น', '', 'ส', 'Male', '088-222-2222', 'short@gmail.com', NULL, NULL, NULL, NULL, NULL, '81dc9bdb52d04dc20036dbd8313ed055', NULL, '2025-05-20 10:09:07', '2025-05-20 10:09:07'),
(8, 'user', '', '1', 'Male', '077-011-1122', 'user1@gmail.com', '21/8', 'นานา', 'เมือง', 'ตรัง', '92222', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/8.png?v=1747741055', '2025-05-20 18:37:35', '2025-06-14 10:58:36'),
(9, 'นัทตี้', '', 'แต๋วแตก', 'ชาย', '055-555-5656', 'nutty@gmail.com', '55/5', 'ตลกจัง', NULL, 'ขำก๊าก', '55555', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/9.png?v=1748624417', '2025-05-31 00:00:17', '2025-05-31 00:00:17'),
(10, 'address', '', 'pro', 'ชาย', '011-777-9999', 'address@gmail.com', '21 หมู่ 5 ถนนเพชเกษม', 'นาท่ามเหนือ', 'เมือง', 'ตรัง', '921110', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/10.png?v=1749005687', '2025-06-04 09:54:47', '2025-06-04 10:00:11'),
(11, 'Address', '', '2', 'หญิง', '011-999-7777', 'address2@gmail.com', '50/5', 'บางรัก', 'บางบ่อ', 'สมุทรปราการ', '10560', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/11.png?v=1749006171', '2025-06-04 10:02:51', '2025-06-04 10:02:51'),
(12, 'หมาป่าเดียวดาย', '', 'สมาธิ', 'Male', '777-888-9999', 'user2@gmail.com', '33', 'หมาป่า', 'เดียวดาย', 'ตัวเทา', '78945', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/12.png?v=1749007549', '2025-06-04 10:25:49', '2025-06-24 14:39:48'),
(13, 'ล.เล่เล่เล่', '', 'หัวเรือ', 'Female', '014-528-7575', 'ley@gmail.com', '21/8', 'นานา', 'เมือง', 'ตรัง', '92222', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/13.png?v=1749030420', '2025-06-04 16:47:00', '2025-06-04 16:55:23'),
(14, 'นายหมาในดำใดดง', 'ณ', 'ป่ามะขาม', 'Male', '011-557-8686', 'wolf@gmail.com', '56/65 ถนนป่าดิบ', 'มะขามเปียก', 'เขียวมะขาม', 'ป่ามะขาม', '11224', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/14.png?v=1749117170', '2025-06-05 16:52:50', '2025-06-05 16:52:50'),
(15, 'นายเอกวัน', '', 'สองไม่รองใคร', 'Male', '022-555-9898', 'user3@gmail.com', '41 ถนนเส้นตรง', 'โค้งนิด', 'บิดหน่อย', 'ทางเร็ว', '92124', '81dc9bdb52d04dc20036dbd8313ed055', NULL, '2025-06-17 12:58:09', '2025-06-17 13:00:37'),
(16, 'เอกไม', '', 'ไมค์ทองคำ', 'Male', '023-858-9988', 'user4@gmail.com', '32 หมู่ 8 ถนนเสียงบรรเลง', 'ทองคำเปลว', 'ร้องจนหมด', 'หนี้ไม่มี', '88994', '81dc9bdb52d04dc20036dbd8313ed055', NULL, '2025-06-17 13:17:23', '2025-06-17 13:17:23'),
(17, 'นางสาวสามารถ', '', 'ทำได้ดี', 'Male', '068-888-9999', 'user5@gmail.com', '', '', '', '', '', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/17.png?v=1750141789', '2025-06-17 13:29:27', '2025-06-17 13:29:49'),
(18, 'นายฉันท์ชยา', '', 'ภิญโญ', 'Male', '0828398430', 'faritre5566@gmail.com', '21 หมู่ 5 ถนนเพชรเกษม', 'นาท่ามเหนือ', 'เมือง', 'ตรัง', '92190', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/18.png?v=1751264120', '2025-06-26 15:42:39', '2025-06-30 13:15:20'),
(19, 'นางอัญมณีasdasd', '', 'คงสีaaa', 'Female', '088-115-5458', 'faritre1@gmail.com', '44 หมู่ 8', 'นาท่ามเหนือ', 'เมือง', 'ตรัง', '92190', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/19.png?v=1753184066', '2025-06-26 15:48:41', '2025-07-22 18:34:26'),
(20, 'แมวหลาม', '', 'ซาบะ', 'Male', '011-555-6687', 'faritre4@gmail.com', '55/87', 'ทะเลฟ้า', 'เรือใบ', 'ทะเลกว้าง', '99887', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/customers/20.png?v=1752809365', '2025-07-18 10:29:25', '2025-07-18 10:29:25');

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
(53, 34, NULL, 1, 1, 19.00),
(53, 14, NULL, 1, 1, 11731.00),
(54, 40, 16, 1, 1, 55.00),
(54, 41, 16, 1, 1, 169.00),
(54, 38, 16, 1, 1, 55.00),
(54, 37, 16, 1, 1, 149.00),
(54, 36, 16, 1, 1, 25000.00),
(55, 33, 10, 1, 2, 199.00),
(56, 14, 13, 1, 1, 11731.00),
(57, 36, 16, NULL, 2, 25000.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_list`
--

CREATE TABLE `order_list` (
  `id` int NOT NULL,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `customer_id` int NOT NULL,
  `delivery_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `total_amount` float(12,2) NOT NULL DEFAULT '0.00',
  `promotion_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `coupon_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
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

INSERT INTO `order_list` (`id`, `code`, `customer_id`, `delivery_address`, `total_amount`, `promotion_discount`, `coupon_discount`, `shipping_methods_id`, `shipping_prices_id`, `promotion_id`, `coupon_code_id`, `payment_status`, `delivery_status`, `status`, `is_seen`, `date_created`, `date_updated`) VALUES
(53, '2025082600001', 19, '44 หมู่ 8, ต.นาท่ามเหนือ, อ.เมือง, จ.ตรัง, 92190', 9450.00, 0.00, 2350.00, 3, NULL, NULL, 1, 0, 0, 0, 1, '2025-08-26 10:40:45', '2025-08-26 10:53:20'),
(54, '2025082600002', 19, '44 หมู่ 8, ต.นาท่ามเหนือ, อ.เมือง, จ.ตรัง, 92190', 10246.20, 10171.20, 5085.60, 3, NULL, 16, 1, 2, 4, 0, 1, '2025-08-26 10:53:08', '2025-08-26 14:20:20'),
(55, '2025082600003', 19, '44 หมู่ 8, ต.นาท่ามเหนือ, อ.เมือง, จ.ตรัง, 92190', 239.00, 119.40, 79.60, 3, 1, 10, 1, 0, 0, 0, 1, '2025-08-26 13:17:05', '2025-08-26 14:38:29'),
(56, '2025082600004', 19, '44 หมู่ 8, ต.นาท่ามเหนือ, อ.เมือง, จ.ตรัง, 92190', 9334.80, 100.00, 2346.20, 7, 65, 13, 1, 0, 0, 0, 1, '2025-08-26 13:17:48', '2025-08-26 14:38:29'),
(57, '2025082700001', 19, '44 หมู่ 8, ต.นาท่ามเหนือ, อ.เมือง, จ.ตรัง, 92190', 30050.00, 20000.00, 0.00, 3, 2, 16, NULL, 0, 0, 0, 1, '2025-08-27 09:05:21', '2025-08-27 09:05:31');

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
(33, 40, 'uploads/products/gallery_68a8177b22d432.76719975.png', '2025-08-22 07:08:43', '2025-08-22 07:08:43');

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
(14, '', '', '', '2025-06-12 10:51:24', '2025-07-09 16:35:53'),
(20, 'https://shopee.co.th/Madcatz-MAD-60-68-HE-%E0%B8%84%E0%B8%B5%E0%B8%A2%E0%B9%8C%E0%B8%9A%E0%B8%AD%E0%B8%A3%E0%B9%8C%E0%B8%94%E0%B8%AA%E0%B8%A7%E0%B8%B4%E0%B8%95%E0%B8%8A%E0%B9%8C%E0%B9%81%E0%B8%A1%E0%B9%88%E0%B9%80%E0%B8%AB%E0%B8%A5%E0%B9%87%E0%B8%81%E0%B8%AA%E0%B9%8D%E0%B8%B2%E0%B8%AB%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%80%E0%B8%A5%E0%B9%88%E0%B8%99%E0%B9%80%E0%B8%81%E0%B8%A1-61-68-%E0%B8%84%E0%B8%B5%E0%B8%A2%E0%B9%8C-Latency-%E0%B8%95%E0%B9%88%E0%B9%8D%E0%B8%B2-Full-Key-Hot-Swap-i.145792167.26366613520', '', '', '2025-06-17 13:40:27', '2025-06-30 11:47:03'),
(21, 'https://shopee.co.th/iHAVECPU-HEADSET-(%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87)-iHAVECPU-MUSES-WITH-MIC-(BLACK-RED)-i.15422060.25650552218', '', '', '2025-06-17 13:43:16', '2025-06-17 13:43:16'),
(22, 'https://shopee.co.th/-%E0%B8%9B%E0%B8%A3%E0%B8%B0%E0%B8%81%E0%B8%B1%E0%B8%99%E0%B8%A8%E0%B8%B9%E0%B8%99%E0%B8%A2%E0%B9%8C%E0%B9%84%E0%B8%97%E0%B8%A2-FiiO-JD10-%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87-IEMs-%E0%B9%84%E0%B8%94%E0%B8%A3%E0%B9%80%E0%B8%A7%E0%B8%AD%E0%B8%A3%E0%B9%8C-Dynamic-%E0%B8%AA%E0%B8%B8%E0%B8%94%E0%B8%84%E0%B8%B8%E0%B9%89%E0%B8%A1-%E0%B8%AA%E0%B8%B3%E0%B8%AB%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B9%80%E0%B8%A5%E0%B9%88%E0%B8%99%E0%B9%80%E0%B8%81%E0%B8%A1-%E0%B8%A3%E0%B8%AD%E0%B8%87%E0%B8%A3%E0%B8%B1%E0%B8%9A-Hi-Res-i.1819391.29563989283', '', '', '2025-06-17 13:48:50', '2025-06-17 13:48:50'),
(23, 'https://shopee.co.th/(%E0%B9%81%E0%B8%9E%E0%B9%87%E0%B8%81-12)-Muek-Groob-%E0%B8%AB%E0%B8%A1%E0%B8%B6%E0%B8%81%E0%B8%81%E0%B8%A3%E0%B8%B8%E0%B8%9A-%E0%B9%80%E0%B8%AA%E0%B9%89%E0%B8%99%E0%B8%9A%E0%B8%B8%E0%B8%81%E0%B8%9B%E0%B8%A3%E0%B8%B8%E0%B8%87%E0%B8%A3%E0%B8%AA%E0%B8%AB%E0%B8%A1%E0%B9%88%E0%B8%B2%E0%B8%A5%E0%B9%88%E0%B8%B2-%E0%B8%AA%E0%B8%B9%E0%B8%95%E0%B8%A3%E0%B8%94%E0%B8%B1%E0%B9%89%E0%B8%87%E0%B9%80%E0%B8%94%E0%B8%B4%E0%B8%A1-%E0%B8%AB%E0%B8%A1%E0%B8%B6%E0%B8%81%E0%B8%81%E0%B8%A3%E0%B8%B8%E0%B8%9A%E0%B8%8B%E0%B8%B1%E0%B8%99%E0%B8%8B%E0%B8%B8-sunsu-i.214411437.25928257606', '', '', '2025-06-17 13:52:22', '2025-06-17 13:52:22'),
(24, 'https://shopee.co.th/Llamito-%E0%B8%9C%E0%B8%87%E0%B8%A1%E0%B8%B1%E0%B8%97%E0%B8%89%E0%B8%B0-%E0%B8%AD%E0%B8%AD%E0%B8%A3%E0%B9%8C%E0%B9%81%E0%B8%81%E0%B8%99%E0%B8%B4%E0%B8%84-(Matcha-Powder)-%E0%B8%82%E0%B8%99%E0%B8%B2%E0%B8%94-250g-i.18117499.10946816382', '', '', '2025-06-17 14:22:06', '2025-06-17 14:22:06'),
(25, 'https://shopee.co.th/%F0%9F%94%A5%E0%B8%AA%E0%B8%B4%E0%B8%99%E0%B8%84%E0%B9%89%E0%B8%B2%E0%B8%82%E0%B8%B2%E0%B8%A2%E0%B8%94%E0%B8%B5%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%AA%E0%B8%B8%E0%B8%94%F0%9F%94%A5-Monster-XKT02-BT-5.3-%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87-%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87%E0%B8%9A%E0%B8%A5%E0%B8%B9%E0%B8%97%E0%B8%B9%E0%B8%98-%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87%E0%B9%84%E0%B8%A3%E0%B9%89%E0%B8%AA%E0%B8%B2%E0%B8%A2-%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87-monster-HIFI-i.886198470.20357703411?sp_atk=c97ab49a-a5e3-4e76-8dc9-7da9c262f62f&xptdk=c97ab49a-a5e3-4e76-8dc9-7da9c262f62f', '', '', '2025-06-17 14:25:31', '2025-07-08 10:55:57'),
(26, 'https://shopee.co.th/Jeep-JP-EW011-%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87%E0%B8%9A%E0%B8%A5%E0%B8%B9%E0%B8%97%E0%B8%B9%E0%B8%98%E0%B9%84%E0%B8%A3%E0%B9%89%E0%B8%AA%E0%B8%B2%E0%B8%A2-HiFi-HD-Call-%E0%B8%A5%E0%B8%94%E0%B9%80%E0%B8%AA%E0%B8%B5%E0%B8%A2%E0%B8%87%E0%B8%A3%E0%B8%9A%E0%B8%81%E0%B8%A7%E0%B8%99-%E0%B8%88%E0%B8%B1%E0%B8%9A%E0%B8%84%E0%B8%B9%E0%B9%88%E0%B9%80%E0%B8%A3%E0%B9%87%E0%B8%A7%E0%B8%AB%E0%B8%B9%E0%B8%9F%E0%B8%B1%E0%B8%87%E0%B8%9A%E0%B8%A5%E0%B8%B9%E0%B8%97%E0%B8%B9%E0%B8%98-%E0%B8%9E%E0%B8%A3%E0%B9%89%E0%B8%AD%E0%B8%A1%E0%B9%84%E0%B8%A1%E0%B9%82%E0%B8%84%E0%B8%A3%E0%B9%82%E0%B8%9F%E0%B8%99-i.1049006465.23762574879?sp_atk=26742576-54f0-444a-8da6-7efd6255b5ad&xptdk=26742576-54f0-444a-8da6-7efd6255b5ad', '', '', '2025-06-17 14:27:14', '2025-06-17 14:27:14'),
(27, '', '', '', '2025-06-17 14:30:06', '2025-07-08 15:24:29'),
(29, '', '', '', '2025-06-25 09:56:39', '2025-08-22 11:39:29'),
(30, '', '', '', '2025-06-30 09:37:04', '2025-07-23 13:22:05'),
(31, '', '', '', '2025-07-08 10:57:13', '2025-07-08 10:57:13'),
(33, '', '', '', '2025-07-18 09:20:38', '2025-07-18 09:32:26'),
(34, '', '', '', '2025-07-22 18:40:44', '2025-07-22 18:40:44'),
(35, '', '', '', '2025-08-21 09:31:50', '2025-08-21 09:31:50'),
(36, '', '', '', '2025-08-22 13:22:38', '2025-08-22 13:25:09'),
(37, '', '', '', '2025-08-22 13:43:01', '2025-08-22 13:47:03'),
(38, '', '', '', '2025-08-22 13:51:20', '2025-08-22 13:52:32'),
(39, '', '', '', '2025-08-22 13:57:18', '2025-08-22 13:59:41'),
(40, '', '', '', '2025-08-22 14:05:46', '2025-08-22 14:08:43'),
(41, '', '', '', '2025-08-22 14:12:21', '2025-08-22 14:12:21');

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
  `dose` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `price` float(12,2) NOT NULL,
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
  `slow_prepare` tinyint(1) NOT NULL DEFAULT '0',
  `sku` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_list`
--

INSERT INTO `product_list` (`id`, `category_id`, `brand`, `name`, `description`, `dose`, `price`, `image_path`, `status`, `delete_flag`, `date_created`, `date_updated`, `discount_type`, `discount_value`, `discounted_price`, `product_width`, `product_length`, `product_height`, `product_weight`, `slow_prepare`, `sku`) VALUES
(14, 1, ' GALAX(แกลแล็กซ์)', 'VGA GALAX GEFORCE RTX 4060 1-CLICK 2X V3 OC - 8GB GDDR6', 'Brand : GALAX\r\n\r\nModel : 1-CLICK 2X V3 OC\r\n\r\nGPU : NVIDIA GeForce RTX 4060\r\n\r\nCUDA Core / Stream Processors : 3072\r\n\r\nCore Clock : 2475 MHz\r\n\r\n1-Click OC Clock: 2490MHz*\r\n\r\n*(by installing Xtreme Tuner Plus Software and using 1-Click OC)\r\n\r\nMemory Clock : 17 Gbps\r\n\r\nMemory Size : 8 GB\r\n\r\nMemory Type : GDDR6\r\n\r\nMemory Interface : 128 bit\r\n\r\nBus Interface : PCI-E 4.0\r\n\r\nHDMI : 1 port\r\n\r\nDisplayPort : 3 port\r\n\r\nDVI : None\r\n\r\nD-Sub (VGA) : None\r\n\r\nMini HDMI : None\r\n\r\nMini DisplayPort : None\r\n\r\nUSB : None\r\n\r\nMicrosoft DirectX Support : 12 Ultimate\r\n\r\nOpenGL : 4.6\r\n\r\nMaximum Resolution : 7680x4320\r\n\r\nPower Input : 1 x 8-pin\r\n\r\nPower Supply Requirement : 550W\r\n\r\nWindows Support : 10/11\r\n\r\nVGA Length : 251mm\r\n\r\nDimension (W x D x H) : 13.30 x 25.10 x 4.10 cm\r\n\r\nNet Weight : 0.00\r\n\r\nPackage Dimension (W x D x H) : 0.00 x 0.00 x 0.00 cm\r\n\r\nGross Weight : 0.00\r\n\r\nVolume : 0.00\r\n\r\nประกัน : 3 ปี', '', 11731.00, 'uploads/product//1_Screenshot 2025-06-12 105117.png?v=1750127763', 1, 0, '2025-06-12 10:51:24', '2025-07-08 10:49:39', NULL, NULL, NULL, 15.00, 30.00, 15.00, 1500.00, 0, '1'),
(20, 6, 'Madcatz', 'Madcatz MAD 60/68 HE คีย์บอร์ดสวิตช์แม่เหล็กสําหรับเล่นเกม 61/68 คีย์ Latency ต่ํา Full Key Hot Swap', '🎉สวัสดีค่ะ. ยินดีต้อนรับสู่ &quot;สถานีอวกาศ&quot; ยินดีให้บริการครับ\r\n👉ของแท้100%! รับประกันหนึ่งปี!\r\n🎁เช็คสินค้าก่อนส่ง! แผ่นกันกระแทก! มาพร้อมของขวัญเล็กๆ น้อยๆ!\r\n🚚 จัดส่งภายใน 24 ชั่วโมง! มาถึงประมาณ 1-2 วัน!\r\n📣ราคาโปรโมชั่น + คูปอง = คุ้มกว่า\r\n❤ติดตามร้านค้าของเรา! เป็นแฟนของเราและเพลิดเพลินกับส่วนลด 2% เมื่อทำการสั่งซื้อ! และเพลิดเพลินกับการรับประกันนาน 2 ปี\r\n✨【โปรดยืนยันว่าสินค้าอยู่ในสภาพดีก่อนที่จะคลิก &quot;ยืนยันการรับ&quot; มิฉะนั้นหน้าต่างการคืนสินค้าจะถูกปิด】\r\n\r\nข้อกำหนดเบื้องต้น\r\nยี่ห้อ Madcatz ยี่ห้อ: Madcatz\r\nชื่อสินค้า: US-Canada MMAD 60 / 68HE Electric:\r\nเอฟเฟกต์แสงพื้นหลัง: ไม่มีด้าน\r\nจำนวนปุ่ม: 61-70 ปุ่ม / RGB\r\nการเชื่อมต่ออุปกรณ์พร้อมกัน: 1 เครื่อง\r\nประเภทปลั๊ก: Hot-swap พร้อมปุ่มทั้งหมด\r\nระบบที่รองรับ: Windows, MacOS\r\nการเชื่อมต่อ: มีสาย\r\nประเภท: แป้นพิมพ์เชิงกลที่กำหนดเอง', '', 2215.00, 'uploads/product//Screenshot 2025-06-17 134019.png?v=1750142427', 1, 0, '2025-06-17 13:40:27', '2025-06-30 11:47:03', 'amount', 1575, 640, NULL, NULL, NULL, 2.20, 0, 'MADCATZ-MAD-60-68-HE'),
(21, 7, 'iHAVECPU', 'iHAVECPU HEADSET (หูฟัง) iHAVECPU MUSES WITH MIC (BLACK/RED)', 'iHAVECPU HEADSET (หูฟัง) iHAVECPU MUSES WITH MIC (BLACK/RED)\r\n\r\n iHAVECPU MUSES WITH MIC หูฟังรุ่น Exclusive จาก iHAVECPU   โดยได้รับแรงบันดาลใจจากเทพธิดา &quot;มิวส์&quot; ผู้ขับร้องบทเพลงอันแสนไพเราะที่แม้แต่เทพเจ้ายังต้องเงี่ยโสดสดับฟัง\r\n\r\nคุณสมบัติสินค้า\r\n\r\n● Headset Brand : KZ\r\n● Color : BLACK / RED\r\n● Connector : 3.5 mm.\r\n● Driver Unit : 10mm.\r\n● Frequency Response : 20Hz ~ 40000 Hz\r\n● Sensitivity : 103+/-3dB\r\n● Input Impedance : 23 Ohms\r\n● Mic. Sensitivity : 112dB\r\n● Warranty : 3 Months\r\n\r\n#เก็บเงินปลายทางได้ครับ\r\n\r\niHAVECPU ถ้าคุณชอบคอมพิวเตอร์ เราคือเพื่อนกัน\r\nบริษัท ไอ แฮฟ ซีพียู จำกัด', '', 290.00, 'uploads/product//Screenshot 2025-06-17 134311.png?v=1750142596', 1, 0, '2025-06-17 13:43:16', '2025-06-17 13:43:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(22, 7, 'FiiO', '[ประกันศูนย์ไทย] FiiO JD10 หูฟัง IEMs ไดรเวอร์ Dynamic สุดคุ้ม สำหรับเล่นเกม รองรับ Hi-Res', 'FiiO JD10\r\n\r\nหูฟัง IEMs ไดรเวอร์ Dynamic สุดคุ้ม สำหรับเล่นเกม ประกันศูนย์ไทย\r\nประกันศูนย์ไทย 1 ปี\r\n&quot;ตามเงื่อนไขการรับประกัน&quot;\r\nสินค้าประเภท : IEMs, Inear, หูฟังอินเอียร์\r\nไดรเวอร์ : Dynamic 10 mm\r\nสายหูฟัง : ทองแดง OFC\r\nขั้วหูฟัง : 2พิน0.78\r\nรุ่นที่มีจำหน่าย : 3.5 ไมค์ (ถอดสายได้)/ TypeC (ถอดสายไม่ได้)\r\nไมค์ = แจ๊คขนาด 3.5mm แต่มีไมค์โครโฟนสำหรับคุยสาย\r\nType C = แจ๊คแบบ Type C และ มีไมค์โครโฟน (สำหรับมือถือ Type C เท่านั้น)\r\n\r\nFiiO JD10\r\nหูฟัง IEMs ไดรเวอร์ Dynamic สุดคุ้มราคาจับต้องได้ สำหรับคอ HiFi หรือสำหรับการเล่นเกม\r\nหูฟังเลือกใช้ดรเวอร์ Dynamic ขนาด 10 มม. ไดอะแฟรมโพลีเมอร์คอมโพสิตความแข็งสูง\r\nให้เสียงเบสที่หนักแน่นและเต็มอิ่ม ใช้คอยล์เสียง CCAW ของญี่ปุ่นขนาด 0.033 มม.\r\nที่เบากว่า แข็งแกร่งกว่า ให้เสียงสูงได้ง่ายขึ้น ตอบสนองได้ไวขึ้น ใช้แม่เหล็กนีโอไดเมียม\r\nโบรอนเหล็กประสิทธิภาพสูง N52 ช่วยให้ไดรเวอร์ทำงานได้อย่างเต็มประสิทธิภาพ\r\nใช้การออกแบบอะคูสติกแบบช่องคู่ โดยมีระบบควบคุมท่อนำเสียงในแต่ละโพรง\r\nช่วยลดการบิดเบือนของเสียงช่วยเพิ่มการเคลื่อนไหวของไดอะแฟรม การออกแบบนี้\r\nจะช่วยให้ JD10 สามารถจับรายละเอียดต่างๆ ได้มากขึ้น ส่งผลให้ได้เบสที่รวดเร็ว\r\nผสมผสานกับเสียงกลางและเสียงแหลมได้อย่างลงตัวเพื่อให้ได้เสียงที่น่าฟังอย่างแท้จริง\r\n\r\nข้อมูลสินค้าเบื้องต้น\r\nไดรเวอร์ Dynamic 10 มม.\r\nไดอะแฟรมโพลีเมอร์คอมโพสิต\r\nการออกแบบช่องคู่\r\nคอยล์เสียง CCAW ของญี่ปุ่นขนาด 0.033 มม.\r\nแม่เหล็กนีโอไดเมียมโบรอนเหล็กประสิทธิภาพสูง N52\r\nกำลังขับ 32 Ohm@1kHz\r\nตอบสนองความถี่ 20 - 40 kHz\r\nความไวเสียง 105 dB/mW@1kHz\r\nสายหูฟัง OFC ปราศจากออกซิเจนความบริสุทธิ์สูง\r\nรองรับการควบคุมแบบ ln-ine พร้อมไมค์ในตัว\r\nชิปถอดรหัส DSP รองรับ 24bit/384kHz (เฉพาะรุ่น TypeC)\r\nพร้อมตั้งค่าจูนในตัว 6 แบบ (เฉพาะรุ่นTypeC)\r\nรองรับทุกแพลตฟอร์มและอุปกรณ์ (เฉพาะรุ่นTypeC)\r\nน้ำหนักเบาเพียง 3.7 กรัม (ไม่รวมสาย\r\nรองรับ Hi-Res\r\n\r\nJD10 ถือเป็นมาตรฐานสำหรับคุณภาพเสียงระดับเริ่มต้น โดยมีไดอะแฟรมโพลีเมอร์\r\nคอมโพสิตขนาด 10 มม. ที่ได้รับการคัดสรรมาอย่างพิถีพิถัน ไดอะแฟรมนี้มีความแข็งสูง\r\nความผิดเพี้ยนต่ำ และมีการหน่วงเสียงที่เหมาะสม ช่วยให้เสียงเบสที่หนักแน่นและเต็มอิ่ม\r\nซึ่งผสมผสานกับเสียงกลางและเสียงแหลมได้อย่างลงตัวเพื่อให้ได้เสียงที่น่าฟัง\r\nอย่างแท้จริง นอกจากนี้ ด้วยการปรับแต่งเสียงอย่างพิถีพิถันซึ่งใช้ประโยชน์จากความแข็ง\r\nของไดอะแฟรม ทำให้ JD10 มีความละเอียดสูงในลักษณะที่ไม่ค่อยพบเห็นในหูฟังรุ่นเดียวกัน\r\nสามารถสร้างรายละเอียดเสียงที่ละเอียดอ่อนได้\r\nเสียงเบสที่เร้าใจ ประสบการณ์ที่น่าตื่นตาตื่นใจ การออกแบบช่องคู่\r\nJD10 ใช้การออกแบบอะคูสติกแบบช่องคู่ โดยมีระบบควบคุมท่อนำเสียงในแต่ละโพรง\r\nการควบคุมการหน่วงการไหลของอากาศที่แม่นยำนี้ช่วยลดการบิดเบือนของเสียง\r\nช่วยเพิ่มการเคลื่อนไหวของไดอะแฟรมและการขยายเสียงเบสได้อย่างมีประสิทธิภาพ\r\nเมื่อฟังเพลงที่ซับซ้อน การออกแบบนี้จะช่วยให้ JD10 สามารถจับรายละเอียดต่างๆ\r\nในโน้ตเบสได้มากขึ้น ส่งผลให้ได้เบสที่รวดเร็วและมีรายละเอียดที่สดชื่น\r\nคอยล์เสียง CCAW ของญี่ปุ่นขนาด 0.033 มม.\r\nเบากว่า แข็งแกร่งกว่า\r\nเพื่อส่งมอบเสียงที่มีคุณภาพสูงขึ้นจากไดรเวอร์ JD10 จึงใช้คอยล์เสียง CCAW แบบพิเศษ\r\nของญี่ปุ่นที่มีความละเอียดพิเศษ โดยมีเส้นผ่านศูนย์กลางลวดเพียงประมาณ 0.033 มม.\r\nคอยล์เสียงนี้ช่วยลดมวลรวมของไดรเวอร์ได้อย่างมากเนื่องจากความบางของสาย\r\nและคุณสมบัติของ CCAW ส่งผลให้ไดรเวอร์สามารถผลิตเสียงที่มีระดับเสียงสูงขึ้นได้ง่ายขึ้น\r\nและยังช่วยปรับปรุงการตอบสนองชั่วขณะอีกด้วย ผลลัพธ์ที่ได้คือเสียงที่สะอาดและน่าฟังยิ่งขึ้น\r\nแม่เหล็กประสิทธิภาพสูงภายนอก N52\r\nคุณภาพเสียงอันทรงพลังที่แข็งแกร่ง\r\nJD10 ใช้การออกแบบแม่เหล็กภายนอก ซึ่งทำให้สามารถใช้แม่เหล็กขนาดใหญ่ขึ้นได้\r\nซึ่งจะห่อหุ้มคอยล์เสียงได้อย่างมีประสิทธิภาพมากขึ้น และทำให้เสียงมีพลังมากขึ้น\r\nแม่เหล็กนีโอไดเมียมโบรอนเหล็กประสิทธิภาพสูง N52 ได้รับการเลือกใช้เนื่องจาก\r\nมีพลังงานมากเพียงพอและสามารถให้แรงกระทำได้ทั่วถึงในพื้นที่กว้างอย่างสม่ำเสมอ\r\nช่วยให้ไดรเวอร์ทำงานได้อย่างเต็มประสิทธิภาพ\r\nมีชิปถอดรหัสในตัว (เฉพาะรุ่นTypeC)\r\nชิป DSP อิสระ\r\nJD10 TC มีชิป DSP อิสระประสิทธิภาพสูงในตัว* ช่วยให้คุณฟังเสียงความละเอียดสูง\r\nได้อย่างง่ายดายและสะดวกสบายโดยไม่ต้องใช้อุปกรณ์อื่นใด\r\nรองรับการถอดรหัสเสียงแบบไม่สูญเสียข้อมูล 384kHz/24bit\r\nJD10 TC รองรับการถอดรหัส 384kHz/24bit ซึ่งถือว่าดีที่สุดในระดับเดียวกัน\r\n\r\nคุณสามารถรับฟังเสียงความละเอียดสูงได้อย่างแท้จริงแม้ในอุปกรณ์ระดับเริ่มต้นนี้\r\nสายเคเบิลระดับออดิโอไฟล์ เส้นทางตรงสู่ระบบ HiFi\r\nแกนลวดตัวนำทำจากทองแดงปราศออกซิเจนที่มีความบริสุทธิ์สูง\r\nสามารถส่งสัญญาณเสียงที่สมบูรณ์ได้โดยไม่เกิดข้อผิดพลาด\r\nช่วยให้คุณดื่มด่ำกับโลกแห่งเสียงที่มีความเที่ยงตรงสูงได้ง่ายขึ้น\r\n*เวอร์ชัน JD10 TC ไม่รองรับการออกแบบสายเคเบิลแบบถอดเปลี่ยนได้ 2 พิน 0.78 มม.\r\n\r\nสินค้าประกอบไปด้วย\r\n1. กล่องสินค้า\r\n2. บัตรรับประกัน\r\n3. สายหูฟัง OFC ขั้ว 2พิน 0.78 (เฉพาะรุ่นปกติ)\r\n4. จุกหูฟัง 3 คู่ เล็ก/กลาง/ใหญ่ (ติดตั้งขนาดกลางให้ล่วงหน้า)\r\n\r\nหากลูกค้าสงสัยเงื่อนไขการรับประกันสินค้าข้อใด สามารถสอบถามเข้ามาได้\r\n\r\n#FiiO', '', 752.00, 'uploads/product//Screenshot 2025-06-17 134844.png?v=1750142930', 1, 0, '2025-06-17 13:48:50', '2025-06-17 13:48:50', 'percent', 50, 376, NULL, NULL, NULL, NULL, 0, NULL),
(23, 8, ' Sunsu(ซันซุ)', '(แพ็ก 12) Muek Groob หมึกกรุบ เส้นบุกปรุงรสหม่าล่า สูตรดั้งเดิม หมึกกรุบซันซุ sunsu', 'สินค้าประกอบด้วย \r\n\r\n1.หมึกกรุบ เส้นบุกปรุงรสหม่าล่า สูตรดั้งเดิม จำนวน 12 ซอง\r\n___________________\r\n\r\n&quot;หมึกกรุบ ชุบมื้ออร่อยให้ตัวคุณ&quot;\r\n🔥 เส้นบุกปรุงรสหม่าล่า\r\n🔥 อร่อย กินเพลิน ได้รสชาติหม่าล่าแท้ๆ\r\n🔥 ทุกสายต้องมีหมึกกรุบ ไม่ว่าจะเป็น สายกินเดี่ยวๆ สายดื่ม สายด่วนกินแบบเร่งรีบ สายดูดเส้น สายไหนก็กินเพลิน\r\n🔥 เพิ่มรสชาติให้มื้ออาหาร กินเดี่ยวอร่อยกรึบ กินคู่อร่อยกรุบ\r\n🔥 ทั้งซองเพียง 25 กิโลแคล หรือประมาณ 4 กิโลแคลต่อซองเล็ก เท่านั้น !!\r\n🔥 ไม่มี คลอเรสโตรอล ไขมันต่ำ\r\n🔥 Size Mini ทานได้สะดวก พร้อมอร่อยได้ทุกที่ ทุกเวลา\r\n\r\nรูปแบบสินค้า\r\n▪️ 1 ซอง บรรจุ 6 ซองเล็ก\r\n▪️ 1 ซอง น้ำหนักสุทธิ 50 กรัม (6 ชิ้น x 8.3 กรัม)\r\n▪️ สินค้ามีอายุ 9 เดือน นับจากวันที่ผลิต\r\n▪️ Product of SUNSU\r\n\r\n#หมึกกรุบ #ชุบมื้ออร่อยให้ตัวคุณ #กินเดี่ยวอร่อยกรึบกินคู่อร่อยกรุบ #ความสุขที่ไม่รู้สึกผิด #SUNSU\r\n___________________\r\n\r\nคำถามที่พบบ่อย\r\n\r\n1) การแก้ไขข้อมูลจัดส่ง และการยกเลิกออเดอร์\r\nทางร้านไม่สามารถแก้ไขข้อมูลชื่อ ที่อยู่ เบอร์โทรคุณลูกค้า หลังได้รับคำสั่งซื้อเข้ามาในระบบ กรุณาตรวจสอบข้อมูลให้ถูกต้องก่อนการกดสั่งซื้อสินค้าทุกครั้ง หากข้อมูลผิด รบกวนทำการยกเลิกออเดอร์พร้อมแจ้งผ่านแชทร้านค้า เพื่อให้ทางร้านตรวจสอบสถานะการจัดออเดอร์กับทางคลังสินค้า หากได้รับการอนุมัติคำขอ จึงค่อยทำการสั่งซื้อเข้ามาใหม่นะคะ\r\n2) ระยะเวลาการจัดส่งสินค้า\r\nใช้เวลาจัดส่งสินค้า กทม. - ปริมณฑล 2-3 วัน, ต่างจังหวัด 3-5 วัน นับจากวันที่สั่งซื้อสินค้าเข้ามาในระบบเรียบร้อย (ไม่รวมวันหยุดเสาร์-อาทิตย์ และวันหยุดนักขัตฤกษ์)\r\n3) การออกใบกำกับภาษี/ใบเสร็จรับเงิน\r\nคุณลูกค้าสามารถแจ้งความประสงค์ขอรับใบกำกับภาษี/ใบเสร็จรับเงิน ผ่านแชทร้านค้า โดยหากต้องการใบกำกับภาษีเต็มรูป &quot;กรุณาแจ้งภายใน 15 วันหลังสั่งซื้อสินค้า และกดยอมรับสินค้าในระบบ&quot;\r\nทางบริษัทจะออกใบกำกับภาษี/ใบเสร็จรับเงิน ให้กับทางลูกค้าภายใน 14 วันทำการ นับจากวันที่แจ้งขอเข้ามา (จะออกใบกำกับภาษีหลังจากลูกค้ากดรับสินค้าแล้วเท่านั้นนะคะ)\r\n\r\nขอบคุณค่ะ🙏', '', 300.00, 'uploads/product//Screenshot 2025-06-17 135217.png?v=1750143142', 1, 0, '2025-06-17 13:52:22', '2025-06-17 13:52:22', 'percent', 13, 261, NULL, NULL, NULL, NULL, 0, NULL),
(24, 9, 'Llamito', 'Llamito ผงมัทฉะ ออร์แกนิค (Matcha Powder) ขนาด 250g', 'ผงมัทฉะ ออร์แกนิค ตรา ยามิโตะ\r\nOrganic Matcha Powder (Llamito Brand)\r\n\r\nรายละเอียดสินค้า \r\nชื่อผลิตภัณฑ์ (ไทย) : ผงมัทฉะ ออร์แกนิค\r\nชื่อผลิตภัณฑ์ (อังกฤษ) : Organic Matcha Powder\r\nขนาดบรรจุ : 250 กรัม\r\nส่วนประกอบสำคัญ : ผงมัทฉะ ออร์แกนิค (Organic Matcha Powder) 100%\r\nเลขอย. : 1240095960030\r\n\r\nวิธีการรับประทาน  \r\nชงดื่มครั้งละ 1-2 ช้อน (สามารถปรับปริมาณความเข้มได้ตามชอบ) ในน้ำประมาณ 150-200 ml\r\nปั่นพร้อมกับผลไม้หรือสมูทตี้\r\nนำมาเขย่าในขวด Shake ทำให้ละลายได้อย่างทั่วถึง\r\nสามารถชงดื่มได้ตอนท้องว่าง ทั้งช่วงเช้าหรือเย็น\r\n\r\nวิธีการเก็บรักษา: หลังเปิดใช้งาน ควรปิดฝาให้สนิทและเก็บไว้ในตู้เย็น หรือในที่แห้ง หลีกเลี่ยงแสงแดดและความชื้น\r\n\r\nLlamito “Make your lifestyle healthier”', '', 900.00, 'uploads/product//Screenshot 2025-06-17 142159.png?v=1750144926', 1, 0, '2025-06-17 14:22:06', '2025-06-17 14:22:06', 'amount', 410, 490, NULL, NULL, NULL, NULL, 0, NULL),
(25, 7, ' Monster', '🔥สินค้าขายดีที่สุด🔥 Monster XKT02 BT 5.3 หูฟัง หูฟังบลูทูธ หูฟังไร้สาย หูฟัง monster HIFI', 'ยี่ห้อ Monster รุ่น: XKT02\r\nเวอร์ชันบลูทู ธ: 5.3\r\nประเภทสินค้า:หูฟังบลูทู ธ\r\nอินเทอร์เฟซการชาร์จแบบชาร์จไฟ: Type-C\r\nระยะการส่งสัญญาณบลูทู ธ: 10M\r\nความจุแบตเตอรี่ของหูฟัง: 40mAh\r\nความจุแบตเตอรี่ของห้องชาร์จ: 300mAh\r\nเกรดกันน้ำ: IPX54\r\nฟังก์ชั่นรีโมทคอนโทรล: การควบคุมแบบไร้สาย\r\n\r\nในรายการรวมด้วย:\r\n1 * หูฟัง\r\n1 * คู่มือการใช้งาน \r\n1 * สายชาร์จ\r\n1 * กล่องชาร์จ\r\n\r\nหากสินค้ามีปัญหาทักหาร้านค้าก่อนให้คะแนน\r\nไม่มีกล่องสินค้าทางร้านไม่รับผิดชอบทุกกรณี\r\nโปรดเก็บกล่องสินค้า/ใบเคลมไว้ตลอดระยะเวลาที่สินค้ามีประกัน', '', 1110.00, 'uploads/product//Screenshot 2025-06-17 142524.png?v=1750145131', 1, 0, '2025-06-17 14:25:31', '2025-07-08 10:55:57', 'percent', 50, 555, 5.00, 10.00, 13.00, 500.00, 0, 'XKT-02-BT'),
(26, 7, 'Jeep', 'Jeep JP-EW011 หูฟังบลูทูธไร้สาย HiFi HD Call ลดเสียงรบกวน จับคู่เร็วหูฟังบลูทูธ พร้อมไมโครโฟน', 'ข้อมูลจำเพาะ:\r\n\r\nรถจี๊ปยี่ห้อ Jeep\r\n\r\nรุ่น: JP EW011\r\n\r\nเวอร์ชันไร้สาย: V5.3\r\n\r\nขนาดลำโพง: Φ13mm\r\n\r\nความไว: 118 ± 3dB\r\n\r\nความต้านทาน: 250\r\n\r\nการตอบสนองความถี่ 50HZ-20KHZ\r\n\r\nแรงดันไฟฟ้าของเซลล์: 3.7V\r\n\r\nเวลาเล่นเพลง: ประมาณ 4H (ระดับเสียง 80%)\r\n\r\nเวลาสแตนด์บาย: ประมาณ 50 ชม\r\n\r\nเวลาในการชาร์จ: ประมาณ 2 ชม\r\n\r\nความจุแบตเตอรี่หูฟังหูฟัง: 30mAh / 3.7V\r\n\r\nความจุแบตเตอรี่ช่องชาร์จ: 300mAh / 3.7V\r\n\r\nขนาดสินค้า: 61.5x25x45.5 มม\r\n\r\n\r\n\r\nสิ่งที่อยู่ในกล่อง\r\n\r\nหูฟังบลูทู ธ 2 x หูฟังบลูทู ธ\r\n\r\nกรณีชาร์จ 1 x เคสชาร์จ\r\n\r\nสายชาร์จ 1 x\r\n\r\n1 x คู่มือการใช้งาน', '', 787.00, 'uploads/product//Screenshot 2025-06-17 142707.png?v=1750145234', 1, 0, '2025-06-17 14:27:14', '2025-06-17 14:27:14', 'amount', 568, 219, NULL, NULL, NULL, NULL, 0, NULL),
(27, 7, ' Basspro Max(เบสโปร แม็ก)', 'Basspro Power 2 หูฟังบลูทูธ ทัชสกรีน แบบ in-ear ระบบ ANC + ENC แท้ 100% เบสหนัก เสียงใส กันน้ำ', ' 🎈 สินค้ารับประกัน 1ปี 🎈\r\n\r\nเรามีฝ่ายบริการลูกค้าหลังการขาย ไว้ใจให้เราดูแลนะคะ\r\n\r\nข้อมูลเฉพาะ: ฟังเพลง ได้ทุกที่ทุกเวลา ด้วยหูฟังบลูทูธยี่ห้อ Basspro Power 2 เบสหนัก มาพร้อมระบบ Active Noise Cancelling ตัดเสียงรบกวน ใส่ออกกำลังกาย สบายๆ ไดร์เวอร์ตัวเดียวกับหูฟังราคาแพง\r\nบลูทูธ 5.4\r\nเป็นหูฟังแบบ in-ear\r\nเชื่อมต่อง่าย\r\n\r\nหูฟังบลูทูธยี่ห้อ Basspro Power ออกแบบมาให้ขนาดเล็ก น้ำหนักเบา เก็บไว้ในกระเป๋ากางเกงได้ไม่แกะกะ มาพร้อมกับระบบ Fast Charge USB Type-C\r\nจุดเด่นอีกอย่างคือตัวหูฟังสามารถ กันน้ำได้ระดับ IPX7\r\n ออกกำลังกายได้\r\n\r\n❤️-------------------- เงื่อนไขการเคลมสินค้า----------------❤️\r\n\r\n💎ช่วงเวลาให้บริการของเรา ทุกวัน เวลา 9.00 - 18.00 น.✅\r\n💎สินค้าในร้านจัดส่งจากไทยและใช้เวลาในการจัดส่งประมาณ 2-4วัน ต้องขออภัยหากมีการล่าช้าเกิดขึ้น\r\n📢ข้อมูลควรทราบในกรณีลูกค้ามีความประสงค์ต้องการส่งสินค้าคืนร้านเพื่อ ตรวจสอบ / เคลม📢\r\n❗️เมื่อตรวจสอบเเล้วสินค้าชำรุด บกพร่อง ทางร้านจะนำส่งสินค้าชิ้นใหม่พร้อมทั้ง ชำระค่าส่งพัสดุและค่าบริการให้ทันที\r\n❗️หากสินค้ามีปัญหารบกวนทักเข้ามาแจ้งทางร้านก่อนนะคะ สินค้ามีการรับประกันทุกชิ้น\r\n❗️หากสินค้ามีปัญหาและคุณลูกค้ากดรับสินค้าแล้ว ทางร้านขออนุญาตไม่รับผิดชอบค่าส่งในการส่งเคลมกับทางร้าน\r\n❗️ได้รับสินค้าแล้วรบกวนถ่ายวิดีโอก่อนแกะสินค้า หากไม่มีวิดีโอทางร้านขออนุญาตไม่รับเคลมทุกกรณี\r\n\r\n          🎈 ได้รับสินค้าแล้วรบกวนรีวิว 5 ดาวให้ทางร้านด้วยนะคะ🎈\r\n                     ร้าน IT Union ยินดีให้บริการค่ะ🎁\r\n             🎈 สินค้ารับประกัน 1 ปี  🎈\r\n\r\nThank you very much for using our service.🎉\r\nThank you for shopping with us today.🎀\r\nThank you for your purchase. Please let us know if we can do anything to improve our service.🌈', '', 699.00, 'uploads/product//Screenshot 2025-06-17 142959.png?v=1750145406', 1, 0, '2025-06-17 14:30:06', '2025-07-08 15:24:29', NULL, NULL, NULL, NULL, NULL, NULL, 1.00, 0, 'BASSPRO-POWER-2'),
(29, 5, 'testpic', 'testpicccccc', 'testpic', '', 200.00, 'uploads/product/testproducts1.jpg?v=1750821164', 1, 0, '2025-06-25 09:56:39', '2025-06-26 14:00:13', NULL, NULL, NULL, NULL, NULL, NULL, 0.50, 1, 'TEST-TOY-KIDS'),
(30, 5, '', 'Test', '', '', 100.00, NULL, 1, 0, '2025-06-30 09:37:04', '2025-07-23 13:22:05', NULL, NULL, NULL, NULL, NULL, NULL, 1000.00, 0, '1111'),
(31, 8, 'MINE', 'เหลี่ยม', 'เหลี่ยม', '', 1000.00, 'uploads/product/minecraft-icon-0.png?v=1751947033', 1, 0, '2025-07-08 10:57:13', '2025-07-08 10:57:13', 'percent', 3, 970, 30.00, 30.00, 30.00, 500.00, 0, 'MINE'),
(33, 23, 'SPYxFAMILY', 'SPYxFAMILY เล่ม 9-14 (แพ็คชุด)', 'รายละเอียดสินค้า\r\nไฮไลท์\r\nสายลับสุดลับต้องสร้างครอบครัวปลอมเพื่อภารกิจสำคัญ แต่ลูกสาวดันอ่านใจได้ ส่วนภรรยาก็เป็นนักฆ่า! เมื่อทุกคนต่างปิดบังตัวตน ความฮาและความลุ้นระทึกจึงเกิดขึ้นไม่หยุด!\r\nรายละเอียด\r\nสุดยอดสปาย &lt;สนธยา&gt; ได้รับคำสั่งให้สร้าง “ครอบครัว” เพื่อลอบเข้าไปในโรงเรียนชื่อดัง แต่ “ลูกสาว” ที่เขาได้พบดันเป็นผู้มีพลังจิตอ่านใจคน! “ภรรยา” เป็นมือสังหาร!?\r\n\r\nโฮมคอเมดี้สุดฮาของครอบครัวปลอมๆที่ต่างฝ่ายต่างปกปิดตัวจริงของกันและกัน ที่ต้องเผชิญหน้ากับการสอบเข้าและปกป้องโลก!!', '', 610.00, 'uploads/product/hznn7keo.png?v=1752805394', 1, 0, '2025-07-18 09:20:38', '2025-07-18 09:32:26', 'amount', 411, 199, 20.00, 20.00, 10.00, 200.00, 0, 'SF-9-14'),
(34, 26, 'เทคนิคตรัง', 'สมุดโน๊ตสีดำ', 'สมุดโน๊ตสีดำ 40 หน้า', '', 20.00, NULL, 1, 0, '2025-07-22 18:40:43', '2025-07-22 18:40:43', 'percent', 5, 19, NULL, NULL, NULL, 500.00, 0, 'NTE-001'),
(35, 24, 'DOG', 'DOG', 'DOG', '', 100000.00, 'uploads/product/e8d9faef1c23e3d2fb00c8d3262dcdd1.jpg?v=1755743510', 1, 0, '2025-08-21 09:31:50', '2025-08-21 09:31:50', NULL, NULL, NULL, 10.00, 10.00, 10.00, 10000.00, 0, 'DOG001'),
(36, 24, 'Sony', 'Sony ZV-E10 kit 16-50mm. zve10 มือ1 ประกันศูนย์ แถมเม็ม32gb ฟิล์มกันรอย กระเป๋า', 'Sony ZV-E10 + 16-50mm kit  (ประกันศูนย์ไทย)\r\n\r\nสินค้าใหม่ กล่องซีล \r\n\r\nรับประกันศูนย์ Sony ประเทศไทย 1 ปี\r\n\r\nแถมเม็ม32gb ฟิล์มกันรอย กระเป\r\n\r\n\r\n\r\nเซนเซอร์ APS-C 24 ล้านพิกเซล\r\n\r\nเลนส์ kit Sony 16-50mm f/3.5-5.6\r\n\r\nXAVC-S 4k 30p 100mbps\r\n\r\nFHD 120p\r\n\r\nกันสั่น 2 โหมด Standard/Active\r\n\r\nมีโหมด Auto exposure และ face priority\r\n\r\nโหมด Rroduct Showcase\r\n\r\nปุ่ม Bokeh Switch\r\n\r\n\r\n\r\nกล้องสาย Vlog ที่ขยับมาใช้เซนเซอร์ APS-C เปลี่ยนเลนส์ได้ เซนเซอร์ APS-C 24 ล้านพิกเซล เลนส์ kit Sony 16-50mm f/3.5-5.6 XAVC-S 4k 25p 100mbps FHD 100p กันสั่น 2 โหมด Standard/Active มีโหมด Auto exposure และ face priority โหมด Product Showcase ปุ่ม Bokeh Switch มีปุ่มเฉพาะสำหรับสลับโหมด S&amp;Q/ภาพนิ่ง/วิดีโอ รองรับ USB-C streaming กับ PC แบบเสียบแล้วใช้ได้ทันที จอ LCD ฟลิบหมุนได้รอบทิศทาง ตัวบอดีคล้ายกับซีรีส์ A6xxx แต่ไม่มีช่องมองภาพ EVF\r\n\r\n\r\n\r\nกล้องเลนส์แบบเปลี่ยนได้สำหรับการทำ Vlog\r\n\r\nเซนเซอร์ Exmor™ CMOS25 ขนาดใหญ่ APS-C 24.2 ล้านเมกะพิกเซล\r\n\r\nไมโครโฟนแบบทิศทาง 3 แคปซูลพร้อมตัวตัดเสียงลม\r\n\r\nคุณสมบัติที่ออกแบบมาเพื่อ Vlogger – การตั้งค่า Product Showcase, สวิตช์โบเก้, ปุ่มภาพนิ่ง/ภาพยนตร์/S&amp;Q\r\n\r\nการเชื่อมต่อที่ยืดหยุ่นเพื่อการแชร์ที่ง่ายดาย\r\n\r\nประเภทเซนเซอร์\r\n\r\nเซนเซอร์ Exmor CMOS ชนิด APS-C (23.5 x 15.6 มม.)\r\n\r\nจำนวนพิกเซล (ที่ใช้งานจริงประมาณ 24.2 ล้านพิกเซล\r\n\r\nความไวแสง ISO (RECOMMENDED EXPOSURE INDEX)\r\n\r\n[ภาพนิ่ง] ISO 100-32000 (สามารถตั้งเลข ISO ตั้งแต่ ISO 50 จนถึง ISO 51200 ให้เป็นช่วง ISO แบบขยาย), AUTO (ISO 100-6400, เลือกขีดจำกัดล่างและขีดจำกัดบนได้), [ภาพยนตร์] เทียบเท่า ISO 100-32000, AUTO (ISO 100-6400, เลือกขีดจำกัดล่างและขีดจำกัดบนได้)\r\n\r\nระยะเวลาการใช้งานแบตเตอรี่ (ภาพนิ่ง) ประมาณ 440 ภาพ (จอภาพ LCD) (มาตรฐาน CIPA)26\r\n\r\nประเภทจอภาพ TFT ชนิด 7.5 ซม. (3.0-type)\r\n\r\n\r\n\r\nอุปกรณ์ภายในกล่อง\r\n\r\n\r\n\r\nSony ZV-E10 camera body\r\n\r\nSony E 16-50mm 3.5-5.6/PZ OSS lens\r\n\r\nShoulder strap\r\n\r\nWind screen for microphone\r\n\r\n1x Sony NP-FW50 battery\r\n\r\nUSB-A to USB-C cable\r\n\r\nAC adapter\r\n\r\nStartup guide and documentation\r\n\r\n\r\n\r\n⚠️⚠️เงื่อนไขในการรับประกัน⚠️', '', 27990.00, 'uploads/product/Screenshot 2025-08-22 131850.png?v=1755843758', 1, 0, '2025-08-22 13:22:38', '2025-08-22 13:22:38', 'amount', 2990, 25000, 20.00, 20.00, 20.00, 1000.00, 0, 'SNZV-E10'),
(37, 24, 'Strawberry Tuesdae', '💛หมอนพิมพ์ลาย3Dรูปทุเรียนหมอนทองลูกโตๆใบใหญ่มากกก✨', '💛หมอนพิมพ์ลาย3Dรูปทุเรียนหมอนทองลูกโตๆใบใหญ่มากกก✨ ขนาด 40*60 เซนติเมตร\r\n\r\nพิมพ์ลายชัด สีสวย สด สามารถซักทำความสะอาดได้ \r\n\r\n\r\n\r\n✨ผลิตจากผ้าและใยเกรดดี ไม่เก็บฝุ่น สามารถซักทำความสะอาดได้บ่อยครั้ง ใยคืนตัวไม่เป็นก้อน\r\n\r\n✨ภาพถ่ายสินค้าจริง (รับสินค้าเองจากโรงงาน)\r\n\r\n✨สินค้าทุกชิ้นสามารถจัดส่งแบบเก็บปลายทาง(COD) ได้ \r\n\r\n✨มีบริการเขียนการ์ดฟรี 💌 สำหรับให้เป็นของขวัญ \r\n\r\nเพื่อน คนรัก คนสนิท โดยไม่มีราคาขั้นต่ำ (รบกวนทักแชท)\r\n\r\n\r\n\r\n🚛การจัดส่ง shipping\r\n\r\n•ร้านจัดส่งเฉพาะวันจันทร์-อาทิตย์ (หยุดวันพุธ) \r\n\r\nตัดรอบเวลา 10.00น.\r\n\r\n•ทุกวันจันทร์-อาทิตย์ สั่งสินค้าก่อน 10.00น. จะจัดส่งภายในวันนั้นเลย แต่ถ้าสั่งเลยจาก 10.00น.ร้านจะจัดส่งในวันถัดไป นับจากวันที่สั่งซื้อ\r\n\r\n•หากลูกค้าสั่งซื้อสินค้าในวันอังคารหลัง 10.00น.หรือวันพุธ ร้านจะจัดส่งสินค้าในวันพฤหัสบดี\r\n\r\n\r\n\r\n📬หากต้องการติดต่อ สอบถามพูดคุย สามารถกดปุ่ม แชทเลย พูดคุย 24 ช.ม. สามารถทิ้งข้อความไว้ ทางร้านจะรีบตอบกลับโดยเร็วที่สุดค่ะ 👍🏻😊\r\n\r\n𝗦𝘁𝗿𝗮𝘄𝗯𝗲𝗿𝗿𝘆 𝗧𝘂𝗲𝘀𝗱𝗮𝗲 ยินดีให้บริการ ! 💕', '', 149.00, 'uploads/product/Screenshot 2025-08-22 134024.png?v=1755844981', 1, 0, '2025-08-22 13:43:01', '2025-08-22 13:43:01', NULL, NULL, NULL, 60.00, 50.00, 70.00, 500.00, 0, 'TR01'),
(38, 24, 'AUV', '[ พร้อมส่ง ] ร่มกันแดด กันยูวีUV ร่มกันฝน สีพื้นไม่มีลาย แบบพับ 3 ตอน', '☂️พร้อมส่งทุกสีค่า ร่มเป็นสีพื้นไม่มีลาย\r\n\r\n▶️ร่มพับ 3 ตอน พับเก็บได้ง่าย \r\n\r\n✔️กันแดด กันฝน กันUVค่า\r\n\r\n\r\n\r\n🌈ร้านเราส่งสินค้าทุกวันจันทร์-เสาร์\r\n\r\nหยุดวันอาทิตย์และวันนักขัตฤกษ์นะคะ\r\n\r\n🚚ตัดรอบส่ง 15.00น. สั่งหลังจากนั้นส่งวันถัดไปค่ะ\r\n\r\n\r\n\r\nสินค้าทุกรายการในร้านพร้อมส่งจากกรุงเทพค่ะ\r\n\r\n\r\n\r\n▶️หากสินค้ามีปัญหา/ชำรุด/เสียหาย/ผิดแบบ\r\n\r\nลูกค้าทักหาแอดมินเพื่อช่วยแก้ไขปัญหาก่อนกดดาวนะคะ🙏\r\n\r\n\r\n\r\n🚩เงื่อนไขการรับเคลมสินค้า🚩\r\n\r\n-ลูกค้าถ่ายวิดีโอ รูปภาพสินค้าเมื่อแกะพัสดุ\r\n\r\n-ห้ามดัดแปลงสภาพสินค้าเช่น แกะ ถอดเปลี่ยนสินค้า \r\n\r\nในกรณีนี้ทางร้านขอไม่รับเคลมหรือเปลี่ยนนะคะ', '', 55.00, 'uploads/product/Screenshot 2025-08-22 134931.png?v=1755845480', 1, 0, '2025-08-22 13:51:20', '2025-08-22 13:51:20', NULL, NULL, NULL, 30.00, 60.00, 30.00, 200.00, 0, 'AUV001'),
(39, 24, 'CIVAGO', 'CIVAGO（26oz） แก้วกาแฟสแตนเลสซับเซรามิกพร้อมฝาปิดขวดสูญญากาศสามารถเก็บความร้อนและความเย็น', '• ซับในเซรามิค\r\n\r\n• สแตนเลส 304\r\n\r\n• ฉนวนสุญญากาศ\r\n\r\n• หลักฐานการรั่วไหล\r\n\r\n• ปลอดสาร Bpa\r\n\r\n• ร้อน 24 ชั่วโมง• ร้อน 12 ชั่วโมง\r\n\r\n• ทนทาน\r\n\r\n• สะดวกในการใช้งาน', '', 999.00, 'uploads/product/Screenshot 2025-08-22 135352.png?v=1755845838', 1, 0, '2025-08-22 13:57:18', '2025-08-22 13:57:18', 'amount', 610, 389, 22.00, 22.00, 30.00, 600.00, 0, 'CIVAGO1001'),
(40, 24, '海苔脆片', '🥗สาหร่ายทะเลปรุงรส (100ซองมี200ชิ้น) สาหร่ายซูชิ สาหร่ายห่อข้าว เด็กๆอร่อยถูกใจ 海苔脆片', '🥗สาหร่ายทะเลปรุงรส 100ซอง สาหร่ายซูชิ สาหร่ายห่อข้าว เด็กๆอร่อยถูกใจ 海苔脆片\r\n\r\n\r\n\r\nขนาด: 100ซอง (1ซองมี2แผ่น)\r\n\r\n\r\n\r\n#สาหร่ายทะเลปรุงรส #สาหร่ายซูชิ #สาหร่ายห่อข้าว #海苔脆片\r\n\r\n\r\n\r\n---------------------------------------------------------\r\n\r\n\r\n\r\n‼️หากสินค้ามีปัญหา‼️\r\n\r\nทักแชทหาร้านก่อน ร้านยินดีช่วยรับผิดชอบและแก้ไขปัญหาพบเจอค่ะ อย่าเพิ่งรีวิวให้ดาวนะคะ 🥰\r\n\r\n\r\n\r\n👩🏻‍💻ร้านเรามีแอดมินคอยตอบแชททุกวัน เวลา 08.00-18.00 ค่ะ\r\n\r\n\r\n\r\n🛑หลังจากได้รับพัสดุ รบกวนถ่ายวิดีโอตอนแกะพัสดุด้วยนะคะ กรณีมีสินค้าผิด หรือมีปัญหาจะได้นำหลักฐานมาเครมได้ ขอบคุณค่ะ  🙏🏼🙏🏼', '', 100.00, 'uploads/product/Screenshot 2025-08-22 140352.png?v=1755846346', 1, 0, '2025-08-22 14:05:46', '2025-08-22 14:05:46', 'percent', 45, 55, 60.00, 60.00, 60.00, 600.00, 0, 'SW001'),
(41, 24, '', 'ขนมปี๊บ ขนมปังปี๊บ ตราศรีกรุง SK ปี๊บเล็ก 400 กรัม - 1.2 กิโล อ่านก่อนกดสั่งซื้อ', 'ขนมปังปี๊บ ปี๊บเล็กบรรจุ 1.2 กก. อ่านก่อนกดสั่งซื้อ\r\n\r\n\r\n\r\n***กรุณาอ่านก่อนสั่งซื้อ  สินค้านี้งดคืน งดเคลมทุกกรณี หากสั่งซื้อแล้วถือว่าเป็นไปตามเงื่อนไขนี้ 🙏🏻*** \r\n\r\n1. ขนมเป็นสินค้าที่สามารถแตกหักได้ระหว่างการขนส่งไม่ว่าจะแพ็คป้องกันอย่างไรก็สามารถกระทบกระเทือนได้ กรุณาพิจารณาก่อนสั่งซื้อ และต้องยอมรับสภาพสินค้าที่ได้รับ โดยเฉพาะขนมแตกหรือกล่องบุบ ทางร้านงดคืน งดเคลมทุกกรณี หากคุณลูกค้านำไปจำหน่ายต่อกรุณาสั่งซื้อชนิดสินค้าที่เหมาะสมค่ะ เนื่องจากมีความเสี่ยงสูงที่ขนมจะแตกหรือหักได้จากการขนส่งซึ่งอยู่เหนือการควบคุม ทางร้านจึงงดคืน/งดเคลมค่ะ\r\n\r\n2. บรรจุภัณฑ์มีวันผลิตและหมดอายุระบุ ทางร้านจำหน่ายสินค้าที่มีอายุการเก็บรักษาไม่ต่ำกว่า 6 เดือน ซึ่งขนมมีอายุ 1 ปีหลังจากวันผลิต ทางร้านจึงงดคืน/งดเคลมเนื่องจากขนมเหม็นหืน รสชาติผิดเพี้ยน\r\n\r\n3. ขนมงดรับคืนในเรื่องของรสชาติทุกรณี เนื่องจากความชอบรสชาติ ของแต่ละท่านไม่เหมือนกัน\r\n\r\n#ขนมปังปี๊บ\r\n\r\n#ขนมปี๊บ\r\n\r\n#ขนม#ปี๊บ\r\n\r\n#ทานเล่น\r\n\r\n#ของฝาก\r\n\r\n#Vfoods\r\n\r\n#ศรีกรุง\r\n\r\n#ของฝาก\r\n\r\n#ขาไก่', '', 169.00, 'uploads/product/Screenshot 2025-08-22 141022.png?v=1755846741', 1, 0, '2025-08-22 14:12:21', '2025-08-22 14:12:21', NULL, NULL, NULL, 60.00, 60.00, 60.00, 1200.00, 0, 'SN0001');

-- --------------------------------------------------------

--
-- Table structure for table `product_shipping`
--

CREATE TABLE `product_shipping` (
  `product_id` int NOT NULL,
  `shipping_methods_id` int NOT NULL,
  `price` float DEFAULT '0',
  `enabled` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'เครื่องเล่น', 'เหล่าเครื่องเล่นต่าง ๆ มากมาย', 0, 1, 0, '2025-07-16 11:21:36', '2025-07-16 16:03:21'),
(2, 'ของเล่น', 'ของเล่นต่าง ๆ มากมาย', 0, 1, 0, '2025-07-16 11:32:02', '2025-07-16 16:03:50'),
(3, 'หนังสือ', 'หนังสือต่าง ๆ มากมาย', 0, 1, 0, '2025-07-16 13:03:25', '2025-07-16 16:06:23'),
(4, 'สื่อ', 'รวบรวมสื่อต่าง ๆ มากมาย', 0, 1, 0, '2025-07-16 14:01:10', '2025-07-16 16:06:56'),
(5, 'ชุดประเมิน', 'ชุดประเมินความรู้ทั้งหลาย', 0, 1, 0, '2025-07-16 16:07:20', '2025-07-16 16:07:20'),
(6, 'อื่น ๆ', 'อื่น ๆ', 1, 1, 0, '2025-07-16 16:07:33', '2025-08-25 10:40:05'),
(7, 'ทดสอบระบบ', 'ทดสอบระบบ', 0, 1, 0, '2025-07-17 13:38:04', '2025-07-17 13:38:04'),
(8, 'ทดสอบระบบ/', 'ทดสอบระบบ/', 0, 1, 0, '2025-07-17 13:38:16', '2025-07-17 13:38:16'),
(9, 'ทดสอบระบบ-', 'ทดสอบระบบ-', 0, 1, 0, '2025-07-17 13:38:26', '2025-07-17 13:38:26'),
(10, 'ทดสอบระบบภ', 'ทดสอบระบบภ', 0, 1, 0, '2025-07-17 13:38:41', '2025-07-17 13:38:41'),
(11, 'ทดสอบลบ', '', 0, 1, 0, '2025-07-22 09:44:31', '2025-07-22 09:44:31'),
(12, 'สมุด', '', 0, 1, 0, '2025-07-22 18:37:35', '2025-07-22 18:37:35'),
(13, 'ทดสอบ', 'ทดสอบ', 0, 1, 0, '2025-07-30 15:06:32', '2025-07-30 15:06:32');

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
(10, 10, 'โปรโมชั่น 8.8 !', 'พบกับส่วนลดพิเศษทั้งร้านค้าต้อนรับ 8.8 ! ลดหนัก ลดกันไปเลย 30 % !', 'uploads/promotions/promo_689db31d6e8fb_1755165469.png?v=1755165469', 'percent', 30, 300, '2025-08-06 09:18:00', '2025-08-21 09:18:00', 0, 0, '2025-08-06 09:18:27', '2025-08-27 11:31:27'),
(11, 10, 'ลดล้างสต๊อก', 'ลดล้างสต๊อก\r\nลดล้างสต๊อกลดล้างสต๊อกลดล้างสต๊อก', 'uploads/promotions/promo_689daed0bca7e_1755164368.png?v=1755164368', 'percent', 90, 0, '2025-08-06 16:01:00', '2025-08-07 16:01:00', 0, 0, '2025-08-06 16:01:33', '2025-08-27 11:31:27'),
(12, 10, 'ส่งฟรีไม่มีขั้นต่ำ', 'ส่งฟรีไม่มีขั้นต่ำ', 'uploads/promotions/promo_689db326cfea5_1755165478.png?v=1755165478', 'free_shipping', 0, 0, '2025-08-07 13:56:00', '2025-08-21 13:56:00', 0, 0, '2025-08-07 13:56:10', '2025-08-27 11:31:27'),
(13, 10, 'ลดราคา 100 บาท', 'สั่งซื้อครบ 20 บาท ลดเลย 100 บาท !', 'uploads/promotions/promo_689db32dcf831_1755165485.png?v=1755165485', 'fixed', 100, 20, '2025-08-08 09:31:00', '2025-08-22 09:31:00', 0, 0, '2025-08-08 09:31:56', '2025-08-27 11:31:27'),
(14, 10, 'ส่งฟรีขั้นต่ำ 200 บาท', 'ส่งฟรีขั้นต่ำ 200 บาท', 'uploads/promotions/promo_689db33b5db62_1755165499.png?v=1755165499', 'free_shipping', 0, 200, '2025-08-08 09:49:00', '2025-08-22 09:49:00', 0, 0, '2025-08-08 09:49:49', '2025-08-27 11:31:27'),
(15, 10, 'ลดหนังสือนิทาน การ์ตูน ฯลฯ ทั้งร้าน !', 'ลดหนังสือนิทาน การ์ตูน มังงะ ทั้งร้าน ! ลดหนัก จัดหนักกันไปเลย ! ลดถึง 20 % !', 'uploads/promotions/promo_689db2eceddb6_1755165420.png?v=1755165420', 'percent', 20, 60, '2025-08-14 16:46:00', '2025-08-30 16:46:00', 1, 0, '2025-08-14 16:46:56', '2025-08-14 16:57:00'),
(16, 10, 'ลดเลยทันที 40% !!!', 'ลดหนัก ๆ จัดกันจุก ๆ ลดทันที 40% !!!', 'uploads/promotions/promo_68a3ebac39e7f_1755573164.png?v=1755573164', 'percent', 40, 0, '2025-08-19 10:12:00', '2025-10-01 10:12:00', 1, 0, '2025-08-19 10:12:44', '2025-08-26 10:51:42');

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
(2, 'ทดสอบโปรโมชั่น', 'ทดสอบ', 1, 0, '2025-07-30 08:13:12', '2025-07-30 08:38:15'),
(3, 'ทดสอบ2', '', 1, 0, '2025-08-01 02:10:13', '2025-08-01 02:10:13'),
(4, 'ทดสอบ3', '', 1, 0, '2025-08-01 04:29:50', '2025-08-01 04:29:50'),
(5, 'ทดสอบ4', 'ทดสอบ4', 1, 0, '2025-08-01 04:30:01', '2025-08-01 04:30:01'),
(6, 'ทดสอบ5', 'ทดสอบ5', 1, 0, '2025-08-01 04:31:20', '2025-08-01 04:31:20'),
(7, 'ทดสอบ6', 'ทดสอบ6', 1, 0, '2025-08-01 04:36:29', '2025-08-01 04:36:29'),
(8, 'ทดสอบ7', 'ทดสอบ7', 1, 0, '2025-08-01 04:36:35', '2025-08-01 04:36:35'),
(9, 'ทดสอบ8', 'ทดสอบ8', 1, 0, '2025-08-01 04:36:44', '2025-08-01 04:36:44'),
(10, 'โปรโมชั่นยอดฮิท !', 'โปรโมชั่นยอดฮิท!', 1, 0, '2025-08-06 02:17:40', '2025-08-06 02:17:40');

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
(41, 11, 23, 1, 0, '2025-08-08 09:32:26', '2025-08-08 09:32:26'),
(43, 11, 22, 1, 0, '2025-08-08 09:32:26', '2025-08-08 09:32:26'),
(44, 11, 21, 1, 0, '2025-08-08 09:32:26', '2025-08-08 09:32:26'),
(45, 11, 26, 1, 0, '2025-08-08 09:32:26', '2025-08-08 09:32:26'),
(46, 11, 24, 1, 0, '2025-08-08 09:32:26', '2025-08-08 09:32:26'),
(47, 11, 20, 1, 0, '2025-08-08 09:32:26', '2025-08-08 09:32:26'),
(48, 11, 31, 1, 0, '2025-08-08 09:32:26', '2025-08-08 09:32:26'),
(49, 11, 34, 1, 0, '2025-08-08 09:32:26', '2025-08-08 09:32:26'),
(50, 13, 27, 1, 0, '2025-08-08 09:32:39', '2025-08-08 09:32:39'),
(51, 13, 14, 1, 0, '2025-08-08 09:32:39', '2025-08-08 09:32:39'),
(52, 14, 30, 1, 0, '2025-08-08 09:50:05', '2025-08-08 09:50:05'),
(53, 14, 25, 1, 0, '2025-08-08 09:50:05', '2025-08-08 09:50:05'),
(55, 10, 35, 1, 0, '2025-08-21 14:55:54', '2025-08-21 14:55:54'),
(56, 10, 33, 1, 0, '2025-08-21 14:55:54', '2025-08-21 14:55:54'),
(57, 16, 41, 1, 0, '2025-08-26 10:50:47', '2025-08-26 10:50:47'),
(58, 16, 38, 1, 0, '2025-08-26 10:50:47', '2025-08-26 10:50:47'),
(59, 16, 39, 1, 0, '2025-08-26 10:50:47', '2025-08-26 10:50:47'),
(60, 16, 36, 1, 0, '2025-08-26 10:50:47', '2025-08-26 10:50:47'),
(61, 16, 37, 1, 0, '2025-08-26 10:50:47', '2025-08-26 10:50:47'),
(62, 16, 40, 1, 0, '2025-08-26 10:50:47', '2025-08-26 10:50:47');

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
(35, 16, 19, 54, 10171.20, 5, '2025-08-26 03:53:08'),
(36, 10, 19, 55, 119.40, 1, '2025-08-26 06:17:05'),
(37, 13, 19, 56, 100.00, 1, '2025-08-26 06:17:48'),
(38, 16, 19, 57, 20000.00, 1, '2025-08-27 02:05:21');

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
  `is_active` tinyint(1) DEFAULT '1',
  `delete_flag` tinyint(1) DEFAULT '0',
  `volumetric_divider` int DEFAULT '5000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_methods`
--

INSERT INTO `shipping_methods` (`id`, `provider_id`, `name`, `description`, `cost`, `cod_enabled`, `is_active`, `delete_flag`, `volumetric_divider`) VALUES
(3, 2, 'Flash Express', 'บริการราคาประหยัดทั่วประเทศ', 40.00, 1, 1, 0, 5000),
(7, 3, 'ไปรษณีย์ไทย', 'สามารถเก็บเงินปลายทางได้', 40.00, 1, 1, 0, 5000),
(24, 4, 'Standard', 'Standard', 45.00, 1, 1, 0, 5000);

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
(85, 24, 0, 1000, 45.00);

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
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` float(12,2) NOT NULL DEFAULT '0.00',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_list`
--

INSERT INTO `stock_list` (`id`, `product_id`, `code`, `quantity`, `date_created`, `date_updated`) VALUES
(13, 14, 'TEST200001', 100.00, '2025-06-12 10:51:44', '2025-06-12 10:51:44'),
(16, 21, 'H100001', 1000.00, '2025-06-17 13:44:15', '2025-06-17 13:44:15'),
(17, 22, 'H200001', 1000.00, '2025-06-17 13:49:18', '2025-06-17 13:49:18'),
(19, 24, 'P100001', 1000.00, '2025-06-17 14:22:29', '2025-06-17 14:22:29'),
(21, 26, 'H400001', 1000.00, '2025-06-17 14:27:41', '2025-06-17 14:27:41'),
(23, 27, '101010', 220.00, '2025-06-19 14:17:20', '2025-06-19 14:17:20'),
(24, 25, 'H100010', 1000.00, '2025-06-24 16:52:16', '2025-06-24 16:52:16'),
(25, 23, 'S100010', 1000.00, '2025-06-24 16:52:40', '2025-06-24 16:52:40'),
(27, 29, 'TP1001', 100.00, '2025-06-25 09:57:03', '2025-06-25 09:57:03'),
(28, 30, '1111', 111.00, '2025-06-30 09:37:48', '2025-06-30 09:37:48'),
(29, 20, '12121', 1000.00, '2025-06-30 11:30:45', '2025-06-30 11:30:45'),
(31, 33, 'SF-00001', 100.00, '2025-07-18 09:24:01', '2025-07-18 09:24:01'),
(32, 34, 'NTE1-001', 1000.00, '2025-07-22 18:41:19', '2025-07-22 18:41:19'),
(34, 35, 'DOG00001', 1000.00, '2025-08-21 09:32:07', '2025-08-21 09:32:07'),
(35, 36, 'SNYZVE1010001', 1000.00, '2025-08-22 13:23:08', '2025-08-22 13:23:08'),
(36, 37, 'TR1001', 1000.00, '2025-08-22 13:47:27', '2025-08-22 13:47:27'),
(37, 38, 'AUV10001', 1000.00, '2025-08-22 13:52:47', '2025-08-22 13:52:47'),
(39, 40, 'SW1001', 1000.00, '2025-08-22 14:06:10', '2025-08-22 14:06:10'),
(40, 41, 'SN1001', 1000.00, '2025-08-22 14:13:21', '2025-08-22 14:13:21'),
(41, 39, 'CIVAGO1001', 1000.00, '2025-08-26 10:51:07', '2025-08-26 10:51:07');

-- --------------------------------------------------------

--
-- Table structure for table `stock_out`
--

CREATE TABLE `stock_out` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `stock_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'name', 'MSG.com'),
(6, 'short_name', ''),
(11, 'logo', 'uploads/logo.png?v=1747713664'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/cover.png?v=1750148058'),
(17, 'phone', '075-051070'),
(18, 'mobile', '0828398430 / 094563212222 '),
(19, 'email', 'AdminFIGSTR@gmail.com'),
(20, 'address', '21 ม.5 ถ.เพชรเกษม ต.นาท่ามเหนือ อ.เมือง จ.ตรัง 92190'),
(21, 'office_hours', 'วันจันทร์-วันเสาร์ เวลา 08.30น.-17.00น.(หยุดวันนักขัตฤกษ์)'),
(22, 'Line', 'https://line.me'),
(23, 'Facebook', 'https://fb.com/kirution2079/'),
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
(1, 'Adminstrator', '', 'Admin', 'admin', 'fcea920f7412b5da7be0cf42b8c93759', 'uploads/avatars/1.png?v=1649834664', NULL, 1, '2021-01-20 14:02:37', '2024-01-07 11:27:23'),
(8, 'Claire', '', 'Blake', 'cblake', 'cd74fae0a3adf459f73bbf187607ccea', 'uploads/avatars/8.png?v=1675047323', NULL, 2, '2023-01-30 10:55:23', '2023-01-30 10:55:23'),
(9, 'StaffN', '', 'ig', 'staff1', '81dc9bdb52d04dc20036dbd8313ed055', 'uploads/avatars/9.png?v=1747646517', NULL, 2, '2025-05-19 16:21:57', '2025-07-22 17:02:12');

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
-- Indexes for table `coupon_code_customer`
--
ALTER TABLE `coupon_code_customer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `coupon_code_id` (`coupon_code_id`);

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
-- Indexes for table `product_shipping`
--
ALTER TABLE `product_shipping`
  ADD PRIMARY KEY (`product_id`,`shipping_methods_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=299;

--
-- AUTO_INCREMENT for table `category_list`
--
ALTER TABLE `category_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `coupon_code_customer`
--
ALTER TABLE `coupon_code_customer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupon_code_list`
--
ALTER TABLE `coupon_code_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `coupon_code_products`
--
ALTER TABLE `coupon_code_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `coupon_code_usage_logs`
--
ALTER TABLE `coupon_code_usage_logs`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `customer_list`
--
ALTER TABLE `customer_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `order_list`
--
ALTER TABLE `order_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `product_image_path`
--
ALTER TABLE `product_image_path`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `product_list`
--
ALTER TABLE `product_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `product_type`
--
ALTER TABLE `product_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `promotions_list`
--
ALTER TABLE `promotions_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `promotion_category`
--
ALTER TABLE `promotion_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `promotion_products`
--
ALTER TABLE `promotion_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `promotion_usage_logs`
--
ALTER TABLE `promotion_usage_logs`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `shipping_prices`
--
ALTER TABLE `shipping_prices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `shipping_providers`
--
ALTER TABLE `shipping_providers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stock_list`
--
ALTER TABLE `stock_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `stock_out`
--
ALTER TABLE `stock_out`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- Constraints for table `coupon_code_customer`
--
ALTER TABLE `coupon_code_customer`
  ADD CONSTRAINT `coupon_code_customer_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_code_customer_ibfk_2` FOREIGN KEY (`coupon_code_id`) REFERENCES `coupon_code_list` (`id`) ON DELETE CASCADE;

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

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
