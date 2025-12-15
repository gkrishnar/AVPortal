-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 15, 2025 at 12:36 PM
-- Server version: 8.0.44
-- PHP Version: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `avagro_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int NOT NULL,
  `employee_id` int NOT NULL,
  `punch_in` datetime DEFAULT NULL,
  `punch_out` datetime DEFAULT NULL,
  `work_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `punch_in`, `punch_out`, `work_date`) VALUES
(3, 28, '2025-12-13 10:06:28', '2025-12-13 21:42:38', '2025-12-13'),
(4, 21, '2025-12-13 10:06:42', '2025-12-13 19:06:07', '2025-12-13'),
(5, 24, '2025-12-13 10:06:55', '2025-12-13 19:06:22', '2025-12-13'),
(6, 4, '2025-12-13 10:07:08', '2025-12-13 19:06:29', '2025-12-13'),
(7, 10, '2025-12-13 10:07:16', '2025-12-13 19:06:37', '2025-12-13'),
(8, 5, '2025-12-13 10:07:24', '2025-12-13 19:24:53', '2025-12-13'),
(9, 7, '2025-12-13 10:07:33', '2025-12-13 19:25:27', '2025-12-13'),
(10, 31, '2025-12-13 10:07:44', '2025-12-13 19:09:18', '2025-12-13'),
(11, 14, '2025-12-13 10:07:56', '2025-12-13 21:42:48', '2025-12-13'),
(12, 13, '2025-12-13 10:08:10', '2025-12-13 21:42:27', '2025-12-13'),
(13, 15, '2025-12-13 10:08:19', '2025-12-13 21:42:16', '2025-12-13'),
(14, 30, '2025-12-13 10:08:31', '2025-12-13 19:25:54', '2025-12-13'),
(15, 19, '2025-12-13 10:08:42', '2025-12-13 21:42:03', '2025-12-13'),
(16, 9, '2025-12-13 10:08:55', '2025-12-13 19:47:09', '2025-12-13'),
(17, 11, '2025-12-13 10:09:04', '2025-12-13 19:46:49', '2025-12-13'),
(18, 17, '2025-12-13 10:09:16', '2025-12-13 21:41:53', '2025-12-13'),
(19, 12, '2025-12-13 10:09:24', '2025-12-13 21:41:40', '2025-12-13'),
(20, 23, '2025-12-13 10:09:35', '2025-12-13 19:46:25', '2025-12-13'),
(21, 1, '2025-12-13 10:10:10', '2025-12-13 19:26:30', '2025-12-13'),
(22, 20, '2025-12-13 10:10:18', '2025-12-13 19:26:12', '2025-12-13'),
(23, 8, '2025-12-13 10:18:52', '2025-12-13 19:10:18', '2025-12-13'),
(24, 2, '2025-12-13 10:31:23', '2025-12-13 21:41:22', '2025-12-13'),
(25, 16, '2025-12-13 15:09:59', '2025-12-13 21:41:12', '2025-12-13'),
(26, 18, '2025-12-13 15:10:29', '2025-12-13 19:05:42', '2025-12-13'),
(27, 28, '2025-12-15 08:23:38', NULL, '2025-12-15'),
(28, 7, '2025-12-15 08:23:47', NULL, '2025-12-15'),
(29, 17, '2025-12-15 08:24:10', NULL, '2025-12-15'),
(30, 14, '2025-12-15 08:29:47', '2025-12-15 12:05:42', '2025-12-15'),
(31, 13, '2025-12-15 08:34:24', NULL, '2025-12-15'),
(32, 26, '2025-12-15 08:36:05', '2025-12-15 17:51:46', '2025-12-15'),
(33, 27, '2025-12-15 08:38:00', '2025-12-15 17:52:13', '2025-12-15'),
(34, 25, '2025-12-15 08:38:09', '2025-12-15 17:52:22', '2025-12-15'),
(35, 18, '2025-12-15 08:38:46', '2025-12-15 12:17:03', '2025-12-15'),
(36, 10, '2025-12-15 08:42:04', '2025-12-15 17:52:30', '2025-12-15'),
(37, 15, '2025-12-15 08:42:23', NULL, '2025-12-15'),
(38, 9, '2025-12-15 08:42:33', '2025-12-15 17:52:46', '2025-12-15'),
(39, 11, '2025-12-15 08:42:47', '2025-12-15 12:19:50', '2025-12-15'),
(40, 16, '2025-12-15 08:45:20', NULL, '2025-12-15'),
(41, 23, '2025-12-15 09:02:43', '2025-12-15 18:06:08', '2025-12-15'),
(42, 24, '2025-12-15 09:03:04', NULL, '2025-12-15'),
(43, 1, '2025-12-15 09:05:59', NULL, '2025-12-15'),
(44, 5, '2025-12-15 09:06:23', NULL, '2025-12-15'),
(45, 12, '2025-12-15 09:13:00', NULL, '2025-12-15'),
(46, 8, '2025-12-15 09:13:11', '2025-12-15 12:23:41', '2025-12-15'),
(47, 4, '2025-12-15 09:24:48', NULL, '2025-12-15'),
(48, 22, '2025-12-15 09:29:42', '2025-12-15 18:03:14', '2025-12-15'),
(49, 21, '2025-12-15 09:50:38', NULL, '2025-12-15'),
(50, 19, '2025-12-15 10:04:15', NULL, '2025-12-15'),
(51, 20, '2025-12-15 10:08:35', '2025-12-15 18:03:24', '2025-12-15'),
(52, 2, '2025-12-15 10:31:53', NULL, '2025-12-15'),
(53, 30, '2025-12-15 10:35:34', '2025-12-15 10:42:57', '2025-12-15'),
(54, 8, '2025-12-15 13:13:55', NULL, '2025-12-15'),
(55, 14, '2025-12-15 14:39:38', NULL, '2025-12-15'),
(56, 11, '2025-12-15 15:00:53', NULL, '2025-12-15');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `details` text,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `timestamp`) VALUES
(1, 2, 'LOGIN', 'User raju logged in.', '2025-12-10 03:21:17'),
(2, 2, 'LOGIN', 'User raju logged in.', '2025-12-10 04:24:48'),
(3, 2, 'LOGIN', 'User raju logged in.', '2025-12-10 04:24:53'),
(4, 2, 'LOGIN', 'User raju logged in.', '2025-12-10 09:31:26'),
(5, 2, 'LOGIN', 'User raju logged in.', '2025-12-10 09:31:30'),
(6, 2, 'LOGIN', 'User raju logged in.', '2025-12-10 10:02:58'),
(7, 2, 'MARK_EXIT', 'Marked exit for Pass ID: 4', '2025-12-10 10:06:06'),
(8, 2, 'LOGIN', 'User raju logged in.', '2025-12-10 10:21:03'),
(9, 2, 'MARK_EXIT', 'Marked exit for Pass ID: 3', '2025-12-10 10:22:16'),
(10, 2, 'MARK_EXIT', 'Marked exit for Pass ID: 2', '2025-12-10 10:22:34'),
(11, 2, 'LOGIN', 'User raju logged in.', '2025-12-10 13:40:33'),
(12, 2, 'MARK_EXIT', 'Marked exit for Pass ID: 6', '2025-12-10 13:46:58'),
(13, 2, 'MARK_EXIT', 'Marked exit for Pass ID: 5', '2025-12-10 13:47:03'),
(14, 1, 'LOGIN', 'User admin logged in.', '2025-12-11 08:09:21'),
(15, 1, 'ADD_PURPOSE', 'Added purpose: Chopa Dali', '2025-12-11 08:09:43'),
(16, 2, 'LOGIN', 'User raju logged in.', '2025-12-11 08:55:21'),
(17, 2, 'MARK_EXIT', 'Marked exit for Pass ID: 7', '2025-12-11 08:57:36'),
(18, 1, 'LOGIN', 'User admin logged in.', '2025-12-11 14:50:24'),
(19, 1, 'ADD_PURPOSE', 'Added purpose: Meeting', '2025-12-11 14:51:13'),
(20, 1, 'MARK_EXIT', 'Marked exit for Pass ID: 8', '2025-12-11 14:52:36'),
(21, 1, 'MARK_EXIT', 'Marked exit for Pass ID: 1', '2025-12-11 14:52:46'),
(22, 2, 'LOGIN', 'User raju logged in.', '2025-12-12 05:58:15'),
(23, 2, 'LOGIN', 'User raju logged in.', '2025-12-12 07:22:31'),
(24, 2, 'LOGIN', 'User raju logged in.', '2025-12-12 08:36:17'),
(25, 2, 'MARK_EXIT', 'Marked exit for Pass ID: 9', '2025-12-12 08:37:46'),
(26, 2, 'LOGIN', 'User raju logged in.', '2025-12-12 08:46:23'),
(27, 2, 'MARK_EXIT', 'Marked exit for Pass ID: 10', '2025-12-12 08:52:18'),
(28, 2, 'MARK_EXIT', 'Marked exit for Pass ID: 11', '2025-12-12 09:03:10'),
(29, 2, 'LOGIN', 'User raju logged in.', '2025-12-12 09:41:41'),
(30, 2, 'MARK_EXIT', 'Marked exit for Pass ID: 13', '2025-12-12 10:05:01'),
(31, 2, 'MARK_EXIT', 'Marked exit for Pass ID: 12', '2025-12-12 10:05:03'),
(32, 2, 'MARK_EXIT', 'Marked exit for Pass ID: 14', '2025-12-12 10:29:30'),
(33, 1, 'LOGIN', 'User admin logged in.', '2025-12-12 13:04:12'),
(34, 2, 'LOGIN', 'User raju logged in.', '2025-12-12 13:05:24'),
(35, 1, 'LOGIN', 'User admin logged in.', '2025-12-12 14:05:53'),
(36, 1, 'ADD_PURPOSE', 'Added purpose: Husk Loading', '2025-12-12 14:06:07'),
(37, 1, 'ADD_PURPOSE', 'Added purpose: Cash Sale', '2025-12-12 14:06:13'),
(38, 1, 'ADD_PURPOSE', 'Added purpose: RCN Job Work', '2025-12-12 14:06:25'),
(39, 2, 'LOGIN', 'User raju logged in.', '2025-12-12 14:10:36'),
(40, 2, 'LOGIN', 'User raju logged in.', '2025-12-12 14:17:47'),
(41, 1, 'LOGIN', 'User admin logged in.', '2025-12-12 19:53:35'),
(42, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-12 21:33:59'),
(43, 2, 'LOGIN', 'User raju logged in.', '2025-12-13 01:47:17'),
(44, 1, 'LOGIN', 'User admin logged in.', '2025-12-13 02:48:07'),
(45, 1, 'LOGIN', 'User admin logged in.', '2025-12-13 03:34:27'),
(46, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 03:52:40'),
(47, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 04:13:45'),
(48, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 04:23:35'),
(49, 2, 'LOGIN', 'User raju logged in.', '2025-12-13 04:35:25'),
(50, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 04:36:53'),
(51, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 04:42:35'),
(52, 2, 'LOGIN', 'User raju logged in.', '2025-12-13 04:44:27'),
(53, 5, 'LOGIN', 'User varma logged in.', '2025-12-13 04:47:32'),
(54, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 04:50:09'),
(55, 5, 'LOGIN', 'User Varma logged in.', '2025-12-13 04:52:37'),
(56, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 04:53:04'),
(57, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 05:03:26'),
(58, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 05:37:09'),
(59, 2, 'LOGIN', 'User raju logged in.', '2025-12-13 05:39:24'),
(60, 5, 'LOGIN', 'User Varma logged in.', '2025-12-13 05:41:22'),
(61, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 05:58:59'),
(62, 1, 'LOGIN', 'User admin logged in.', '2025-12-13 07:58:02'),
(63, 1, 'MARK_EXIT', 'Marked exit for Pass ID: 15', '2025-12-13 07:59:08'),
(64, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 08:01:46'),
(65, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 09:19:20'),
(66, 2, 'LOGIN', 'User raju logged in.', '2025-12-13 09:38:25'),
(67, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 11:07:19'),
(68, 1, 'LOGIN', 'User admin logged in.', '2025-12-13 11:08:27'),
(69, 1, 'LOGIN', 'User admin logged in.', '2025-12-13 11:40:15'),
(70, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 11:42:30'),
(71, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 11:43:16'),
(72, 5, 'LOGIN', 'User varma logged in.', '2025-12-13 11:49:14'),
(73, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 12:08:42'),
(74, 2, 'LOGIN', 'User raju logged in.', '2025-12-13 12:18:01'),
(75, 2, 'LOGIN', 'User raju logged in.', '2025-12-13 12:21:32'),
(76, 2, 'LOGIN', 'User raju logged in.', '2025-12-13 12:26:05'),
(77, 2, 'LOGIN', 'User raju logged in.', '2025-12-13 12:33:51'),
(78, 2, 'LOGIN', 'User raju logged in.', '2025-12-13 12:39:26'),
(79, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 13:05:26'),
(80, 2, 'LOGIN', 'User raju logged in.', '2025-12-13 13:10:24'),
(81, 1, 'LOGIN', 'User admin logged in.', '2025-12-13 13:21:41'),
(82, 2, 'LOGIN', 'User raju logged in.', '2025-12-13 13:54:11'),
(83, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 14:10:23'),
(84, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 14:28:19'),
(85, 1, 'LOGIN', 'User admin logged in.', '2025-12-13 14:32:50'),
(86, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-13 14:49:34'),
(87, 1, 'LOGIN', 'User admin logged in.', '2025-12-13 15:09:09'),
(88, 1, 'LOGIN', 'User admin logged in.', '2025-12-13 15:33:56'),
(89, 2, 'LOGIN', 'User raju logged in.', '2025-12-13 16:33:46'),
(90, 1, 'LOGIN', 'User admin logged in.', '2025-12-13 16:41:16'),
(91, 2, 'LOGIN', 'User raju logged in.', '2025-12-14 09:28:37'),
(92, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 02:53:22'),
(93, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 03:11:49'),
(94, 1, 'LOGIN', 'User admin logged in.', '2025-12-15 03:33:27'),
(95, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 03:35:07'),
(96, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 04:33:36'),
(97, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 05:11:58'),
(98, 5, 'LOGIN', 'User Varma logged in.', '2025-12-15 05:20:58'),
(99, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-15 05:33:11'),
(100, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 06:27:59'),
(101, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-15 06:41:43'),
(102, 1, 'LOGIN', 'User admin logged in.', '2025-12-15 06:42:35'),
(103, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 07:18:01'),
(104, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-15 07:19:08'),
(105, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-15 07:20:57'),
(106, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-15 07:40:29'),
(107, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 07:40:57'),
(108, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 07:47:28'),
(109, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 07:51:38'),
(110, 5, 'LOGIN', 'User varma logged in.', '2025-12-15 07:56:18'),
(111, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 07:58:32'),
(112, 1, 'LOGIN', 'User admin logged in.', '2025-12-15 07:59:07'),
(113, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 08:18:45'),
(114, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 08:41:44'),
(115, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 08:44:58'),
(116, 2, 'MARK_EXIT', 'Marked exit for Pass ID: 17', '2025-12-15 09:08:12'),
(117, 2, 'MARK_EXIT', 'Marked exit for Pass ID: 16', '2025-12-15 09:08:18'),
(118, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 09:30:34'),
(119, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 10:16:27'),
(120, 1, 'LOGIN', 'User admin logged in.', '2025-12-15 10:24:13'),
(121, 1, 'LOGIN', 'User admin logged in.', '2025-12-15 11:04:54'),
(122, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 11:29:15'),
(123, 1, 'LOGIN', 'User admin logged in.', '2025-12-15 11:54:41'),
(124, 3, 'LOGIN', 'User kiranp logged in.', '2025-12-15 12:12:12'),
(125, 2, 'LOGIN', 'User raju logged in.', '2025-12-15 12:12:53'),
(126, 1, 'MARK_EXIT', 'Marked exit for Pass ID: 18', '2025-12-15 12:23:02');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `mobile_no` varchar(15) DEFAULT NULL,
  `alt_mobile` varchar(15) DEFAULT NULL,
  `whatsapp_no` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text,
  `joining_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `designation`, `mobile_no`, `alt_mobile`, `whatsapp_no`, `email`, `address`, `joining_date`, `created_at`) VALUES
(1, 'T Santosh', NULL, '9938804805', '', '9938804805', '', 'Borigumma ', '2021-07-01', '2025-12-12 17:57:28'),
(2, 'Kiran Patnaik', NULL, '9668058263', '', '9668058263', '', 'Khudiguda', '2025-12-13', '2025-12-12 19:11:59'),
(4, 'Damburu Ganda', NULL, '9078950891', '', '9078950891', '', 'Khudiguda', '2025-12-13', '2025-12-12 19:26:29'),
(5, 'DANO', NULL, '8093806466', '', '8093806466', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:26:48'),
(7, 'Dhablu Jani', NULL, '9078373234', '', '9078373234', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:27:13'),
(8, 'Jagdish', NULL, '7653837206', '', '7653837206', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:27:24'),
(9, 'Nilkantha Das - Sahebo', NULL, '7609917507', '', '7609917507', '', 'Borigumma ', '2025-12-13', '2025-12-12 19:27:40'),
(10, 'DAMU GADABA', NULL, '7894858589', '', '7894858589', '', 'Khudiguda', '2025-12-13', '2025-12-12 19:27:49'),
(11, 'Pitamber Gadaba', NULL, '8260789315', '', '8260789315', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:27:57'),
(12, 'S Durgaprasad Varma', NULL, '8096033239', '', '8096033239', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:28:06'),
(13, 'Kailas Gadaba', NULL, '9040884103', '', '9040884103', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:28:17'),
(14, 'Guru Muduli', NULL, '8260869289', '', '8260869289', '', 'Ambaguda', '2025-12-13', '2025-12-12 19:28:24'),
(15, 'Kamla Lochan Paraja', NULL, '9937918117', '', '9937918117', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:28:32'),
(16, 'Ghanu', NULL, '7894624479', '', '', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:28:40'),
(17, 'Subhash', NULL, '7683871820', '', '9348448736', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:28:47'),
(18, 'A Komlachan', NULL, '8144640458', '', '', '', 'Ambdaguda', '2025-12-13', '2025-12-12 19:28:54'),
(19, 'Mohan', NULL, '9692113261', '', '9692113261', '', 'Ambdaguda ', '2025-12-13', '2025-12-12 19:29:04'),
(20, 'T Laxmi', NULL, '6372023702', '', '6372023702', '', 'Borigumma ', '2025-12-13', '2025-12-12 19:29:13'),
(21, 'BALLESH', NULL, '8144387609', '', '8144387609', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:29:21'),
(22, 'SUKRI', NULL, '7682998469', '', '', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:29:27'),
(23, 'TULLAWATI', NULL, '7077984445', '', '7077984445', '', 'Biriguda ', '2025-12-13', '2025-12-12 19:29:34'),
(24, 'BOTI', NULL, '1234567822', '', '', '', 'Biriguda', '2025-12-13', '2025-12-12 19:29:41'),
(25, 'TULSHI', NULL, '1234567823', '', '', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:29:48'),
(26, 'NILA', NULL, '1234567824', '', '', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:29:55'),
(27, 'TILTAMMA', NULL, '1234567825', '', '', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:30:02'),
(28, 'Amir Dukhi', NULL, '9348806716', '', '9348806716', '', 'Khudiguda', '2025-12-13', '2025-12-12 19:30:09'),
(29, 'Jay Patel', NULL, '6358040941', '', '6358040941', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:30:18'),
(30, 'Lalu', NULL, '6372748496', '', '66372748496', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:30:24'),
(31, 'G Jaggan Raju', NULL, '8247424738', '', '8247424738', '', 'Khudiguda ', '2025-12-13', '2025-12-12 19:30:31');

-- --------------------------------------------------------

--
-- Table structure for table `gate_passes`
--

CREATE TABLE `gate_passes` (
  `id` int NOT NULL,
  `visitor_name` varchar(100) NOT NULL,
  `mobile_no` varchar(15) DEFAULT NULL,
  `vehicle_no` varchar(50) DEFAULT NULL,
  `material_details` text,
  `purpose_id` int DEFAULT NULL,
  `entry_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `exit_time` datetime DEFAULT NULL,
  `visitor_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_de_pb_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gate_passes`
--

INSERT INTO `gate_passes` (`id`, `visitor_name`, `mobile_no`, `vehicle_no`, `material_details`, `purpose_id`, `entry_time`, `created_by`, `exit_time`, `visitor_image`) VALUES
(1, 'CBI', '6005571582', '', 'Cashew sale 5kg', 1, '2025-12-10 09:17:10', 2, '2025-12-11 20:22:46', NULL),
(2, 'Loxman', '8018834826', 'OD10P4445', 'Chopa 250 pkt', 8, '2025-12-10 15:28:13', 2, '2025-12-10 15:52:34', NULL),
(3, 'Monaj', '6372956953', 'OD10j4445', 'Chopa 250 pkt', 8, '2025-12-10 15:35:31', 2, '2025-12-10 15:52:16', NULL),
(4, 'Monaj', '6372956953', 'OD10j4445', 'Chopa 250 pkt', 8, '2025-12-10 15:35:49', 2, '2025-12-10 15:36:06', NULL),
(5, 'VRL', '7815074729', 'Cg04NS7089', 'Buckets 999', 7, '2025-12-10 19:13:56', 2, '2025-12-10 19:17:03', NULL),
(6, 'Raipur', '8085168590', 'Cg24u9681', 'Bucket 34', 7, '2025-12-10 19:16:07', 2, '2025-12-10 19:16:58', NULL),
(7, 'Laxmon', '8018834826', 'OD10j4445', 'Chopa 300pkt', 8, '2025-12-11 14:27:11', 2, '2025-12-11 14:27:36', NULL),
(8, 'Chandu Mid India', '9692878560', '', 'Just for meeting', 11, '2025-12-11 20:21:45', 1, '2025-12-11 20:22:36', NULL),
(9, 'Loxman', '8018834826', 'OD10j4445', 'Chopa 300 pkt', 8, '2025-12-12 14:07:20', 2, '2025-12-12 14:07:46', NULL),
(10, 'Mahesh ', '8374585246', 'AP39UR6423', 'White maal\r\nKgs- 3000', 1, '2025-12-12 14:22:11', 2, '2025-12-12 14:22:18', NULL),
(11, 'Kishore', '9700508608', '', 'Cutting machine mechanic ', 2, '2025-12-12 14:32:58', 2, '2025-12-12 14:33:10', NULL),
(12, 'Mahesh ', '8374585246', 'AP39UR6423', 'Rcn 40pkt', 7, '2025-12-12 15:16:05', 2, '2025-12-12 15:35:03', NULL),
(13, 'Vinay', '9337704680', 'OD 10aa4610', 'Bucket 18', 6, '2025-12-12 15:31:07', 2, '2025-12-12 15:35:01', NULL),
(14, 'Kamesh', '9030253353', 'Ap39np8860', 'Cashew sale. 500gm', 7, '2025-12-12 15:59:21', 2, '2025-12-12 15:59:30', NULL),
(15, 'Ajay', '8655278086', 'Hr47d5212', '', 12, '2025-12-12 19:42:25', 2, '2025-12-13 13:29:08', NULL),
(16, 'Sanu', '7846901088', 'OD02ck3080', 'Khali bucket ', 6, '2025-12-15 14:35:20', 2, '2025-12-15 14:38:18', NULL),
(17, 'Rabi', '8823847321', 'Cg04pp1971', 'Bucket 250', 7, '2025-12-15 14:37:49', 2, '2025-12-15 14:38:12', NULL),
(18, 'VRL', '7815074729', 'Ka639885', 'Bucket 600', 7, '2025-12-15 17:45:26', 2, '2025-12-15 17:53:02', NULL),
(21, 'Chompia', '8260789315', 'OD10p7410', 'RCN 60pkt', 14, '2025-12-15 18:02:48', 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `oms_categories`
--

CREATE TABLE `oms_categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `oms_categories`
--

INSERT INTO `oms_categories` (`id`, `name`) VALUES
(2, 'Cashew Broken'),
(1, 'Cashew NW'),
(3, 'Cashew Rejection'),
(4, 'Cashew Wholes');

-- --------------------------------------------------------

--
-- Table structure for table `oms_customers`
--

CREATE TABLE `oms_customers` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `whatsapp` varchar(15) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `address` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `oms_customers`
--

INSERT INTO `oms_customers` (`id`, `name`, `mobile`, `whatsapp`, `state`, `address`, `created_at`) VALUES
(1, 'Manish Enterprises', '9039144364', '9039144364', 'Madhya Pradesh', 'Indore', '2025-12-12 20:50:18'),
(2, 'SN - Raipur', '9303801305', '9303801305', 'Chattisgarh', 'Raipur', '2025-12-12 20:55:13'),
(3, 'Sai P Raj', '8090030900', '8090030900', 'UP', 'Kanpur', '2025-12-12 20:58:28'),
(4, 'Sitesh', '7847960091', '7847960091', 'Chattisgarh', 'Raipur', '2025-12-12 20:59:53'),
(5, 'Dheeraj Traders', '9425911825', '9425911825', 'MP', 'Indore', '2025-12-12 21:01:20'),
(6, 'Rangoli Foods', '1234567890', '', '', '', '2025-12-13 03:36:18'),
(7, 'V R Trading', '1234567890', '', 'Jharkhand', 'C4, Bhdadani Trade Centre', '2025-12-13 03:40:05'),
(8, 'Shubham K Mart', '12345667890', '', '', '', '2025-12-13 04:30:32'),
(9, 'Karnataka', '1234567890', '', '', '', '2025-12-13 12:30:40'),
(10, 'Anil Kumar', '1234567890', '', '', '', '2025-12-13 12:30:57'),
(11, 'Rohini Traders', '01234567890', '', 'Jharkhand', 'C4, Bhdadani Trade Centre', '2025-12-13 12:34:18');

-- --------------------------------------------------------

--
-- Table structure for table `oms_items`
--

CREATE TABLE `oms_items` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `short_code` varchar(20) DEFAULT NULL,
  `current_stock` int DEFAULT '0',
  `unit_price` decimal(10,2) DEFAULT '0.00',
  `category_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `oms_items`
--

INSERT INTO `oms_items` (`id`, `name`, `short_code`, `current_stock`, `unit_price`, `category_id`) VALUES
(1, 'W140', 'W140', 0, 0.00, 4),
(2, 'S140', 'S140', 0, 0.00, 4),
(3, 'S160', 'S160', 0, 0.00, 4),
(4, 'Y160', 'Y160', 0, 0.00, 4),
(5, 'W160', 'W160', 0, 0.00, 4),
(6, 'W180', 'W180', 0, 0.00, 4),
(7, 'W210', 'W210', 0, 0.00, 4),
(8, 'W240', 'W240', 2, 0.00, 4),
(9, 'S210', 'S210', 0, 0.00, 4),
(10, 'Y210', 'Y210', 15, 0.00, 4),
(11, 'A210', 'A210', 0, 0.00, 4),
(12, 'Y240', 'Y240', 5, 0.00, 4),
(13, 'S240', 'S240', 0, 0.00, 4),
(14, 'P240', 'P240', 3, 0.00, 4),
(15, 'A320', 'A320', 52, 0.00, 4),
(16, 'W320', 'W320', 109, 0.00, 4),
(17, 'Y320', 'Y320', 96, 0.00, 4),
(18, 'S320', 'S320', 0, 0.00, 4),
(19, 'W400', 'W400', 0, 0.00, 4),
(20, 'A400', 'A400', 0, 0.00, 4),
(21, 'S400', 'S400', 0, 0.00, 4),
(22, 'Y400', 'Y400', 0, 0.00, 4),
(23, 'SW', 'SW', 21, 0.00, 4),
(24, 'SJB', 'SJB', 7, 0.00, 4),
(25, 'PG', 'PG', 11, 0.00, 4),
(26, 'PW-1', 'PW-1', 0, 0.00, 4),
(27, 'WMIX', 'WMIX', 1, 0.00, 4),
(28, 'DW', 'DW', 363, 0.00, 4),
(29, 'JH', 'JH', 0, 0.00, 2),
(30, 'SJH', 'SJH', 77, 0.00, 2),
(31, 'SJH2', 'SJH2', 0, 0.00, 2),
(32, 'JK', 'JK', 182, 0.00, 2),
(33, 'K', 'K', 271, 0.00, 2),
(34, 'SK', 'SK', 739, 0.00, 2),
(35, 'SK2', 'SK2', 0, 0.00, 2),
(36, 'SWP', 'SWP', 45, 0.00, 2),
(37, 'SBP', 'SBP', 0, 0.00, 2),
(38, 'BB', 'BB', 19, 0.00, 2),
(39, 'RRW', 'RRW', 22, 0.00, 4),
(40, 'RW', 'RW', 92, 0.00, 4),
(41, 'SWP-1/SN', 'SWP-1/SN', 6, 0.00, 2),
(42, 'S', 'S', 1, 0.00, 2),
(43, 'SW210', 'SW210', 0, 0.00, 4),
(44, 'DW2', 'DW2', 18, 0.00, 4),
(45, 'JB', 'JB', 7, 0.00, 4),
(46, 'DK', 'DK', 48, 0.00, 2),
(47, 'SPOT- 1', 'SPOT- 1', 0, 0.00, 4),
(48, 'DJH', 'DJH', 13, 0.00, 2),
(49, 'DJB', 'DJB', 0, 0.00, 2),
(50, 'NW - Mold-Tek', 'NW - Mold-Tek', 63, 0.00, 1),
(51, 'NW - Blue Cap', 'NW - Blue Cap', 226, 0.00, 1),
(52, 'NW - Bag', 'NW - Bag', 0, 0.00, 1),
(53, 'MKT', 'MKT', 0, 0.00, 3),
(54, 'SPOT', 'SPOT', 0, 0.00, 3),
(55, 'KP', 'KP', 0, 0.00, 3),
(56, 'KTP', 'KTP', 0, 0.00, 3),
(57, 'TM', 'TM', 0, 0.00, 3),
(58, 'KG', 'KG', 0, 0.00, 3),
(59, 'KG2', 'KG2', 0, 0.00, 3),
(60, 'TM1', 'TM1', 0, 0.00, 3),
(61, 'KN', 'KN', 0, 0.00, 3),
(62, 'SN3', 'SN3', 0, 0.00, 3),
(63, 'KP3', 'KP3', 0, 0.00, 3),
(64, 'SWP3', '', 0, 0.00, 3);

-- --------------------------------------------------------

--
-- Table structure for table `oms_orders`
--

CREATE TABLE `oms_orders` (
  `id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `dispatch_date` date DEFAULT NULL,
  `status` enum('Pending','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `oms_orders`
--

INSERT INTO `oms_orders` (`id`, `customer_id`, `item_id`, `quantity`, `price`, `dispatch_date`, `status`, `created_at`) VALUES
(1, 1, 8, 10, 8800.00, '2025-12-15', 'Pending', '2025-12-12 20:53:18'),
(2, 1, 16, 15, 8300.00, '2025-12-15', 'Pending', '2025-12-12 20:53:44'),
(3, 1, 27, 30, 7800.00, '2025-12-15', 'Pending', '2025-12-12 20:54:02'),
(4, 1, 23, 30, 7400.00, '2025-12-15', 'Pending', '2025-12-12 20:54:20'),
(6, 2, 36, 20, 6800.00, '2025-12-13', 'Delivered', '2025-12-12 20:56:22'),
(7, 2, 16, 10, 8200.00, '2025-12-13', 'Delivered', '2025-12-12 20:56:42'),
(8, 2, 14, 20, 8800.00, '2025-12-13', 'Delivered', '2025-12-12 20:57:06'),
(9, 2, 1, 1, 12500.00, '2025-12-14', 'Pending', '2025-12-12 20:57:28'),
(10, 2, 29, 5, 7250.00, '2025-12-13', 'Delivered', '2025-12-12 20:57:45'),
(11, 3, 32, 100, 7550.00, '2025-12-15', 'Shipped', '2025-12-12 20:58:58'),
(12, 4, 28, 70, 7000.00, '2025-12-13', 'Delivered', '2025-12-12 21:00:24'),
(13, 4, 38, 50, 5300.00, '2025-12-13', 'Delivered', '2025-12-12 21:00:40'),
(14, 6, 32, 100, 7900.00, '2025-12-15', 'Shipped', '2025-12-13 03:39:31'),
(15, 7, 51, 170, 6050.00, '2025-12-15', 'Shipped', '2025-12-13 03:41:09'),
(17, 3, 7, 14, 9500.00, '2025-12-15', 'Shipped', '2025-12-13 03:50:30'),
(18, 3, 19, 50, 7550.00, '2025-12-14', 'Pending', '2025-12-13 03:50:52'),
(19, 3, 47, 50, 6450.00, '2025-12-15', 'Shipped', '2025-12-13 03:51:15'),
(20, 8, 16, 100, 8700.00, '2025-12-15', 'Shipped', '2025-12-13 04:30:55'),
(21, 8, 19, 100, 8300.00, '2025-12-15', 'Shipped', '2025-12-13 04:31:12'),
(22, 8, 45, 50, 7900.00, '2025-12-15', 'Shipped', '2025-12-13 04:31:30'),
(23, 2, 7, 5, 9800.00, '2025-12-13', 'Delivered', '2025-12-13 11:44:49'),
(24, 9, 29, 27, 8100.00, '2025-12-22', 'Pending', '2025-12-13 12:31:36'),
(25, 10, 29, 50, 8000.00, '2025-12-15', 'Shipped', '2025-12-13 12:32:03'),
(26, 10, 8, 25, 8800.00, '2025-12-15', 'Pending', '2025-12-13 12:32:25'),
(27, 10, 42, 10, 7800.00, '2025-12-15', 'Pending', '2025-12-13 12:32:53'),
(28, 3, 8, 10, 8800.00, '2025-12-15', 'Shipped', '2025-12-13 12:33:21'),
(30, 11, 8, 20, 8700.00, '2025-12-20', 'Pending', '2025-12-13 12:34:43'),
(31, 5, 16, 25, 8100.00, '2025-12-15', 'Pending', '2025-12-13 12:35:35'),
(32, 5, 28, 25, 7000.00, '2025-12-15', 'Pending', '2025-12-13 12:35:52'),
(33, 5, 40, 10, 7200.00, '2025-12-15', 'Pending', '2025-12-13 12:36:10'),
(34, 5, 8, 25, 8700.00, '2025-12-22', 'Pending', '2025-12-13 12:36:28'),
(35, 7, 51, 835, 6050.00, '2025-12-22', 'Pending', '2025-12-15 08:00:25'),
(36, 9, 29, 73, 8100.00, '2025-12-15', 'Shipped', '2025-12-15 08:01:58'),
(37, 3, 32, 20, 7600.00, '2025-12-15', 'Shipped', '2025-12-15 08:03:15'),
(38, 5, 28, 75, 7000.00, '2025-12-22', 'Pending', '2025-12-15 08:06:57'),
(39, 9, 36, 3, 6800.00, '2025-12-15', 'Shipped', '2025-12-15 11:56:52');

-- --------------------------------------------------------

--
-- Table structure for table `oms_stock_logs`
--

CREATE TABLE `oms_stock_logs` (
  `id` int NOT NULL,
  `item_id` int DEFAULT NULL,
  `txn_type` enum('Add','Dispatch','Sale','Return') NOT NULL,
  `quantity` int NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `log_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `oms_stock_logs`
--

INSERT INTO `oms_stock_logs` (`id`, `item_id`, `txn_type`, `quantity`, `remarks`, `log_date`, `user_id`) VALUES
(1, 7, 'Add', 14, '', '2025-12-12 20:40:51', 1),
(2, 8, 'Add', 12, '', '2025-12-12 20:41:03', 1),
(3, 8, 'Add', 12, '', '2025-12-12 20:42:45', 1),
(4, 10, 'Add', 15, '', '2025-12-12 20:43:02', 1),
(5, 12, 'Add', 5, '', '2025-12-12 20:43:13', 1),
(6, 14, 'Add', 23, '', '2025-12-12 20:43:25', 1),
(7, 15, 'Add', 52, '', '2025-12-12 20:43:36', 1),
(8, 16, 'Add', 198, '', '2025-12-12 20:43:51', 1),
(9, 17, 'Add', 96, '', '2025-12-12 20:44:01', 1),
(10, 19, 'Add', 29, '', '2025-12-12 20:44:12', 1),
(11, 23, 'Add', 21, '', '2025-12-12 20:44:27', 1),
(12, 24, 'Add', 7, '', '2025-12-12 20:44:38', 1),
(13, 25, 'Add', 11, '', '2025-12-12 20:44:47', 1),
(14, 27, 'Add', 1, '', '2025-12-12 20:44:59', 1),
(15, 28, 'Add', 433, '', '2025-12-12 20:45:11', 1),
(16, 29, 'Add', 111, '', '2025-12-12 20:45:22', 1),
(17, 30, 'Add', 77, '', '2025-12-12 20:45:32', 1),
(18, 32, 'Add', 376, '', '2025-12-12 20:45:45', 1),
(19, 33, 'Add', 271, '', '2025-12-12 20:46:02', 1),
(20, 34, 'Add', 739, '', '2025-12-12 20:46:15', 1),
(21, 36, 'Add', 68, '', '2025-12-12 20:46:27', 1),
(22, 38, 'Add', 28, '', '2025-12-12 20:46:37', 1),
(23, 39, 'Add', 22, '', '2025-12-12 20:46:47', 1),
(24, 40, 'Add', 92, '', '2025-12-12 20:46:57', 1),
(25, 41, 'Add', 6, '', '2025-12-12 20:47:09', 1),
(26, 42, 'Add', 1, '', '2025-12-12 20:47:22', 1),
(27, 44, 'Add', 18, '', '2025-12-12 20:47:33', 1),
(28, 46, 'Add', 48, '', '2025-12-12 20:47:44', 1),
(29, 47, 'Add', 12, '', '2025-12-12 20:47:54', 1),
(30, 48, 'Add', 13, '', '2025-12-12 20:48:03', 1),
(31, 50, 'Add', 63, '', '2025-12-12 20:48:17', 1),
(32, 51, 'Add', 178, '', '2025-12-12 20:48:28', 1),
(33, 51, 'Add', 218, '', '2025-12-13 11:57:15', 1),
(34, 45, 'Add', 57, '', '2025-12-13 11:57:27', 1),
(35, 47, 'Add', 27, '', '2025-12-13 11:57:43', 1),
(36, 38, 'Add', 41, '', '2025-12-13 11:57:53', 1),
(37, 32, 'Add', 26, '', '2025-12-13 11:58:03', 1),
(38, 16, 'Add', 21, '', '2025-12-13 11:58:15', 1),
(39, 28, 'Sale', 70, 'Dispatch Sch #12', '2025-12-13 13:26:54', 3),
(40, 38, 'Sale', 50, 'Dispatch Sch #13', '2025-12-13 14:13:03', 3),
(41, 36, 'Sale', 20, 'Dispatch Sch #6', '2025-12-13 14:13:10', 3),
(42, 16, 'Sale', 10, 'Dispatch Sch #7', '2025-12-13 14:13:15', 3),
(43, 29, 'Sale', 5, 'Dispatch Sch #10', '2025-12-13 14:13:27', 3),
(44, 7, 'Sale', 5, 'Dispatch Sch #23', '2025-12-13 14:13:31', 3),
(45, 14, 'Sale', 20, 'Order #8', '2025-12-13 15:35:50', 1),
(46, 19, 'Add', 71, '', '2025-12-15 06:51:00', 1),
(47, 16, 'Sale', 100, 'Dispatch Sch #20', '2025-12-15 06:51:14', 1),
(48, 19, 'Sale', 100, 'Dispatch Sch #21', '2025-12-15 06:51:20', 1),
(49, 45, 'Sale', 50, 'Dispatch Sch #22', '2025-12-15 06:51:27', 1),
(50, 51, 'Sale', 170, 'Dispatch Sch #15', '2025-12-15 10:24:27', 1),
(51, 32, 'Sale', 20, 'Dispatch Sch #37', '2025-12-15 10:24:34', 1),
(52, 29, 'Sale', 50, 'Dispatch Sch #25', '2025-12-15 10:24:52', 1),
(53, 32, 'Sale', 100, 'Dispatch Sch #14', '2025-12-15 10:35:15', 1),
(54, 32, 'Sale', 100, 'Dispatch Sch #11', '2025-12-15 11:05:19', 1),
(55, 8, 'Sale', 10, 'Dispatch Sch #28', '2025-12-15 11:05:26', 1),
(56, 47, 'Add', 11, '', '2025-12-15 11:08:51', 1),
(57, 47, 'Sale', 50, 'Dispatch Sch #19', '2025-12-15 11:09:18', 1),
(58, 7, 'Add', 5, '', '2025-12-15 11:55:36', 1),
(59, 7, 'Sale', 14, 'Dispatch Sch #17', '2025-12-15 11:55:45', 1),
(60, 29, 'Add', 18, '', '2025-12-15 11:57:19', 1),
(61, 29, 'Sale', 73, 'Dispatch Sch #36', '2025-12-15 11:59:10', 1),
(62, 36, 'Sale', 3, 'Dispatch Sch #39', '2025-12-15 11:59:15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int NOT NULL,
  `perm_key` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `perm_key`, `description`) VALUES
(1, 'create_gatepass', 'Create Visitor Pass'),
(2, 'gatepass_report', 'View Visitor Reports'),
(3, 'gatepass_master', 'Manage Visitor Purposes'),
(4, 'employee_manage', 'Manage Employee List (Add/Edit/Delete)'),
(5, 'attendance_mark', 'Mark Staff Attendance'),
(6, 'attendance_report', 'View Attendance Reports'),
(7, 'user_manage', 'Manage System Users & Access'),
(19, 'oms_orders_manage', 'Order Mgmt: Manage Orders (Create/Edit)'),
(20, 'oms_stock_manage', 'Order Mgmt: Manage Stock (Manual In/Out)'),
(21, 'oms_orders_view', 'Order Mgmt: View Orders Only'),
(22, 'oms_reports_view', 'Order Mgmt: View Order Reports'),
(23, 'manage_oms_cust', 'Master: Manage OMS Customers'),
(24, 'manage_oms_item', 'Master: Manage OMS Items'),
(25, 'oms_orders_status', 'Order Mgmt: Change Status (Ship/Deliver)'),
(26, 'oms_stock_report_view', 'Order Mgmt: View Current Stock Report'),
(27, 'oms_dispatch_schedule', 'Order Mgmt: Schedule Daily Dispatch');

-- --------------------------------------------------------

--
-- Table structure for table `purposes`
--

CREATE TABLE `purposes` (
  `id` int NOT NULL,
  `purpose_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `purposes`
--

INSERT INTO `purposes` (`id`, `purpose_name`) VALUES
(1, 'Delivery'),
(2, 'Maintenance'),
(3, 'Interview'),
(5, 'Cake Delivery'),
(6, 'Buckets Delivery'),
(7, 'Buckets Loading'),
(8, 'Shell Loading'),
(9, 'Rejection buyer'),
(10, 'Chopa Dali'),
(11, 'Meeting'),
(12, 'Husk Loading'),
(13, 'Cash Sale'),
(14, 'RCN Job Work');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','operator') NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `reset_token`, `reset_expires`) VALUES
(1, 'admin', 'gkrishnar@gmail.com', '$2y$10$8.Dk.u.k/u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u', 'admin', 'ef7e10cbb94e5bab15ed0c6a3193147aeea595c125a26ab22188be255c2bb330', '2025-12-13 12:31:36'),
(2, 'raju', NULL, '$2y$10$ua8O2rO8Ly9W2HQ2x3I06O88XC.CzrOB954spDW5z91Nz3zfiI.Re', 'operator', NULL, NULL),
(3, 'kiranp', NULL, '$2y$10$ApDjxegpqtTcF/DkKnPUw.icoWPeXk84lXiLTfBYez9KrslbSkCzW', 'operator', NULL, NULL),
(5, 'varma', NULL, '$2y$10$zxw4hnj23DX0vs9fNUN95uIL4I4YcwIc.KtxZmBcDAOvbFAHpHrxC', 'operator', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `user_id` int NOT NULL,
  `permission_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`user_id`, `permission_id`) VALUES
(1, 1),
(2, 1),
(1, 2),
(2, 2),
(3, 2),
(1, 3),
(1, 4),
(3, 4),
(1, 5),
(2, 5),
(1, 6),
(2, 6),
(3, 6),
(1, 7),
(1, 19),
(1, 20),
(3, 20),
(1, 21),
(3, 21),
(5, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(3, 25),
(1, 26),
(3, 26),
(1, 27),
(3, 27),
(5, 27);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mobile_no` (`mobile_no`);

--
-- Indexes for table `gate_passes`
--
ALTER TABLE `gate_passes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purpose_id` (`purpose_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `oms_categories`
--
ALTER TABLE `oms_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `oms_customers`
--
ALTER TABLE `oms_customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oms_items`
--
ALTER TABLE `oms_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `short_code` (`short_code`);

--
-- Indexes for table `oms_orders`
--
ALTER TABLE `oms_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `oms_stock_logs`
--
ALTER TABLE `oms_stock_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `perm_key` (`perm_key`);

--
-- Indexes for table `purposes`
--
ALTER TABLE `purposes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`user_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `gate_passes`
--
ALTER TABLE `gate_passes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `oms_categories`
--
ALTER TABLE `oms_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `oms_customers`
--
ALTER TABLE `oms_customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `oms_items`
--
ALTER TABLE `oms_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `oms_orders`
--
ALTER TABLE `oms_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `oms_stock_logs`
--
ALTER TABLE `oms_stock_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `purposes`
--
ALTER TABLE `purposes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `gate_passes`
--
ALTER TABLE `gate_passes`
  ADD CONSTRAINT `gate_passes_ibfk_1` FOREIGN KEY (`purpose_id`) REFERENCES `purposes` (`id`),
  ADD CONSTRAINT `gate_passes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `oms_orders`
--
ALTER TABLE `oms_orders`
  ADD CONSTRAINT `oms_orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `oms_customers` (`id`),
  ADD CONSTRAINT `oms_orders_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `oms_items` (`id`);

--
-- Constraints for table `oms_stock_logs`
--
ALTER TABLE `oms_stock_logs`
  ADD CONSTRAINT `oms_stock_logs_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `oms_items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
