-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2025 at 09:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chickenfarm`
--

-- --------------------------------------------------------

--
-- Table structure for table `agriculturist`
--

CREATE TABLE `agriculturist` (
  `agc_id` int(11) NOT NULL,
  `agc_name` varchar(40) NOT NULL,
  `agc_Fname` varchar(40) NOT NULL,
  `agc_phone` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agriculturist`
--

INSERT INTO `agriculturist` (`agc_id`, `agc_name`, `agc_Fname`, `agc_phone`) VALUES
(13, 'teerayut chankaew', 'TT Farm', '0874512566'),
(14, 'จิราภรณ์ จันทร์แก้ว', 'จิราภรณ์จำกัด', '0630725218'),
(15, 'jams jams', 'เจมส์จำกัด', '0122222222'),
(16, 'test1', '333', '123456789'),
(17, '15', '0630725218', 'teerayut c'),
(18, '15', '0630725218', 'teerayut c'),
(19, '2025-03-13', '', ''),
(21, 'บรรยาย', 'bb1', '0621158501');

-- --------------------------------------------------------

--
-- Table structure for table `data_chick`
--

CREATE TABLE `data_chick` (
  `dc_id` int(11) NOT NULL,
  `dc_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `dc_quan` decimal(10,2) NOT NULL,
  `dc_price` decimal(10,2) NOT NULL,
  `agc_id` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_chick`
--

INSERT INTO `data_chick` (`dc_id`, `dc_date`, `dc_quan`, `dc_price`, `agc_id`) VALUES
(5, '2025-03-17 07:45:32', 2800.00, 16800.00, '15');

-- --------------------------------------------------------

--
-- Table structure for table `data_chick_detail`
--

CREATE TABLE `data_chick_detail` (
  `dcd_id` int(11) NOT NULL,
  `dcd_date` date NOT NULL,
  `dcd_quan` decimal(10,2) NOT NULL,
  `dcd_price` decimal(10,2) NOT NULL,
  `agc_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_chick_detail`
--

INSERT INTO `data_chick_detail` (`dcd_id`, `dcd_date`, `dcd_quan`, `dcd_price`, `agc_id`) VALUES
(22, '2025-03-17', 100.00, 300.00, 16),
(23, '2025-03-17', 100.00, 300.00, 16),
(24, '2025-03-17', 100.00, 200.00, 15),
(25, '2025-03-17', 200.00, 400.00, 15),
(26, '2025-03-17', 200.00, 500.00, 15);

-- --------------------------------------------------------

--
-- Table structure for table `data_feeding`
--

CREATE TABLE `data_feeding` (
  `feed_id` int(11) NOT NULL,
  `feed_date` date NOT NULL,
  `feed_name` varchar(100) NOT NULL,
  `feed_quan` decimal(10,2) NOT NULL,
  `feed_price` decimal(10,2) NOT NULL,
  `agc_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_feeding`
--

INSERT INTO `data_feeding` (`feed_id`, `feed_date`, `feed_name`, `feed_quan`, `feed_price`, `agc_id`) VALUES
(1, '2025-03-18', 'BB3', 50.00, 500.00, 15),
(2, '2025-03-13', 'BB3', 50.00, 150.00, 15),
(3, '2025-03-17', 'bb1', 1.00, 200.00, 21),
(4, '2025-03-17', 'dd1', 20.00, 500.00, 15);

-- --------------------------------------------------------

--
-- Table structure for table `data_food`
--

CREATE TABLE `data_food` (
  `df_id` int(11) NOT NULL,
  `df_name` varchar(100) NOT NULL,
  `agc_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_food`
--

INSERT INTO `data_food` (`df_id`, `df_name`, `agc_id`) VALUES
(1, 'BB5', 15),
(2, 'BB3', 15),
(3, 'BB2', 15),
(4, 'BB6', 15),
(5, 'bb1', 21),
(6, 'nn1', 15),
(7, 'dd1', 15),
(8, 'ff1', 15);

-- --------------------------------------------------------

--
-- Table structure for table `data_inex`
--

CREATE TABLE `data_inex` (
  `inex_id` int(11) NOT NULL,
  `inex_date` date NOT NULL,
  `inex_type` varchar(10) NOT NULL,
  `inex_name` varchar(100) NOT NULL,
  `inex_price` decimal(10,2) NOT NULL,
  `agc_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_inex`
--

INSERT INTO `data_inex` (`inex_id`, `inex_date`, `inex_type`, `inex_name`, `inex_price`, `agc_id`) VALUES
(1, '2025-03-05', 'รายรับ', 'ทุเรียนหมอนทอง', 2500.00, 15);

-- --------------------------------------------------------

--
-- Table structure for table `data_sale`
--

CREATE TABLE `data_sale` (
  `sale_id` int(11) NOT NULL,
  `sale_date` date NOT NULL,
  `sale_quan` decimal(10,2) NOT NULL,
  `sale_weigth` decimal(10,2) NOT NULL,
  `sale_priceKg` decimal(10,2) NOT NULL,
  `sale_total` decimal(10,2) NOT NULL,
  `agc_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_sale`
--

INSERT INTO `data_sale` (`sale_id`, `sale_date`, `sale_quan`, `sale_weigth`, `sale_priceKg`, `sale_total`, `agc_id`) VALUES
(1, '2025-03-13', 50.00, 200.00, 30.00, 6000.00, 15);

-- --------------------------------------------------------

--
-- Table structure for table `user_login`
--

CREATE TABLE `user_login` (
  `us_id` int(11) NOT NULL,
  `us_name` varchar(50) NOT NULL,
  `us_pass` varchar(50) NOT NULL,
  `us_role` varchar(1) NOT NULL,
  `agc_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_login`
--

INSERT INTO `user_login` (`us_id`, `us_name`, `us_pass`, `us_role`, `agc_id`) VALUES
(10, 'joe_wara', '12345', '1', '12'),
(11, 'admin', '123', '1', '13'),
(12, '12345', '67890', '2', '14'),
(13, 'jams', '12345', '2', '15'),
(14, 'agc', '11111', '2', '16'),
(16, '', '', '2', '20'),
(17, '321', '321', '2', '21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agriculturist`
--
ALTER TABLE `agriculturist`
  ADD PRIMARY KEY (`agc_id`);

--
-- Indexes for table `data_chick`
--
ALTER TABLE `data_chick`
  ADD PRIMARY KEY (`dc_id`);

--
-- Indexes for table `data_chick_detail`
--
ALTER TABLE `data_chick_detail`
  ADD PRIMARY KEY (`dcd_id`);

--
-- Indexes for table `data_feeding`
--
ALTER TABLE `data_feeding`
  ADD PRIMARY KEY (`feed_id`);

--
-- Indexes for table `data_food`
--
ALTER TABLE `data_food`
  ADD PRIMARY KEY (`df_id`);

--
-- Indexes for table `data_inex`
--
ALTER TABLE `data_inex`
  ADD PRIMARY KEY (`inex_id`);

--
-- Indexes for table `data_sale`
--
ALTER TABLE `data_sale`
  ADD PRIMARY KEY (`sale_id`);

--
-- Indexes for table `user_login`
--
ALTER TABLE `user_login`
  ADD PRIMARY KEY (`us_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agriculturist`
--
ALTER TABLE `agriculturist`
  MODIFY `agc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `data_chick`
--
ALTER TABLE `data_chick`
  MODIFY `dc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `data_chick_detail`
--
ALTER TABLE `data_chick_detail`
  MODIFY `dcd_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `data_feeding`
--
ALTER TABLE `data_feeding`
  MODIFY `feed_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `data_food`
--
ALTER TABLE `data_food`
  MODIFY `df_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `data_inex`
--
ALTER TABLE `data_inex`
  MODIFY `inex_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `data_sale`
--
ALTER TABLE `data_sale`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_login`
--
ALTER TABLE `user_login`
  MODIFY `us_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
