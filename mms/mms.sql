-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 14, 2025 at 07:06 PM
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
-- Database: `mms`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(255) NOT NULL,
  `userid` int(255) NOT NULL,
  `movieid` int(255) NOT NULL,
  `tickets` int(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `userid`, `movieid`, `tickets`, `date`, `time`) VALUES
(1, 1, 3, 2, '2025-01-15', '18:30:00'),
(2, 2, 1, 4, '2025-01-16', '20:00:00'),
(3, 3, 5, 1, '2025-01-15', '21:00:00'),
(4, 4, 2, 3, '2025-01-17', '19:00:00'),
(5, 5, 4, 2, '2025-01-18', '16:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(255) NOT NULL,
  `moviename` varchar(255) NOT NULL,
  `moviedesc` varchar(255) NOT NULL,
  `moviequality` varchar(255) NOT NULL,
  `movierating` int(255) NOT NULL,
  `movieimage` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `moviename`, `moviedesc`, `moviequality`, `movierating`, `movieimage`) VALUES
(1, 'Inception', 'A thief who steals corporate secrets through dream-sharing technology is given the inverse task of planting an idea.', 'HD', 9, 'inception.jpg'),
(2, 'The Matrix', 'A computer hacker learns about the true nature of reality and his role in the war against its controllers.', 'HD', 9, 'matrix.jpg'),
(3, 'The Godfather', 'The aging patriarch of an organized crime dynasty transfers control to his reluctant son.', 'SD', 9, 'godfather.jpg'),
(4, 'Avengers: Endgame', 'The Avengers assemble to undo the damage caused by Thanos and restore the universe.', '4K', 8, 'endgame.jpg'),
(5, 'The Dark Knight', 'Batman battles the Joker, a criminal mastermind who seeks to create chaos in Gotham City.', 'HD', 9, 'dark_knight.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `confirmpassword` varchar(255) NOT NULL,
  `isadmin` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `confirmpassword`, `isadmin`) VALUES
(1, 'John Doe', 'johndoe', 'johndoe@example.com', 'password123', 'password123', '0'),
(2, 'Jane Smith', 'janesmith', 'janesmith@example.com', 'password456', 'password456', '0'),
(3, 'Admin User', 'admin', 'admin@example.com', 'adminpass', 'adminpass', '1'),
(4, 'Alice Brown', 'alicebrown', 'alice.brown@example.com', 'alicepwd', 'alicepwd', '0'),
(5, 'Bob White', 'bobwhite', 'bob.white@example.com', 'bobpass', 'bobpass', '0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
