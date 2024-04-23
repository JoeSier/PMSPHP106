-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 23, 2024 at 05:53 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pms`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `UserID` int(11) NOT NULL,
  `IsAdmin` tinyint(1) NOT NULL DEFAULT 0,
  `Firstname` varchar(40) NOT NULL,
  `Surname` varchar(40) NOT NULL,
  `Credit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Username` varchar(50) NOT NULL,
  `UserPassword` varchar(255) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`UserID`, `IsAdmin`, `Firstname`, `Surname`, `Credit`, `Username`, `UserPassword`, `Email`, `PhoneNumber`) VALUES
(1, 0, 'test', 'test', 0.00, 'test', 'password123', 'test@gmail.com', '458023342'),
(2, 0, 'billy', 'mays', 0.00, 'asdsdads', '$2y$10$C5fSVepULVYLi.i0FxCp.eRoKGSbgJqhyAwMUhlalPxrhn5L6nACG', 'asda@gmail.com', 't53535435');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `PhoneNumber` (`PhoneNumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
