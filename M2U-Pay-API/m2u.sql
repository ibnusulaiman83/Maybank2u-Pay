-- phpMyAdmin SQL Dump
-- version 4.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 27, 2017 at 09:43 PM
-- Server version: 5.6.32-78.1
-- PHP Version: 5.6.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `spfaszoi_wan_m2u`
--

-- --------------------------------------------------------

--
-- Table structure for table `m2u_bills`
--

CREATE TABLE IF NOT EXISTS `m2u_bills` (
  `AcctId` bigint(20) NOT NULL,
  `PmtType` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `RefId` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TrnDateTime` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `OrderInfo` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Internally Save Data',
  `Amt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `StatusCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `StatusDesc` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `URLId` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Buyer Name',
  `Email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Buyer Email',
  `Mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Buyer Phone',
  `RedirectUrl` mediumtext COLLATE utf8_unicode_ci,
  `CallbackUrl` mediumtext COLLATE utf8_unicode_ci,
  `Timestamp` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Time where the bills is created'
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `m2u_options`
--

CREATE TABLE IF NOT EXISTS `m2u_options` (
  `option_id` bigint(20) NOT NULL,
  `option_name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `option_value` longtext COLLATE utf8_unicode_ci NOT NULL,
  `autoload` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `m2u_options`
--

INSERT INTO `m2u_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(1, 'local_license', '', 'yes');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `m2u_bills`
--
ALTER TABLE `m2u_bills`
  ADD PRIMARY KEY (`AcctId`);

--
-- Indexes for table `m2u_options`
--
ALTER TABLE `m2u_options`
  ADD PRIMARY KEY (`option_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `m2u_bills`
--
ALTER TABLE `m2u_bills`
  MODIFY `AcctId` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=49;
--
-- AUTO_INCREMENT for table `m2u_options`
--
ALTER TABLE `m2u_options`
  MODIFY `option_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
