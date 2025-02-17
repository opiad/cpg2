-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2024 at 07:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bank_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `AccountNumber` int(6) NOT NULL,
  `Pin` varchar(6) NOT NULL,
  `LastName` varchar(20) NOT NULL,
  `FirstName` varchar(30) NOT NULL,
  `MiddleName` varchar(20) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Password` varchar(50) NOT NULL,
  `Balance` int(11) NOT NULL,
  `DateCreated` datetime NOT NULL DEFAULT current_timestamp(),
  `DateUpdated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`AccountNumber`, `Pin`, `LastName`, `FirstName`, `MiddleName`, `Email`, `Password`, `Balance`, `DateCreated`, `DateUpdated`) VALUES
(280325, '808452', 'Lamberte', 'Christopher Anthony', 'Ragasa', 'christopher_lamberte@dlsu.edu.ph', 'carllamberte123', 1500, '2024-03-29 17:25:54', NULL),
(280327, '931232', 'Burgos', 'Cocoy', 'Arsua', 'francisco_burgos@dlsu.edu.ph', 'cocoyburgos3', 500, '2024-03-30 01:09:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `TransactionID` int(6) NOT NULL,
  `AccountID` int(30) NOT NULL,
  `Type` tinyint(4) NOT NULL COMMENT '1=Cash In, 2 = Withdraw, 3 = Transfer',
  `Amount` float NOT NULL,
  `Remarks` text NOT NULL,
  `DateCreated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`TransactionID`, `AccountID`, `Type`, `Amount`, `Remarks`, `DateCreated`) VALUES
(303561, 280325, 2, 1000, 'Test Transaction', '2024-03-29 18:25:55'),
(303562, 280325, 1, 100, 'Deposit of 100', '2024-03-29 19:08:15'),
(303563, 280325, 3, -500, 'Transfer of 500 to Burgos, Cocoy Arsua', '2024-03-29 19:23:11'),
(303564, 280326, 3, 500, 'Transfer from Lamberte, Christopher Anthony Ragasa', '2024-03-29 19:23:11'),
(303565, 280325, 2, -500, 'Withdrawal of 500', '2024-03-29 20:57:29'),
(303566, 280325, 1, 500, 'Deposit of 500', '2024-03-29 20:58:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `IDNumber` int(11) NOT NULL,
  `FirstName` varchar(30) NOT NULL,
  `LastName` varchar(30) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(20) NOT NULL,
  `DateCreated` datetime NOT NULL DEFAULT current_timestamp(),
  `isAdmin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`IDNumber`, `FirstName`, `LastName`, `Username`, `Password`, `DateCreated`, `isAdmin`) VALUES
(231564, 'LBYCPG2', 'Lamberte', 'admin', 'Carl12179310', '2024-03-28 21:17:05', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`AccountNumber`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`TransactionID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`IDNumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `AccountNumber` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=280330;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `TransactionID` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=303567;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `IDNumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231565;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
