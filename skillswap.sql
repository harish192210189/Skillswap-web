-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 27, 2025 at 09:06 AM
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
-- Database: `skillswap`
--

-- --------------------------------------------------------

--
-- Table structure for table `connections`
--

CREATE TABLE `connections` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `status` enum('pending','accepted','declined') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `connections`
--

INSERT INTO `connections` (`id`, `sender_id`, `receiver_id`, `status`) VALUES
(1, 1, 2, 'accepted'),
(2, 3, 2, 'accepted'),
(3, 1, 3, 'accepted'),
(4, 2, 1, 'accepted'),
(5, 1, 1, ''),
(6, 11, 1, 'accepted'),
(7, 11, 2, 'pending'),
(8, 11, 5, 'pending'),
(9, 11, 9, ''),
(10, 11, 11, 'pending'),
(11, 1, 11, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `timestamp`) VALUES
(1, 2, 1, 'hi', '2025-02-18 16:38:08'),
(2, 3, 1, 'hi', '2025-02-19 03:24:39'),
(3, 1, 2, 'hi', '2025-02-19 07:58:27'),
(4, 2, 1, 'hi', '2025-02-19 08:05:55'),
(5, 1, 2, '637894512', '2025-02-19 10:25:21'),
(6, 2, 1, 'hi', '2025-02-19 10:27:22'),
(7, 1, 2, 'hwo r u', '2025-02-19 10:27:35');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reviewer_name` varchar(255) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `reviewer_name`, `rating`, `review_text`, `created_at`) VALUES
(7, 5, 'kumar', 4, 'hi', '2025-02-21 14:28:18'),
(8, 1, 'sam ', 5, 'he was good at teaching', '2025-02-26 08:31:59');

-- --------------------------------------------------------

--
-- Table structure for table `userregister`
--

CREATE TABLE `userregister` (
  `id` int(11) NOT NULL,
  `Fullname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `mobile` varchar(11) NOT NULL,
  `skills` varchar(255) NOT NULL,
  `experience` varchar(50) NOT NULL,
  `dob` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userregister`
--

INSERT INTO `userregister` (`id`, `Fullname`, `email`, `password`, `mobile`, `skills`, `experience`, `dob`) VALUES
(1, 'harish a', 'hari@gmail.com', '$2y$10$RCiNOLGEKp99XLkcg8ss2eT56EXxHR11fVJmhpHqaqzU7FNL2cb6S', '9874563210', 'java,php', '3', '2025-02-12'),
(2, 'deepan', 'de@gmail.com', '$2y$10$Rg6/rEfmE.Ugzk6ZSkqDLOKITpon//iXlimjkJPWvAUGF0SWxyZEe', '7894563210', 'c program', '3', '2025-02-05'),
(3, 'pothy', 'po@gmail.com', '$2y$10$PURupn4uiN6dYuS/yaBD9ui5ZwzOYqcWa6XXcWvegLnzbG8h0SVpG', '7896541230', 'python', '3', '2025-02-12'),
(5, 'dani', 'da@gmail.com', '$2y$10$zXTlv4pAl36ztptxA3RIN.9oVBckIa7zMIs0AqvLkqc.DDjMB1vbC', '0987654321', 'c++', '6', '2025-02-05'),
(8, 'dani', 'dan@gmail.com', '$2y$10$PoCZrLn2z4k/0YYngrgny.N7IC5Pi0Q78aFuPd9YNrVTbxhJMWgEK', '0987654321', 'java', '5', '2025-02-04'),
(9, 'sam', 'sam@gmail.com', '$2y$10$gz5VCta1g3bHUbZexggfEeuto5bG6VL/32oQsEEk/ZEPhJ5CqqVZO', '09876543321', 'backend', '5', '2025-02-06'),
(11, 'sam', 'sams@gmail.com', '$2y$10$okK8TlkUE38AbbVqKNmnjebOpu1SVsavbWpcN5039Rfqa4fWDbBJ2', '09876543321', 'backend', '5', '2025-02-06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `connections`
--
ALTER TABLE `connections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `userregister`
--
ALTER TABLE `userregister`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `connections`
--
ALTER TABLE `connections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `userregister`
--
ALTER TABLE `userregister`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `connections`
--
ALTER TABLE `connections`
  ADD CONSTRAINT `connections_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `userregister` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `connections_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `userregister` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `userregister` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `userregister` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `userregister` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
