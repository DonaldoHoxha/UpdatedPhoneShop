-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 11, 2025 alle 15:06
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

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
-- Struttura della tabella `administrator_user`
--

CREATE TABLE `administrator_user` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `administrator_user`
--

INSERT INTO `administrator_user` (`id`, `name`, `password`) VALUES
(1, 'try', 'try'),
(2, 'try2', 'try'),
(3, 'donald', '12345678'),
(4, 'donald1', '$2y$10$0bw1CJ8azmJvnhWcAZ1Y/OQC7bJYaY6Wn4y/JE00FIjTR/KxEK7IO'),
(9, 'qiu@gmail.com', '$2y$10$1eHAAvJDXW/AdbTQcScv1uU4t/n6HaDXl/s4oEMYpV/g2bL0taC/e'),
(10, 'qiu100@gmail.com', '$2y$10$UTuuJgY/Fh58537T4BxuouhRaTTGr3nZFzaj4J7U2GgrAci6riwju'),
(11, 'prova@gmail.com', '$2y$10$2nJaiRrZgp2EbrUaUmQIJu5nTHjmB5nXY.z9Q1mdFJPR/Kn3YWQVe'),
(12, 'qiu1232@gmail.com', '$2y$10$YZjg7Ysj9Jh7ItHKPbUyZOvu0SjC8oSeemaxUaKlaWl6g4upUhEf.'),
(13, 'qiu12333', '$2y$10$c8sHUGaLEuuAP1gPs8qx..z6hvVxMVOI0S.jbQo0RR.sdKc82AVtG'),
(14, 'qiu2797223631@gmail.com', '$2y$10$HN.R..pdHDOwFb/zChzE6ux/dJu1oVFJbilaz6b1qiYnJ88GQUURO');

-- --------------------------------------------------------

--
-- Struttura della tabella `cart`
--

CREATE TABLE `cart` (
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `cart`
--

INSERT INTO `cart` (`user_id`, `product_id`, `quantity`) VALUES
(4, 1, 6),
(4, 2, 1),
(4, 3, 2),
(5, 2, 1),
(5, 3, 2),
(5, 4, 1),
(6, 1, 4),
(6, 2, 2),
(6, 3, 1),
(10, 1, 1),
(10, 2, 5),
(10, 3, 2),
(10, 4, 1),
(10, 5, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_price` decimal(10,2) NOT NULL,
  `shipping_address` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `order_date`, `quantity`, `total_price`, `shipping_address`) VALUES
(1, 4, 1, '2025-04-14 13:39:47', 2, 2398.00, 'via 123'),
(2, 4, 4, '2025-04-14 13:40:19', 1, 40.00, 'via 123'),
(3, 10, 1, '2025-04-14 18:13:58', 3, 3597.00, '1234567890'),
(4, 10, 2, '2025-04-14 18:13:58', 2, 1000.00, '1234567890'),
(5, 10, 1, '2025-04-17 15:25:02', 6, 7194.00, '1234567890'),
(6, 10, 2, '2025-04-17 15:25:02', 1, 500.00, '1234567890'),
(7, 10, 3, '2025-04-17 15:25:02', 1, 200.00, '1234567890'),
(8, 10, 4, '2025-04-17 15:25:02', 1, 40.00, '1234567890'),
(9, 10, 5, '2025-04-17 15:25:02', 1, 600.00, '1234567890'),
(10, 10, 1, '2025-04-17 15:45:05', 1, 2398.00, '1234567890'),
(11, 10, 2, '2025-04-17 15:45:07', 1, 1000.00, '1234567890'),
(12, 10, 3, '2025-04-17 15:45:09', 1, 400.00, '1234567890'),
(13, 10, 4, '2025-04-17 15:45:28', 1, 80.00, '1234567890'),
(16, 10, 1, '2025-04-17 15:58:34', 1, 1199.00, '1234567890'),
(17, 10, 2, '2025-04-17 15:58:49', 1, 500.00, '1234567890'),
(18, 10, 3, '2025-04-17 16:01:34', 1, 200.00, '1234567890'),
(19, 10, 4, '2025-04-17 16:01:35', 1, 40.00, '1234567890'),
(20, 10, 5, '2025-04-17 16:01:36', 1, 600.00, '1234567890'),
(21, 10, 5, '2025-04-17 16:04:45', 1, 600.00, '1234567890'),
(22, 10, 1, '2025-04-17 16:05:24', 1, 1199.00, '1234567890'),
(23, 10, 1, '2025-04-17 16:05:24', 1, 1199.00, '1234567890'),
(24, 10, 1, '2025-04-17 16:05:25', 1, 1199.00, '1234567890'),
(25, 10, 3, '2025-04-17 16:05:25', 1, 200.00, '1234567890'),
(26, 10, 4, '2025-04-17 16:05:26', 1, 40.00, '1234567890'),
(27, 10, 5, '2025-04-18 12:51:37', 1, 600.00, '1234567890'),
(28, 10, 1, '2025-04-18 12:51:48', 1, 1199.00, '1234567890'),
(29, 10, 1, '2025-04-18 12:51:48', 1, 1199.00, '1234567890'),
(30, 10, 2, '2025-04-18 12:51:48', 1, 500.00, '1234567890'),
(38, 12, 6, '2025-05-08 20:01:27', 1, 700.00, 'vias fashfaei'),
(39, 13, 10, '2025-05-09 12:10:33', 1, 1200.00, 'via ss'),
(40, 13, 10, '2025-05-11 00:09:33', 1, 1200.00, 'via ss');

-- --------------------------------------------------------

--
-- Struttura della tabella `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `ram` int(11) NOT NULL,
  `rom` int(11) NOT NULL,
  `camera` int(11) NOT NULL,
  `battery` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `fk_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `product`
--

INSERT INTO `product` (`id`, `name`, `brand`, `ram`, `rom`, `camera`, `battery`, `price`, `quantity`, `fk_admin`) VALUES
(1, 'iPhone 15 Pro', 'Apple', 16, 512, 12, 4000, 1199, 10, 1),
(2, 'Samsung Galaxy A24', 'Samsung', 12, 256, 10, 5000, 500, 15, 1),
(3, 'xiaomi 1', 'xiaomi', 6, 64, 12, 2000, 200, 500, 1),
(4, 'huawei', 'huawei', 8, 128, 4, 4242, 40, 2, 2),
(5, 'iphone 12 pro ', 'apple', 12, 128, 8, 4444, 600, 30, 2),
(6, 'iphone 14', 'iphone', 6, 128, 2, 4220, 700, 99, 9),
(7, 'iphone15', 'iphone', 8, 256, 4, 3300, 1300, 50, 10),
(8, 'iphone15', 'iphone', 6, 128, 3, 3000, 4000, 20, 13),
(9, 'iphone15', 'iphone', 6, 64, 2, 3000, 900, 20, 13),
(10, 'xiaomi13pro', 'xiaomi', 10, 512, 3, 5000, 1200, 99, 14),
(11, '23', '345', 345, 34, 34, 2345, 45, 2345, 14),
(12, '3', '3', 3, 3, 3, 3, 3, 3, 14),
(13, '5', '5', 5, 5, 5, 5, 5, 5, 14),
(14, '5', '5', 6, 6, 6, 6, 6, 7, 14),
(15, '5', '5', 5, 5, 5, 5, 5, 5, 14),
(16, '3', '3', 2, 1, 3, 3, 4, 5, 14),
(17, '3', '3', 3, 3, 3, 3, 3, 3, 14),
(18, '2', '2', 2, 2, 2, 2, 2, 2, 14),
(19, '22', '2', 22, 11, 22, 33, 44, 55, 14),
(20, '1', '2', 3, 4, 5, 6, 7, 8, 14);

-- --------------------------------------------------------

--
-- Struttura della tabella `searches`
--

CREATE TABLE `searches` (
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `searched_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `searches`
--

INSERT INTO `searches` (`user_id`, `product_id`, `searched_at`) VALUES
(13, 10, '2025-05-10 22:09:20'),
(13, 11, '2025-05-10 21:52:38');

-- --------------------------------------------------------

--
-- Struttura della tabella `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `shipping_address` varchar(100) DEFAULT NULL,
  `registration_date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `email`, `shipping_address`, `registration_date`) VALUES
(4, 'don', '$2y$10$7kGOiOytHZWo/fuuwD3hsOfB6.ly2Kh0W4OFfDOvdc9vq24uBAtiS', 'don@gmail.com', NULL, '2025-04-22'),
(5, 'don1', '$2y$10$uWVg4T9STlST9Xp6EGdpm.Mv3/.V6VZgjHru2y5tQ2O/0OFzX6Ehi', 'don1@gmail.com', NULL, '2025-04-22'),
(6, 'don2', '$2y$10$BOj7F8oTWOZPMU39vMAR5u8bvY2jwmxE4Mcp4c3jqnPIpBz/0tzHW', 'don2@gmail.com', NULL, '2025-04-22'),
(7, 'don3', '$2y$10$juoNX/rAkXXX8TgoOUO8TOc9RY38yEPnkDiWehlyikxGFmaKDL4Wu', 'don3@gmail.com', NULL, '2025-04-22'),
(8, 'qiu1', '$2y$10$RPUKv1QFcrorE1S92bKJyOXcWVUlF/ORq/kKpShiXHII43kb84flO', 'qiu1@gmail.com', NULL, '2025-04-22'),
(9, 'don9', '$2y$10$9dXM8M6B8B1I04AQl.9zJ./g3s1.ZwTi.yrCJYlbCvhigKTzddCH.', 'bkaba@gmail.com', NULL, '2025-04-22'),
(10, 'updated_don', '$2y$10$MEiuKtAxcesR2Lul.zfySeGbzIXDBkKecfsQakTbBQ0ankEeApBMC', 'updated_don@gmail.com', '1234567890', '2025-04-22'),
(11, '123', '$2y$10$SgEwKUEOOf8.1nw0.FNgiOgLi.LSPnePPlyXS27ZQY5iXWal1lehm', '123@gmail.com', '1234567890', '2025-04-22'),
(12, 'test299', '$2y$10$N.lj.CFifCNiBW6Fwr5OwuBEB59lqN0e8XgMByDDWyc9sCGRE6Yey', 'test299@gmail.com', 'vias fashfaei', '2025-05-08'),
(13, 'prova', '$2y$10$UDpPa5I.VJNk0DsUUcmFBO0fp6p9K/cvmT6C7BYlxJvv/elkgbn6a', 'prova@gmail.com', 'via ss', '2025-05-09');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `administrator_user`
--
ALTER TABLE `administrator_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indici per le tabelle `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indici per le tabelle `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_user_id` (`user_id`),
  ADD KEY `FK_product_id` (`product_id`);

--
-- Indici per le tabelle `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admin` (`fk_admin`);

--
-- Indici per le tabelle `searches`
--
ALTER TABLE `searches`
  ADD PRIMARY KEY (`user_id`,`product_id`,`searched_at`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `searched_at` (`searched_at`);

--
-- Indici per le tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `password` (`password`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `administrator_user`
--
ALTER TABLE `administrator_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT per la tabella `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT per la tabella `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT per la tabella `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `FK_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `FK_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Limiti per la tabella `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_admin` FOREIGN KEY (`fk_admin`) REFERENCES `administrator_user` (`id`);

--
-- Limiti per la tabella `searches`
--
ALTER TABLE `searches`
  ADD CONSTRAINT `searches_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `searches_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
