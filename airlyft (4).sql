-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2025 at 06:05 PM
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
-- Database: `airlyft`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL,
  `street` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `Booking_Id` int(11) NOT NULL,
  `User_Id` int(11) NOT NULL,
  `Aircraft_Id` int(11) NOT NULL,
  `Selected_Date_of_Flight` date NOT NULL,
  `Sched_Id` int(11) DEFAULT NULL,
  `Total_Cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Booking_Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lift`
--

CREATE TABLE `lift` (
  `Aircraft_Id` int(11) NOT NULL,
  `Aircraft_Name` varchar(255) NOT NULL,
  `Capacity` varchar(50) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Rate` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lift`
--

INSERT INTO `lift` (`Aircraft_Id`, `Aircraft_Name`, `Capacity`, `Description`, `Rate`) VALUES
(1, 'Cessna Turbo Stationair HD (T206H)', 'Up to 5 passengers', 'A high-performance single-engine piston aircraft, ideal for short to medium-range flights and scenic tours. Known for its reliability and versatility.', 50000.00),
(2, 'Cessna Grand Caravan EX (Deluxe Config)', 'Up to 12 passengers', 'A powerful and reliable turboprop aircraft, perfect for larger groups or cargo. The deluxe configuration offers enhanced comfort and amenities.', 120000.00),
(3, 'Airbus H160', 'Up to 10 passengers', 'A next-generation medium twin-engine helicopter, offering superior performance, comfort, and safety. Ideal for executive transport and aerial tours.', 300000.00),
(4, 'Sikorsky S-76D', 'Up to 12 passengers', 'A highly sophisticated and reliable medium-sized commercial helicopter, renowned for its executive transport capabilities and long-range flights.', 450000.00);

-- --------------------------------------------------------

--
-- Table structure for table `passengers`
--

CREATE TABLE `passengers` (
  `Passenger_Id` int(11) NOT NULL,
  `Booking_Id` int(11) NOT NULL,
  `FName` varchar(100) NOT NULL,
  `LName` varchar(100) NOT NULL,
  `Age` int(11) NOT NULL,
  `Address_Id` int(11) DEFAULT NULL,
  `Has_Insurance` tinyint(1) NOT NULL,
  `Insurance_Details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `Booking_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_mode` varchar(255) NOT NULL,
  `ref_number` int(11) NOT NULL,
  `proof_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `places`
--

CREATE TABLE `places` (
  `Place_Id` int(11) NOT NULL,
  `Place_Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `places`
--

INSERT INTO `places` (`Place_Id`, `Place_Name`) VALUES
(8, 'Alphaland Baguio'),
(1, 'Amanpulo'),
(3, 'Amorita Resort'),
(11, 'Aureo La Union'),
(2, 'Balesin Island'),
(6, 'Banwa'),
(5, 'El Nido Resorts'),
(10, 'Farm San Benito Lipa'),
(4, 'Huma Island Resort'),
(7, 'Nay Palad'),
(9, 'Shangri-La Boracay');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `Sched_Id` int(11) NOT NULL,
  `Aircraft_Id` int(11) NOT NULL,
  `Departure_Coordinates` varchar(255) DEFAULT NULL,
  `Arrival_Coordinates` varchar(255) DEFAULT NULL,
  `Arr_Date_Time` datetime DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `Booked_Capacity` int(11) DEFAULT 0,
  `Dep_Date_Time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phonenumber` varchar(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phonenumber`, `password`, `role`) VALUES
(57, 'chrisian', 'chris@gmail.com', '09519678889', '$2y$10$vFGTFXEqgtw4Z/XmeANbZuxOeTh3QKjySY/Heow5.ggBU9/YpLcFi', 'user'),
(58, 'kalbow', 'kalbow@gmail.com', '09519678889', '$2y$10$YnuUt1MFqIFt75.3YLD2Oevmi7C2JQdjfRnkRGGBbrajyWtm5hg.C', 'user'),
(59, 'admin', 'admin@gmail.com', '', 'admin123', 'admin'),
(60, 'kalbow2', 'kalbow2@gmail.com', '09519678889', '$2y$10$0ACNf34IlycYJgrwVg7bmuPRLpt41AyFyN4JzfJ83m.kbOq.OH5i2', 'user'),
(61, 'chris', 'christo@gmail.com', '09664101238', '$2y$10$RguUc9mCQuvaeyFL4/5yl.WrToQRuX5duvhAqGt1d4DGD0RepwRJq', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`Booking_Id`);

--
-- Indexes for table `lift`
--
ALTER TABLE `lift`
  ADD PRIMARY KEY (`Aircraft_Id`),
  ADD UNIQUE KEY `Aircraft_Name` (`Aircraft_Name`);

--
-- Indexes for table `passengers`
--
ALTER TABLE `passengers`
  ADD PRIMARY KEY (`Passenger_Id`),
  ADD KEY `Booking_Id` (`Booking_Id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`Place_Id`),
  ADD UNIQUE KEY `name` (`Place_Name`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`Sched_Id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`,`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `Booking_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `lift`
--
ALTER TABLE `lift`
  MODIFY `Aircraft_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `passengers`
--
ALTER TABLE `passengers`
  MODIFY `Passenger_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `Sched_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `passengers`
--
ALTER TABLE `passengers`
  ADD CONSTRAINT `passengers_ibfk_1` FOREIGN KEY (`Booking_Id`) REFERENCES `booking` (`Booking_Id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
