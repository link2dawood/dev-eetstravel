-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 29, 2023 at 06:06 AM
-- Server version: 10.5.19-MariaDB-0+deb11u2
-- PHP Version: 8.0.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dev_tms`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `country` varchar(191) NOT NULL,
  `city` varchar(191) NOT NULL,
  `address` varchar(191) DEFAULT NULL,
  `account_no` varchar(250) DEFAULT NULL,
  `company_address` varchar(250) DEFAULT NULL,
  `invoice_address` varchar(250) DEFAULT NULL,
  `work_phone` varchar(191) DEFAULT NULL,
  `contact_phone` varchar(191) DEFAULT NULL,
  `work_email` varchar(191) DEFAULT NULL,
  `contact_email` varchar(191) DEFAULT NULL,
  `password` varchar(250) NOT NULL,
  `work_fax` varchar(191) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `dep_date` date DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `arrival_city` varchar(255) DEFAULT NULL,
  `dep_city` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `country`, `city`, `address`, `account_no`, `company_address`, `invoice_address`, `work_phone`, `contact_phone`, `work_email`, `contact_email`, `password`, `work_fax`, `department`, `dep_date`, `duration`, `arrival_city`, `dep_city`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Test Client', 'DE', '104', NULL, NULL, NULL, NULL, NULL, NULL, 'aaaa@aaa.aa', 'aaaa@aaa.aa', '', NULL, '', '0000-00-00', '', '', '', '2018-03-26 00:41:38', '2022-12-10 09:35:29', '2022-12-10 09:35:29'),
(2, 'Test Client 2', 'AU', '682', NULL, NULL, NULL, NULL, '123654', NULL, 'bbbbb@bbb.bb', 'bbbbb@bbb.bb', '', NULL, '', '0000-00-00', '', '', '', '2018-03-26 00:42:51', '2018-03-26 00:47:47', '2018-03-26 00:47:47'),
(3, 'Test Client #0207+', 'CA', '681', 'qwerty', NULL, NULL, NULL, '123', '456', 'jul.zahorodna@gmail.com', 'julikzigi@gmail.com', '', '789', '', '0000-00-00', '', '', '', '2019-07-02 06:11:11', '2019-07-02 06:12:07', '2019-07-02 06:12:07'),
(4, 'newrole', 'BD', '2', 'Testadress', '111111', 'fgghhj', 'test Invoice Address', '03232374749', '1234455555', 'wahid@gmail.com', 'wahid@gmail.com', '', '23', '', '0000-00-00', '', '', '', '2022-12-10 09:30:28', '2023-05-19 05:48:06', NULL),
(5, 'newrole', 'AU', '2', 'Testadress', NULL, NULL, NULL, '33333132', '1234455555', 'test@test.com', 'contactemail@gmail.com', '', '23', '', '0000-00-00', '', '', '', '2022-12-10 09:32:13', '2022-12-10 09:36:14', '2022-12-10 09:36:14'),
(6, 'Ameer Hamza', 'BD', '19', '10Clane-1 Street3-RahatCommercial Phase 6 DHA Karachi', '999994', 'test company  address', 'test Invoice Address', '0412267687638', '0412267687638', 'meerhamza@gmail.com', 'meerhamza@gmail.com', '', '20', NULL, NULL, NULL, NULL, NULL, '2023-04-12 06:48:54', '2023-05-19 06:29:04', NULL),
(7, 'Ameer', 'BD', '19', '10Clane-1 Street3-RahatCommercial Phase 6 DHA Karachi', '222223', 'test company  address', 'test Invoice Address', '0412267687638', '0412267687638', 'meerhamza@gmail.com', 'meerhamza@gmail.com', '', '20', NULL, NULL, NULL, NULL, NULL, '2023-04-12 06:50:14', '2023-05-19 06:28:31', NULL),
(8, 'QAtester', 'PK', '670', 'test Address', 'test Account Address', 'test company  address', 'test Invoice Address', '09876543', '09876543', 'tester@gmail.com', 'tester@gmail.com', '', '20', NULL, NULL, NULL, NULL, NULL, '2023-04-13 13:20:33', '2023-04-27 15:36:29', '2023-04-27 15:36:29'),
(9, 'Ather Zahid', 'PK', '3', 'ather@client.co.uk', '111111', 'company address', 'iunvoice address', '098765432', '097654', 'ather@gmail.com', 'ather@gmail.com', '', '10', NULL, NULL, NULL, NULL, NULL, '2023-05-26 10:30:51', '2023-05-26 10:30:51', NULL),
(10, 'Zeeshan', 'CA', '2', 'BaldiaTown', '999994', 'BaldiaTown', 'BaldiaTown', '7423523423', '7423523423', 'zeeshan@gmail.com', 'zeeshan@gmail.com', 'zeeshan@12', '20', NULL, NULL, NULL, NULL, NULL, '2023-05-29 05:55:01', '2023-05-29 05:55:01', NULL),
(11, 'Azhar', 'AU', '2', 'United states texas', '7654765', 'Company Address', 'Invoice Address', '654321113', '6543256456', 'azhar@gmail.com', 'azhar@gmail.com', 'azhar@12', '23', NULL, NULL, NULL, NULL, NULL, '2023-05-29 06:01:11', '2023-05-29 06:01:11', NULL),
(12, 'rehan', 'PK', '1', 'Address', '654366557', 'Company Address', 'Invoice Address', '876543', '876543', 'rehan@gmail.com', 'rehan@gmail.com', '$2y$10$1R9zjE6YZ2RdeN1f/YTy.eI57870C/hfcZXVwkOuMwc0W8oHMWoxG', '14', NULL, NULL, NULL, NULL, NULL, '2023-05-29 06:05:47', '2023-05-29 06:05:47', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
