-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql206.byetcluster.com
-- Generation Time: Mar 27, 2025 at 09:38 PM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_38379669_webstock`
--

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL,
  `sell_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `issue_date` datetime DEFAULT current_timestamp(),
  `payment_status` enum('PENDING','PAID','CANCELLED') DEFAULT 'PENDING',
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_contact` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_id`, `sell_id`, `invoice_number`, `total_price`, `issue_date`, `payment_status`, `customer_name`, `customer_contact`, `created_at`, `updated_at`) VALUES
(1, 37, 'INV-1741518692', '450.00', '2025-03-09 04:11:32', 'PAID', '', '', '2025-03-09 11:11:32', '2025-03-09 11:11:32'),
(2, 38, 'INV-1741519118', '120.00', '2025-03-09 04:18:38', 'PAID', '', '', '2025-03-09 11:18:38', '2025-03-09 11:18:38'),
(3, 39, 'INV-1741519366', '12.00', '2025-03-09 04:22:46', 'PAID', '', '', '2025-03-09 11:22:46', '2025-03-09 11:22:46'),
(4, 40, 'INV-1741519861', '30.00', '2025-03-09 04:31:01', 'PAID', '', '', '2025-03-09 11:31:01', '2025-03-09 11:31:01'),
(5, 41, 'INV-1741870634', '95.00', '2025-03-13 05:57:14', 'PAID', '', '', '2025-03-13 12:57:14', '2025-03-13 12:57:14');

-- --------------------------------------------------------

--
-- Table structure for table `out_product_details`
--

CREATE TABLE `out_product_details` (
  `detail_id` int(11) NOT NULL,
  `out_id` int(11) NOT NULL,
  `product_code` varchar(50) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `expiration_date` date DEFAULT NULL,
  `product_model` varchar(100) DEFAULT NULL,
  `production_date` date DEFAULT NULL,
  `shelf_life` int(11) DEFAULT NULL,
  `sticker_color` varchar(50) DEFAULT NULL,
  `reminder_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `sender_code` varchar(50) DEFAULT NULL,
  `sender_company` varchar(255) DEFAULT NULL,
  `recorder` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `out_date` date DEFAULT curdate(),
  `position` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `out_stock_product`
--

CREATE TABLE `out_stock_product` (
  `out_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `out_date` datetime NOT NULL,
  `out_username` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `bank_account` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `qr_data` text NOT NULL,
  `qr_code_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_code` varchar(50) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_model` varchar(100) DEFAULT NULL,
  `production_date` date DEFAULT NULL,
  `shelf_life` int(11) DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `sticker_color` varchar(50) DEFAULT NULL,
  `reminder_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `sender_code` varchar(50) DEFAULT NULL,
  `sender_company` varchar(255) DEFAULT NULL,
  `recorder` varchar(255) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `position` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_code`, `product_name`, `product_model`, `production_date`, `shelf_life`, `expiration_date`, `sticker_color`, `reminder_date`, `received_date`, `quantity`, `unit`, `unit_cost`, `sender_code`, `sender_company`, `recorder`, `unit_price`, `category`, `created_at`, `updated_at`, `status`, `position`) VALUES
(316, '001', 'test', '12', '2025-03-04', 10, '2025-03-06', 'หมดอายุเดือน 1', NULL, '2025-03-04', 84, 'ซอง', '20.00', '17', 'test com', 'test', '30.00', 'ของใช้', '2025-03-04 11:35:04', '2025-03-09 11:31:01', 'active', ''),
(317, '8859064100292', 'ถุง 6x11 บาง', '', '0000-00-00', 0, '0000-00-00', 'ไม่มีวันหมดอายุ', '0000-00-00', '2025-03-23', 190, 'ห่อ', '40.00', '', '', 'pad', '40.00', 'ของใช้', '2025-03-04 15:37:09', '2025-03-09 08:45:58', 'active', 'B23'),
(318, '8859064100285', 'ถุง 6 x 14 หนา', '', '0000-00-00', 0, '0000-00-00', 'ไม่มีวันหมดอายุ', '0000-00-00', '2025-03-23', 500, 'ห่อ', '30.00', '', '', 'pad', '38.00', 'ของใช้', '2025-03-04 15:37:09', '2025-03-04 15:37:09', 'active', 'B22'),
(319, '8850360065353', 'ครีมอาบน้ำเดทตอลชมพู500มล.', '23B0018', '0000-00-00', 458, '2025-02-01', 'หมดอายุเดือน 2', '0000-00-00', '2025-03-23', 59, 'ขวด', '90.00', '', 'reckitt', 'pad', '95.00', 'ของใช้', '2025-03-04 15:37:09', '2025-03-13 12:57:14', 'active', 'D43'),
(320, '8851932464680', 'ซันไลน์ (แกลอน)', '699714545', '0000-00-00', 0, '2024-05-28', 'หมดอายุเดือน 5', '0000-00-00', '2025-03-23', 30, 'แกลอน', '150.00', '', 'Unilever', 'pad', '165.00', 'ของใช้', '2025-03-04 15:37:09', '2025-03-04 15:37:09', 'active', 'M42'),
(321, '8851932424318', 'บรีสแดง600ก.', '64771402', '0000-00-00', 0, '2024-12-28', 'หมดอายุเดือน 12', '0000-00-00', '2025-03-23', 100, 'ถุง', '40.00', '', '', 'pad', '50.00', 'ของใช้', '2025-03-04 15:37:09', '2025-03-04 15:37:09', 'active', 'M15'),
(322, '8850124033055E160325', 'คอฟฟี่เมท 200 ก. ', '42210523Y', '0000-00-00', 0, '2025-03-16', 'หมดอายุเดือน 3', '0000-00-00', '2025-03-23', 60, 'ห่อ', '50.00', '', 'เนสท์เล่', 'pad', '60.00', 'ขนม/เครื่องดื่ม', '2025-03-04 15:37:09', '2025-03-13 15:08:53', 'active', 'Q11'),
(323, '8850233210088', 'แป้งเย็นเภสัช 300 ก.', '', '0000-00-00', 1095, '2027-09-04', 'หมดอายุเดือน 9', '0000-00-00', '2025-03-23', 24, 'กระป๋อง', '75.00', '', '', 'pad', '85.00', 'ของใช้', '2025-03-04 15:37:09', '2025-03-04 15:37:09', 'active', 'D36'),
(324, '8851717049033E060325', 'ดัชมิลล์ผลไม้รวม180*4', '130238118', '0000-00-00', 0, '2025-03-06', 'หมดอายุเดือน 9', '0000-00-00', '2025-03-23', 12, 'แพ็ค', '28.00', '', '', 'pad', '35.00', 'ขนม/เครื่องดื่ม', '2025-03-04 15:37:09', '2025-03-23 02:59:15', 'active', 'A11'),
(325, '8850267117421', 'แลคตาซอย300*6', '2421', '0000-00-00', 303, '2025-07-24', 'หมดอายุเดือน 7', '0000-00-00', '2025-03-23', 300, 'แพ็ค', '40.00', '', 'แลคตาซอย', 'pad', '55.00', 'ขนม/เครื่องดื่ม', '2025-03-04 15:37:10', '2025-03-04 15:37:10', 'active', 'G26'),
(326, '88510281400476', 'นมไวตามิ้นขวด*6', '1252', '0000-00-00', 0, '2025-12-03', '', '0000-00-00', '2025-03-23', 120, 'แพ็ค', '70.00', '', 'กรีนสปอต', 'pad', '78.00', 'ขนม/เครื่องดื่ม', '2025-03-04 15:37:10', '2025-03-04 15:37:10', 'active', 'G33'),
(327, '8850250010968', 'รสดีหมู 800 กรัม', '23092025', '0000-00-00', 0, '2025-09-23', 'หมดอายุเดือน 9', '0000-00-00', '2025-03-23', 60, 'ห่อ', '95.00', '', 'อายิโนะโมะโต๊ะ', 'pad', '105.00', 'เครื่องปรุงรส', '2025-03-04 15:37:10', '2025-03-04 15:37:10', 'active', 'F41'),
(328, '8851613101392', 'กะทิอร่อยดี 1 ลิตร', '2FDLLX', '0000-00-00', 0, '2026-05-12', 'หมดอายุเดือน 5', '0000-00-00', '2025-03-23', 48, 'กล่อง', '75.00', '', 'ไทย อกริ ฟู้ดส์จำกัด', 'pad', '80.00', 'เครื่องปรุงรส', '2025-03-04 15:37:10', '2025-03-04 15:37:10', 'active', 'H23'),
(329, '8850124065407', 'แม็กกี้เล็ก100มล.', '41280501GA', '0000-00-00', 0, '2025-10-31', 'หมดอายุเดือน 10', '0000-00-00', '2025-03-23', 200, 'ขวด', '15.00', '', 'คิวพี', 'pad', '20.00', 'เครื่องปรุงรส', '2025-03-04 15:37:10', '2025-03-04 15:37:10', 'active', 'H15'),
(330, '8850058003728E040325E110325', 'น้ำจิ้มสุกี้ใหญ่830ก.', '', '0000-00-00', 730, '2025-03-11', 'หมดอายุเดือน 10', '0000-00-00', '2025-03-23', 60, 'ขวด', '90.00', '', '', 'pad', '95.00', 'เครื่องปรุงรส', '2025-03-04 15:37:10', '2025-03-11 15:00:18', 'active', 'A01'),
(331, '8850372000557', 'วุ้นเส้น80ก.', 'P16', '0000-00-00', 730, '2026-08-21', 'หมดอายุเดือน 8', '0000-00-00', '2025-03-23', 50, 'ห่อ', '8.00', '', 'สิทธินันท์', 'pad', '14.00', 'อาหาร', '2025-03-04 15:37:10', '2025-03-04 15:37:10', 'active', 'F14'),
(332, '8850987128325', 'มาม่าน้ำข้น กล่อง', '499236', '0000-00-00', 181, '2025-05-30', 'หมดอายุเดือน 5', '0000-00-00', '2025-03-23', 48, 'กล่อง', '148.00', '', 'สหพัฒนพิบูล', 'pad', '155.00', 'อาหาร', '2025-03-04 15:37:10', '2025-03-04 15:37:10', 'active', 'I46'),
(333, '8850144207887E080325', 'โจ๊กคัพไก่32ก*6ถ้วย', '', '0000-00-00', 0, '2025-03-08', 'หมดอายุเดือน 10', '0000-00-00', '2025-03-23', 48, 'แพ็ค', '85.00', '', 'Unilever', 'pad', '90.00', 'อาหาร', '2025-03-04 15:37:10', '2025-03-27 13:35:01', 'active', 'G71'),
(334, '885047705650324', 'อาหารแมวตัก', '', '0000-00-00', 0, '0000-00-00', '', '0000-00-00', '2025-03-23', 30, 'ถุง', '30.00', '', '', 'pad', '35.00', 'สัตว์เลี้ยง', '2025-03-04 15:37:10', '2025-03-04 15:37:10', 'active', 'C42'),
(335, '8855004004026', 'สีผสมอาหารส้มแดง', 'CM024112103', '0000-00-00', 1095, '2027-11-21', 'หมดอายุเดือน 11', '0000-00-00', '2025-03-23', 100, 'ซอง', '3.50', '', '', 'pad', '5.00', 'เครื่องปรุงรส', '2025-03-04 15:37:10', '2025-03-04 15:37:10', 'active', 'N12'),
(336, '8851959144169', 'น้ำส้มแฟนต้า1.25ลิตร', '141224', '0000-00-00', 0, '2024-12-14', 'หมดอายุเดือน 12', '0000-00-00', '2025-03-23', 3, 'ขวด', '23.00', '', '', 'pad', '27.00', 'ขนม/เครื่องดื่ม', '2025-03-04 15:37:10', '2025-03-04 15:37:10', 'active', 'L46'),
(337, '002E300325E190325', 'tt', '12', '2025-03-06', 10, '2025-03-19', 'ไม่มีวันหมดอายุ', '2025-03-07', '2025-03-08', 1, 'ซอง', '10.00', 'tt', 'tt', 'tt', '12.00', 'tt', '2025-03-05 00:52:47', '2025-03-19 15:46:04', 'active', 'Q11'),
(338, 'แ', 'แ', 'แ', '0000-00-00', 0, '0000-00-00', NULL, '0000-00-00', '2025-03-12', 1, 'แ', '0.00', '', '', '', '0.00', '', '2025-03-12 04:43:48', '2025-03-12 04:43:48', 'active', '');

-- --------------------------------------------------------

--
-- Table structure for table `sell_product`
--

CREATE TABLE `sell_product` (
  `sell_id` int(11) NOT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `sell_date` date DEFAULT NULL,
  `sell_username` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sell_product`
--

INSERT INTO `sell_product` (`sell_id`, `total_price`, `sell_date`, `sell_username`, `created_at`) VALUES
(34, '300.00', '2025-03-04', 'test', '2025-03-04 14:39:08'),
(35, '2100.00', '2025-03-04', 'test', '2025-03-04 14:40:30'),
(36, '400.00', '2025-03-09', 'test', '2025-03-09 11:45:58'),
(37, '450.00', '2025-03-09', 'test', '2025-03-09 14:11:32'),
(38, '120.00', '2025-03-09', 'test', '2025-03-09 14:18:38'),
(39, '12.00', '2025-03-09', 'test', '2025-03-09 14:22:46'),
(40, '30.00', '2025-03-09', 'test', '2025-03-09 14:31:01'),
(41, '95.00', '2025-03-13', 'pad', '2025-03-13 15:57:14');

-- --------------------------------------------------------

--
-- Table structure for table `sell_product_details`
--

CREATE TABLE `sell_product_details` (
  `detail_id` int(11) NOT NULL,
  `sell_id` int(11) DEFAULT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `product_model` varchar(100) DEFAULT NULL,
  `production_date` date DEFAULT NULL,
  `shelf_life` int(11) DEFAULT NULL,
  `sticker_color` varchar(50) DEFAULT NULL,
  `reminder_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `sender_code` varchar(50) DEFAULT NULL,
  `sender_company` varchar(255) DEFAULT NULL,
  `recorder` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `sell_date` date DEFAULT curdate(),
  `position` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sell_product_details`
--

INSERT INTO `sell_product_details` (`detail_id`, `sell_id`, `product_code`, `product_name`, `quantity`, `unit_price`, `expiration_date`, `product_model`, `production_date`, `shelf_life`, `sticker_color`, `reminder_date`, `received_date`, `unit`, `unit_cost`, `sender_code`, `sender_company`, `recorder`, `category`, `status`, `sell_date`, `position`) VALUES
(36, 34, '001', 'test', 10, '30.00', '2025-03-14', '12', '2025-03-04', 10, 'หมดอายุเดือน 1', NULL, '2025-03-04', 'ซอง', '20.00', '17', 'test com', 'test', 'ของใช้', 'SELL', '2025-03-04', 'A12'),
(37, 35, '001', 'test', 70, '30.00', '2025-03-14', '12', '2025-03-04', 10, 'หมดอายุเดือน 1', NULL, '2025-03-04', 'ซอง', '20.00', '17', 'test com', 'test', 'ของใช้', 'SELL', '2025-03-04', 'A12'),
(38, 36, '8859064100292', 'ถุง 6x11 บาง', 10, '40.00', '0000-00-00', '', '0000-00-00', 0, 'ไม่มีวันหมดอายุ', '0000-00-00', '2025-03-23', 'ห่อ', '40.00', '', '', 'pad', 'ของใช้', 'SELL', '2025-03-09', 'B23'),
(39, 37, '001', 'test', 15, '30.00', '2025-03-06', '12', '2025-03-04', 10, 'หมดอายุเดือน 1', NULL, '2025-03-04', 'ซอง', '20.00', '17', 'test com', 'test', 'ของใช้', 'SELL', '2025-03-09', ''),
(40, 38, '002', 'tt', 10, '12.00', '2025-03-13', '12', '2025-03-06', 10, 'ไม่มีวันหมดอายุ', '2025-03-07', '2025-03-08', 'ซอง', '10.00', 'tt', 'tt', 'tt', 'tt', 'SELL', '2025-03-09', 'tt'),
(41, 39, '002', 'tt', 1, '12.00', '2025-03-13', '12', '2025-03-06', 10, 'ไม่มีวันหมดอายุ', '2025-03-07', '2025-03-08', 'ซอง', '10.00', 'tt', 'tt', 'tt', 'tt', 'SELL', '2025-03-09', 'tt'),
(42, 40, '001', 'test', 1, '30.00', '2025-03-06', '12', '2025-03-04', 10, 'หมดอายุเดือน 1', NULL, '2025-03-04', 'ซอง', '20.00', '17', 'test com', 'test', 'ของใช้', 'SELL', '2025-03-09', ''),
(43, 41, '8850360065353', 'ครีมอาบน้ำเดทตอลชมพู500มล.', 1, '95.00', '2025-02-01', '23B0018', '0000-00-00', 458, 'หมดอายุเดือน 2', '0000-00-00', '2025-03-23', 'ขวด', '90.00', '', 'reckitt', 'pad', 'ของใช้', 'SELL', '2025-03-13', 'D43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `telegram_chat_id` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `created_at`, `telegram_chat_id`, `reset_token`, `reset_expiry`) VALUES
(17, 'test', 'thanawadee.titha@gmail.com', '$2y$10$oZqnHeXaiHeHr1IvD5RiIenT0aGK6mrl8UGZBG8vuX1K57AOkiq26', '2025-03-04 10:55:19', '', NULL, NULL),
(18, 'pad', 'p@gmail.com', '$2y$10$qeWKkwn3xCaTpACOr2UoI.hrmJ2s2MJfh9tLU5xZXr9/R5hBs0rCi', '2025-03-04 15:23:56', '7760757561', NULL, NULL),
(19, 'Fon', 'venus0388@gmail.com', '$2y$10$/Otifz55pjBcbRr46oiiYumGi1VvpIEombLGMTFtb7nC4hikMRqTO', '2025-03-18 02:05:15', '', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `sell_id` (`sell_id`);

--
-- Indexes for table `out_product_details`
--
ALTER TABLE `out_product_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `out_id` (`out_id`);

--
-- Indexes for table `out_stock_product`
--
ALTER TABLE `out_stock_product`
  ADD PRIMARY KEY (`out_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_code` (`product_code`);

--
-- Indexes for table `sell_product`
--
ALTER TABLE `sell_product`
  ADD PRIMARY KEY (`sell_id`);

--
-- Indexes for table `sell_product_details`
--
ALTER TABLE `sell_product_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `sell_id` (`sell_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `out_product_details`
--
ALTER TABLE `out_product_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `out_stock_product`
--
ALTER TABLE `out_stock_product`
  MODIFY `out_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=339;

--
-- AUTO_INCREMENT for table `sell_product`
--
ALTER TABLE `sell_product`
  MODIFY `sell_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `sell_product_details`
--
ALTER TABLE `sell_product_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `out_product_details`
--
ALTER TABLE `out_product_details`
  ADD CONSTRAINT `out_product_details_ibfk_1` FOREIGN KEY (`out_id`) REFERENCES `out_stock_product` (`out_id`) ON DELETE CASCADE;

--
-- Constraints for table `sell_product_details`
--
ALTER TABLE `sell_product_details`
  ADD CONSTRAINT `sell_product_details_ibfk_1` FOREIGN KEY (`sell_id`) REFERENCES `sell_product` (`sell_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
