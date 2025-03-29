-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2025 at 01:55 PM
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
-- Database: `phoneshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrator_user`
--

CREATE TABLE `administrator_user` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `user_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`user_id`, `warehouse_id`, `quantity`) VALUES
(4, 1, 2),
(4, 3, 1),
(5, 2, 1),
(5, 3, 2),
(5, 4, 1),
(6, 1, 4),
(6, 2, 2),
(6, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `email`) VALUES
(2, 'hoxha', '$2y$10$zpYMKiAjGjPCBBOnBwstiucuS4mA1B7m9XP3j3TLRXbPmu.ZenFEG', 'donaldo.hoxha06@gmail.com'),
(3, 'fenis', '$2y$10$yApCpIxSu.NedBOAut2PQOHYPCAX7jtRsRKqwIEyWWSeyo2NGSE1.', 'ciao@gmail.com'),
(4, 'don', '$2y$10$7kGOiOytHZWo/fuuwD3hsOfB6.ly2Kh0W4OFfDOvdc9vq24uBAtiS', 'don@gmail.com'),
(5, 'don1', '$2y$10$uWVg4T9STlST9Xp6EGdpm.Mv3/.V6VZgjHru2y5tQ2O/0OFzX6Ehi', 'don1@gmail.com'),
(6, 'don2', '$2y$10$BOj7F8oTWOZPMU39vMAR5u8bvY2jwmxE4Mcp4c3jqnPIpBz/0tzHW', 'don2@gmail.com'),
(7, 'don3', '$2y$10$juoNX/rAkXXX8TgoOUO8TOc9RY38yEPnkDiWehlyikxGFmaKDL4Wu', 'don3@gmail.com'),
(8, 'qiu1', '$2y$10$RPUKv1QFcrorE1S92bKJyOXcWVUlF/ORq/kKpShiXHII43kb84flO', 'qiu1@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse`
--

CREATE TABLE `warehouse` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `price` float NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouse`
--

INSERT INTO `warehouse` (`id`, `name`, `brand`, `price`, `quantity`) VALUES
(1, 'iPhone 15 Pro', 'Apple', 1199, 10),
(2, 'Samsung Galaxy A24', 'Samsung', 500, 15),
(3, 'xiaomi 1', 'xiaomi', 200, 500),
(4, 'huawei', 'huawei', 40, 2),
(5, 'iphone 12 pro ', 'apple', 600, 30);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrator_user`
--
ALTER TABLE `administrator_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`user_id`,`warehouse_id`),
  ADD KEY `warehouse_id` (`warehouse_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `password` (`password`);

--
-- Indexes for table `warehouse`
--
ALTER TABLE `warehouse`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrator_user`
--
ALTER TABLE `administrator_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `warehouse`
--
ALTER TABLE `warehouse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
