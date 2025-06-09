-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 09:21 PM
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
-- Database: `todolistdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `created_at`) VALUES
(5, 8, 'User logged in', '2025-06-09 22:38:42'),
(6, 9, 'User logged in', '2025-06-09 22:39:40'),
(7, 8, 'User logged in', '2025-06-09 22:42:56'),
(8, 9, 'User logged in', '2025-06-09 22:43:06'),
(9, 8, 'User logged in', '2025-06-09 22:43:17'),
(10, 8, 'User logged in', '2025-06-09 22:55:25'),
(11, 8, 'Added task: h', '2025-06-09 22:57:15'),
(12, 10, 'User logged in', '2025-06-09 23:02:31'),
(13, 8, 'User logged in', '2025-06-09 23:02:38'),
(14, 8, 'Deleted task: sd', '2025-06-09 23:04:37'),
(15, 8, 'Marked task \'h\' as Completed', '2025-06-09 23:05:50'),
(16, 8, 'Updated task \'h\'', '2025-06-09 23:05:59'),
(17, 8, 'Deleted task: h', '2025-06-09 23:14:00'),
(18, 8, 'Added task: uuu', '2025-06-09 23:14:15'),
(19, 8, 'Deleted task: uuu', '2025-06-09 23:14:28'),
(20, 8, 'Deleted task: sad', '2025-06-09 23:15:10'),
(21, 12, 'User logged in', '2025-06-09 23:17:41'),
(22, 12, 'Added task: gymn', '2025-06-09 23:17:53'),
(23, 12, 'Added task: f', '2025-06-09 23:17:59'),
(24, 12, 'Marked task \'gymn\' as Completed', '2025-06-09 23:18:07'),
(25, 12, 'Deleted task: f', '2025-06-09 23:19:27');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `ID` int(11) NOT NULL,
  `Title` varchar(50) NOT NULL,
  `Priority` varchar(50) NOT NULL,
  `Status` varchar(50) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `user_ID` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`ID`, `Title`, `Priority`, `Status`, `category`, `user_ID`) VALUES
(17, 'gymn', 'Low', 'Completed', 'Work', '12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `Username`, `Email`, `Password`, `role`) VALUES
(7, 'anri', 'anri@gmail.com', '$2y$10$gK1DS7r1A1Eg0fVdWrNvfO8oCtkUCGQu76bEOh2jD1VAmZPcIkVg6', 'user'),
(8, 'scar', 'scar@asdasd', '$2y$10$KcgRHIovsQqlH6JjDB/FZ.wIxC4yTJZdbjfcBMy/H.pDceMPji6lu', 'admin'),
(9, 'sk', 'sk@gmail.com', '$2y$10$Vgtm4U2bUzVjil2JI9/aZODtu0fntGIkvesY99Y2SIc/vpMYJ6gmO', 'user'),
(10, 'sad', 'sad@g', '$2y$10$xsuADmVvdiwn2DyPaxJRUu3x.qE4g4tmnAbQTyFuDpskykvJcwWpC', 'user'),
(11, 'scar', 'scar@gmail.com', '$2y$10$8oirjbs7ehLKpVkEuKf81uzPR/imp.HYsEPKFN71XfhEMevImapAu', 'admin'),
(12, 'a', 'a@gmail.com', '$2y$10$bCjlHyfvFBvSWhn8F02x8OVV.Ycj.he181Xvz/Znb0.miu49Ivs9.', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`ID`),
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
