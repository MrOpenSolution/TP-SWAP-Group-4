-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 03, 2025 at 12:33 PM
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
-- Database: `requests`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('Admin','Procurement Officer','Department Head') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `email`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$NhY03M8Z8Ms8RCE0tkRvFe1NVTLmI3Ji1ErgEWEQyy9kgR9/EwUMm', 'admin@example.com', 'Admin', '2025-01-28 23:38:11', '2025-02-03 10:06:40'),
(2, 'officer', '$2y$10$I9X3PV9n5KFBu8kFEq0EWeKudrOOOUr/pd2jI2il5PX17Ke/8UwnS', 'officer@example.com', 'Procurement Officer', '2025-01-29 01:00:07', '2025-02-03 10:06:40'),
(3, 'head', '$2y$10$W7P.KkVtZvdk8XiS5fQzqeivPzGKoAXRsBEaifWygkwh2Y/948fS.', 'head@example.com', 'Department Head', '2025-01-29 01:00:07', '2025-02-03 10:06:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
