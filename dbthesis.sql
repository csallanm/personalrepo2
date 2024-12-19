-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2024 at 02:56 PM
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
-- Database: `dbthesis`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblcourses`
--

CREATE TABLE `tblcourses` (
  `course` varchar(10) NOT NULL,
  `full_course` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcourses`
--

INSERT INTO `tblcourses` (`course`, `full_course`) VALUES
('ABPsy', 'Bachelor of Arts in Psychology'),
('ACS', 'Associate in Computer Science'),
('BEEd', 'Bachelor of Elementary Education'),
('BSA', 'Bachelor of Science in Accountancy'),
('BSBA', 'Bachelor of Science in Business Administration'),
('BSCoE', 'Bachelor of Science in Computer Engineering'),
('BSCS', 'Bachelor of Science in Computer Science'),
('BSEd', 'Bachelor of Secondary Education'),
('BSHM', 'Bachelor of Science in Hospitality Management'),
('BSN', 'Bachelor of Science in Nursing'),
('BSTM', 'Bachelor of Science in Tourism Management');

-- --------------------------------------------------------

--
-- Table structure for table `tbldocuments`
--

CREATE TABLE `tbldocuments` (
  `file_psa` varchar(255) DEFAULT NULL,
  `file_goodmoral` varchar(255) DEFAULT NULL,
  `file_transcre` varchar(255) DEFAULT NULL,
  `file_tor` varchar(255) DEFAULT NULL,
  `file_permrec` varchar(255) DEFAULT NULL,
  `file_f137` varchar(255) DEFAULT NULL,
  `file_f9` varchar(255) DEFAULT NULL,
  `file_marcon` varchar(255) DEFAULT NULL,
  `o_file` text DEFAULT NULL,
  `sid` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblroles`
--

CREATE TABLE `tblroles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblroles`
--

INSERT INTO `tblroles` (`role_id`, `role_name`) VALUES
(0, 'Staff'),
(1, 'Admin'),
(2, 'Head Admin');

-- --------------------------------------------------------

--
-- Table structure for table `tblstudents`
--

CREATE TABLE `tblstudents` (
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `midname` varchar(255) NOT NULL,
  `minit` varchar(1) NOT NULL,
  `suffix` varchar(16) NOT NULL,
  `course` varchar(10) NOT NULL,
  `section` varchar(16) NOT NULL,
  `major` varchar(256) NOT NULL,
  `ylvl` int(10) NOT NULL,
  `batch` int(11) NOT NULL,
  `sid` varchar(25) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(535) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `age` int(16) NOT NULL,
  `bday` date NOT NULL,
  `sex` varchar(32) NOT NULL,
  `filename` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `code` mediumint(50) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`id`, `name`, `email`, `password`, `code`, `role_id`, `status`) VALUES
(81, 'dummy2', 'allanmelitonalternate@gmail.com', '$2y$10$BZOS/F1vpskAGQ.XhAOpzuu28dwF53XOVQERL1uGxqyFeThfeudH.', 0, 2, 'verified');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblcourses`
--
ALTER TABLE `tblcourses`
  ADD UNIQUE KEY `course` (`course`);

--
-- Indexes for table `tblroles`
--
ALTER TABLE `tblroles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `tblstudents`
--
ALTER TABLE `tblstudents`
  ADD PRIMARY KEY (`sid`);

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD CONSTRAINT `fk_role` FOREIGN KEY (`role_id`) REFERENCES `tblroles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
