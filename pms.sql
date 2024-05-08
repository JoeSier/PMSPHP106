-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2024 at 01:14 PM
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
                           `PhoneNumber` varchar(20) NOT NULL,
                           `reset_token_hash` varchar(64) DEFAULT NULL,
                           `reset_token_expires_at` datetime DEFAULT NULL,
                           `account_activation_hash` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`UserID`, `IsAdmin`, `Firstname`, `Surname`, `Credit`, `Username`, `UserPassword`, `Email`, `PhoneNumber`, `reset_token_hash`, `reset_token_expires_at`, `account_activation_hash`) VALUES
    (1, 1, 'ADMIN', 'ADMIN', 0.00, 'ADMIN', '$2y$10$2KwwhydG3Z.dRhjRB45LmO0JNV6rsEZ3wxDfinn8tHyXkbkXM..Iq', 'Parklyuser@outlook.com', '1234567890', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
                           `BookingID` int(11) NOT NULL,
                           `UserID` int(11) NOT NULL,
                           `ParkingSpaceID` int(50) NOT NULL,
                           `LicensePlate` varchar(7) NOT NULL,
                           `BookingCost` decimal(10,2) NOT NULL,
                           `timeStart` timestamp NOT NULL DEFAULT current_timestamp(),
                           `timeEnd` timestamp NULL DEFAULT NULL,
                           `LotName` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--


-- --------------------------------------------------------

--
-- Table structure for table `car`
--

CREATE TABLE `car` (
                       `UserID` int(11) NOT NULL,
                       `LicensePlate` varchar(7) NOT NULL,
                       `CarType` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car`
--

INSERT INTO `car` (`UserID`, `LicensePlate`, `CarType`) VALUES
    (1, 'ADMIN', 'Hatchback');

-- --------------------------------------------------------

--
-- Table structure for table `parkinglots`
--

CREATE TABLE `parkinglots` (
                               `TotalSpaces` int(11) NOT NULL,
                               `LotName` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parkinglots`
--

INSERT INTO `parkinglots` (`TotalSpaces`, `LotName`) VALUES
    (30, 'UEA main');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
    ADD PRIMARY KEY (`UserID`),
    ADD UNIQUE KEY `PhoneNumber` (`PhoneNumber`),
    ADD UNIQUE KEY `Email` (`Email`),
    ADD UNIQUE KEY `reset_token_hash` (`reset_token_hash`),
    ADD UNIQUE KEY `account_activation_hash` (`account_activation_hash`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
    ADD PRIMARY KEY (`BookingID`),
    ADD KEY `UserID` (`UserID`),
    ADD KEY `LicensePlate` (`LicensePlate`),
    ADD KEY `ParkingSpaceID` (`ParkingSpaceID`) USING BTREE;

--
-- Indexes for table `car`
--
ALTER TABLE `car`
    ADD PRIMARY KEY (`LicensePlate`),
    ADD UNIQUE KEY `LicensePlate` (`LicensePlate`),
    ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `parkinglots`
--
ALTER TABLE `parkinglots`
    ADD PRIMARY KEY (`LotName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
    MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
    MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
    ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `account` (`UserID`),
    ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`LicensePlate`) REFERENCES `car` (`LicensePlate`),
    ADD CONSTRAINT `booking_ibfk_3` FOREIGN KEY (`LotName`) REFERENCES `parkinglots` (`LotName`);

--
-- Constraints for table `car`
--
ALTER TABLE `car`
    ADD CONSTRAINT `car_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `account` (`UserID`);
COMMIT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
