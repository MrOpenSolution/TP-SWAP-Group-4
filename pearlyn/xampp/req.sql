-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 03, 2025 at 12:34 PM
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
-- Table structure for table `req`
--

CREATE TABLE `req` (
  `request_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `priority_level` enum('Low','Medium','High') NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `request_date` datetime DEFAULT current_timestamp(),
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `req`
--

INSERT INTO `req` (`request_id`, `item_name`, `quantity`, `department`, `priority_level`, `created_by`, `request_date`, `status`) VALUES
(20, 'test', 8, '01', 'Low', 'oo', '2025-02-03 00:00:00', 'Pending'),
(21, 'test', 8, '01', 'Low', 'oo', '2025-02-03 00:00:00', 'Pending'),
(22, 'test', 8, '01', 'Low', 'oo', '2025-02-03 00:00:00', 'Pending'),
(23, 'test', 8, '01', 'Low', 'oo', '2025-02-03 00:00:00', 'Pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `req`
--
ALTER TABLE `req`
  ADD PRIMARY KEY (`request_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `req`
--
ALTER TABLE `req`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
