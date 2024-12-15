-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2024 at 04:11 PM
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

--
-- Dumping data for table `tbldocuments`
--

INSERT INTO `tbldocuments` (`file_psa`, `file_goodmoral`, `file_transcre`, `file_tor`, `file_permrec`, `file_f137`, `file_f9`, `file_marcon`, `o_file`, `sid`) VALUES
('DUPLICATED BIRTHCERT DOCUMENT.pdf', 'DUPLICATED GOOD MORAL DOCUMENT.pdf', 'DUPLICATED TRANSFER CREDENTIALS DOCUMENT.pdf', 'DUPLICATED TOR CREDENTIALS DOCUMENT.pdf', 'DUPLICATED PERMANENT RECORD DOCUMENT.pdf', 'DUPLICATED FORM 137 DOCUMENT.pdf', 'DUPLICATED FORM 9 DOCUMENT.pdf', 'DUPLICATED MARRIAGE CONTRACT DOCUMENT.pdf', NULL, 'A00-0000'),
('dummy birthcert.pdf', 'dummy good moral.pdf', 'dummy transfer credentials.pdf', 'dummy tor.pdf', 'dummy permanent record.pdf', 'dummy form 137.pdf', 'dummy form 9.pdf', 'dummy marriage contract.pdf', 'dummy opt2.pdf,dummy opt1.pdf', 'A00-0001'),
('', '', '', '', '', '', '', '', NULL, 'A00-0002'),
('', '', '', '', '', '', '', '', NULL, 'A00-0003'),
('SIMULATED BIRTHCERT DOCUMENT.pdf', 'SIMULATED GOOD MORAL DOCUMENT.pdf', 'SIMULATED TRANSFER CREDENTIALS DOCUMENT.pdf', 'SIMULATED TOR CREDENTIALS DOCUMENT.pdf', 'SIMULATED PERMANENT RECORD DOCUMENT.pdf', 'SIMULATED FORM 137 DOCUMENT.pdf', 'SIMULATED FORM 9 DOCUMENT.pdf', '', NULL, 'A00-0004'),
('Simulated psa.pdf', 'Simulated good moral.pdf', 'Simulated transfer cred.pdf', 'Simulated tor.pdf', 'Simulated permanent rec.pdf', 'Simulated form137.pdf', 'Simulated form 9.pdf', 'Simulated marriage contract.pdf', 'Simulated optional 1.docx,Simulated optional 3.docx', 'A00-0005'),
('dum\'s psa 3.pdf', 'dum\'s goodmoral 3.pdf', 'dum\'s transcre 3.pdf', 'dum\'s tor 3.pdf', 'dum\'s permarec 3.pdf', 'dum\'s form137 3.pdf', 'dum\'s form9 3.pdf', 'dum\'s marriagecon 3.pdf', 'dum\'s option2 3.pdf,dum\'s option1 3.pdf', 'A00-0006');

-- --------------------------------------------------------

--
-- Table structure for table `tblfacts`
--

CREATE TABLE `tblfacts` (
  `id` int(11) NOT NULL,
  `fact` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblfacts`
--

INSERT INTO `tblfacts` (`id`, `fact`) VALUES
(5, 'EUC SAS (Student Archiving System)'),
(6, 'this is a test'),
(7, 'hello'),
(8, 'This is develoed by CCS department.'),
(9, 'Make your day great!'),
(10, 'I got this! Never giver up!'),
(11, 'MSEUF-CI, was established in 1992. The Luzonian was the first name of MSEUF.'),
(12, 'MSEUF-CI is resputable Education institution that offers a wide range of programs from elementary to college levels.');

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
(2, 'Super Admin');

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
  `phone` int(16) NOT NULL,
  `age` int(16) NOT NULL,
  `bday` date NOT NULL,
  `sex` varchar(32) NOT NULL,
  `filename` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstudents`
--

INSERT INTO `tblstudents` (`fname`, `lname`, `midname`, `minit`, `suffix`, `course`, `section`, `major`, `ylvl`, `batch`, `sid`, `email`, `address`, `phone`, `age`, `bday`, `sex`, `filename`) VALUES
('eklj', 'skldad', 'Mkslj', 'M', '', 'BSA', '', '-', 1, 0, 'A00-0000', 'somd@domain.com', 'ospdkas', 324809328, 21, '2003-01-01', 'Male', 'a student.png'),
('adasdasdas', 'skldad', 'Mkslj', 'M', 'Jr.', 'ACS', 'A', '-', 2, 2024, 'A00-0001', 'dosom@domain.com', 'ospdkas', 2147483647, 21, '2003-01-02', 'Male', 'dancing banana.gif'),
('Test', 'Test', 'Test', 'T', 'Jr.', 'BSCoE', 'C', 'STEM', 1, 2024, 'A00-0002', 'adsa@domain.com', 'oiasuasn', 123, 21, '2003-02-03', 'Male', '56217b1ef6a69a2583ff13655d48bc53.jpg'),
('Another', 'Test', 'T', 'T', 'Sr.', 'BSBA', 'A', 'Financial Management', 2, 2023, 'A00-0003', 'test@domain.com', 'adasd', 122313, 24, '2000-03-02', 'Male', 'minecraft-steve-game-character-q13oX64-600.jpg'),
('dfdsfsdf', 'sfsafsa', 'Soasdunas', 'S', 'II', 'ABPsy', '', '-', 4, 2012, 'A00-0004', 'teo@domai.com', 'soiduasn', 142352, 3, '2021-02-03', 'Male', '66393c7bf72ac71bd1fc070be8b9cd52.jpg'),
('rewtwre', 'rterte', 'Rwrew', 'R', 'III', 'BSTM', 'B', '-', 4, 2017, 'A00-0005', 'osamaf@domain.com', 'iosudnsiou', 124314, 25, '1999-02-01', 'Male', 'a student cartoon version.png'),
('eklj', 'skldad', 'Mkslj', 'M', '', 'BSCS', '', '-', 2, 0, 'A00-0006', 'OASMFD@DOMAIN.COM', 'ospdkas', 3432432, 50, '1974-02-02', 'Male', 'baconguyroblox.png');

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
(70, 'dummy', 'allanmelitonalternate@gmail.com', '$2y$10$uKQPgHIDgaQP4ocxRmdrDeTSSDGJSRXlyGYWLusK99p5BwRcJJGa2', 0, 0, 'verified');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblcourses`
--
ALTER TABLE `tblcourses`
  ADD UNIQUE KEY `course` (`course`);

--
-- Indexes for table `tblfacts`
--
ALTER TABLE `tblfacts`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `tblfacts`
--
ALTER TABLE `tblfacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

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
