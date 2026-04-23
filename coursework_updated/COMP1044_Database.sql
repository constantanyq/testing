-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Apr 22, 2026 at 04:13 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `COMP1044_Database`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` varchar(20) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `admin_email` varchar(100) DEFAULT NULL,
  `admin_password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `admin_email`, `admin_password`) VALUES
('A001', 'Admin User', 'admin@university.edu', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `company_id` int NOT NULL,
  `company_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`company_id`, `company_name`) VALUES
(1, 'Petronas Digital Sdn Bhd'),
(2, 'Maybank IT Division'),
(3, 'Telekom Malaysia Berhad'),
(4, 'Grab Holdings Inc.'),
(5, 'CIMB Group Technology');

-- --------------------------------------------------------

--
-- Table structure for table `component`
--

CREATE TABLE `component` (
  `component_id` int NOT NULL,
  `component_name` varchar(200) NOT NULL,
  `component_weightage` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `component`
--

INSERT INTO `component` (`component_id`, `component_name`, `component_weightage`) VALUES
(1, 'Undertaking Tasks/Projects', 10.00),
(2, 'Health and Safety Requirements at the Workplace', 10.00),
(3, 'Connectivity and Use of Theoretical Knowledge', 10.00),
(4, 'Presentation of the Report as a Written Document', 15.00),
(5, 'Clarity of Language and Illustration', 10.00),
(6, 'Lifelong Learning Activities', 15.00),
(7, 'Project Management', 15.00),
(8, 'Time Management', 15.00);

-- --------------------------------------------------------

--
-- Table structure for table `internship`
--

CREATE TABLE `internship` (
  `internship_id` int NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `lecturer_id` varchar(20) DEFAULT NULL,
  `supervisor_id` varchar(20) DEFAULT NULL,
  `company_id` int DEFAULT NULL,
  `s_marks_id` int DEFAULT NULL,
  `l_marks_id` int DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `average_marks` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `internship`
--

INSERT INTO `internship` (`internship_id`, `student_id`, `lecturer_id`, `supervisor_id`, `company_id`, `s_marks_id`, `l_marks_id`, `duration`, `average_marks`) VALUES
(100001, 'STU001', 'L001', 'S001', 1, 100001, 100001, 24, 82.08),
(100002, 'STU002', 'L001', 'S002', 2, 100002, 100002, 24, 53.50),
(100003, 'STU003', 'L002', 'S001', 3, 100003, 100003, 20, 37.30),
(100004, 'STU004', 'L002', 'S003', 4, NULL, NULL, 24, NULL),
(100005, 'STU005', 'L003', 'S002', 5, NULL, NULL, 20, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lecturer`
--

CREATE TABLE `lecturer` (
  `lecturer_id` varchar(20) NOT NULL,
  `lecturer_name` varchar(100) NOT NULL,
  `lecturer_email` varchar(100) DEFAULT NULL,
  `lecturer_password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer`
--

INSERT INTO `lecturer` (`lecturer_id`, `lecturer_name`, `lecturer_email`, `lecturer_password`) VALUES
('L001', 'Dr. Ahmad Farid', 'ahmad.farid@university.edu', 'lec123'),
('L002', 'Dr. Nurul Huda', 'nurul.huda@university.edu', 'lec123'),
('L003', 'Dr. Tan Wei Liang', 'tan.weiliang@university.edu', 'lec123');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_marks`
--

CREATE TABLE `lecturer_marks` (
  `l_marks_id` int NOT NULL,
  `component_id` int NOT NULL,
  `component_mark` decimal(5,2) NOT NULL DEFAULT '0.00',
  `comments` text,
  `total_marks` decimal(6,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_marks`
--

INSERT INTO `lecturer_marks` (`l_marks_id`, `component_id`, `component_mark`, `comments`, `total_marks`) VALUES
(100001, 1, 80.00, 'Completed tasks satisfactorily', 8.00),
(100001, 2, 85.00, 'Good awareness of safety', 8.50),
(100001, 3, 76.00, 'Could improve theoretical links', 7.60),
(100001, 4, 84.00, 'Report well written', 12.60),
(100001, 5, 78.00, 'Good clarity', 7.80),
(100001, 6, 82.00, 'Participated well', 12.30),
(100001, 7, 79.00, 'Project delivered on time', 11.85),
(100001, 8, 83.00, 'Punctual throughout', 12.45),
(100002, 1, 48.00, 'Needs more effort', 4.80),
(100002, 2, 55.00, 'Basic compliance only', 5.50),
(100002, 3, 45.00, 'Theory not well applied', 4.50),
(100002, 4, 50.00, 'Report needs improvement', 7.50),
(100002, 5, 48.00, 'Some clarity issues', 4.80),
(100002, 6, 55.00, 'Limited participation', 8.25),
(100002, 7, 50.00, 'Project barely managed', 7.50),
(100002, 8, 52.00, 'Sometimes late', 7.80),
(100003, 1, 40.00, 'Below expectations', 4.00),
(100003, 2, 42.00, 'Poor safety awareness', 4.20),
(100003, 3, 35.00, 'Cannot apply theory', 3.50),
(100003, 4, 38.00, 'Poorly structured', 5.70),
(100003, 5, 36.00, 'Poor writing skills', 3.60),
(100003, 6, 40.00, 'Did not participate', 6.00),
(100003, 7, 38.00, 'Poor time management', 5.70),
(100003, 8, 40.00, 'Consistently late', 6.00);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` varchar(20) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `student_password` varchar(100) NOT NULL,
  `student_email` varchar(100) DEFAULT NULL,
  `programme` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `student_name`, `student_password`, `student_email`, `programme`) VALUES
('STU001', 'Muhammad Ali bin Hassan', 'stu123', 'ali.hassan@student.edu', 'BSc Computer Science'),
('STU002', 'Siti Aishah binti Zulkifli', 'stu123', 'siti.aishah@student.edu', 'BSc Information Technology'),
('STU003', 'Chong Wei Jian', 'stu123', 'chong.weijian@student.edu', 'BSc Software Engineering'),
('STU004', 'Priya Rajendran', 'stu123', 'priya.rajendran@student.edu', 'BSc Computer Science'),
('STU005', 'Amirul Haziq bin Razali', 'stu123', 'amirul.haziq@student.edu', 'BSc Information Technology');

-- --------------------------------------------------------

--
-- Table structure for table `supervisor`
--

CREATE TABLE `supervisor` (
  `supervisor_id` varchar(20) NOT NULL,
  `supervisor_name` varchar(100) NOT NULL,
  `supervisor_email` varchar(100) DEFAULT NULL,
  `supervisor_password` varchar(100) NOT NULL,
  `company_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supervisor`
--

INSERT INTO `supervisor` (`supervisor_id`, `supervisor_name`, `supervisor_email`, `supervisor_password`, `company_id`) VALUES
('S001', 'Mr. Rajan Kumar', 'rajan.kumar@company.com', 'sup123', 1),
('S002', 'Ms. Lim Bee Ling', 'lim.beeling@company.com', 'sup123', 2),
('S003', 'En. Hafiz Rahmat', 'hafiz.rahmat@company.com', 'sup123', 3);

-- --------------------------------------------------------

--
-- Table structure for table `supervisor_marks`
--

CREATE TABLE `supervisor_marks` (
  `s_marks_id` int NOT NULL,
  `component_id` int NOT NULL,
  `component_mark` decimal(5,2) NOT NULL DEFAULT '0.00',
  `comments` text,
  `total_marks` decimal(6,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supervisor_marks`
--

INSERT INTO `supervisor_marks` (`s_marks_id`, `component_id`, `component_mark`, `comments`, `total_marks`) VALUES
(100001, 1, 85.00, 'Excellent task execution', 8.50),
(100001, 2, 90.00, 'Strictly followed safety protocols', 9.00),
(100001, 3, 78.00, 'Good application of theory', 7.80),
(100001, 4, 82.00, 'Well-structured report', 12.30),
(100001, 5, 75.00, 'Clear language used', 7.50),
(100001, 6, 88.00, 'Active in learning activities', 13.20),
(100001, 7, 80.00, 'Managed project timeline well', 12.00),
(100001, 8, 85.00, 'Always on time', 12.75),
(100002, 1, 55.00, 'Average performance', 5.50),
(100002, 2, 60.00, 'Met basic requirements', 6.00),
(100002, 3, 50.00, 'Needs improvement', 5.00),
(100002, 4, 58.00, 'Satisfactory report', 8.70),
(100002, 5, 52.00, 'Language acceptable', 5.20),
(100002, 6, 60.00, 'Some learning activity', 9.00),
(100002, 7, 55.00, 'Met deadlines mostly', 8.25),
(100002, 8, 58.00, 'Mostly punctual', 8.70),
(100003, 1, 35.00, 'Did not complete tasks', 3.50),
(100003, 2, 40.00, 'Safety ignored at times', 4.00),
(100003, 3, 30.00, 'Poor theory application', 3.00),
(100003, 4, 38.00, 'Report incomplete', 5.70),
(100003, 5, 32.00, 'Language unclear', 3.20),
(100003, 6, 35.00, 'No proactive learning', 5.25),
(100003, 7, 40.00, 'Missed several deadlines', 6.00),
(100003, 8, 35.00, 'Frequently absent', 5.25);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `component`
--
ALTER TABLE `component`
  ADD PRIMARY KEY (`component_id`);

--
-- Indexes for table `internship`
--
ALTER TABLE `internship`
  ADD PRIMARY KEY (`internship_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `lecturer_id` (`lecturer_id`),
  ADD KEY `supervisor_id` (`supervisor_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `lecturer`
--
ALTER TABLE `lecturer`
  ADD PRIMARY KEY (`lecturer_id`);

--
-- Indexes for table `lecturer_marks`
--
ALTER TABLE `lecturer_marks`
  ADD PRIMARY KEY (`l_marks_id`,`component_id`),
  ADD KEY `component_id` (`component_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `supervisor`
--
ALTER TABLE `supervisor`
  ADD PRIMARY KEY (`supervisor_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `supervisor_marks`
--
ALTER TABLE `supervisor_marks`
  ADD PRIMARY KEY (`s_marks_id`,`component_id`),
  ADD KEY `component_id` (`component_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `company_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `component`
--
ALTER TABLE `component`
  MODIFY `component_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `internship`
--
ALTER TABLE `internship`
  MODIFY `internship_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100006;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `internship`
--
ALTER TABLE `internship`
  ADD CONSTRAINT `internship_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `internship_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturer` (`lecturer_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `internship_ibfk_3` FOREIGN KEY (`supervisor_id`) REFERENCES `supervisor` (`supervisor_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `internship_ibfk_4` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE SET NULL;

--
-- Constraints for table `lecturer_marks`
--
ALTER TABLE `lecturer_marks`
  ADD CONSTRAINT `lecturer_marks_ibfk_1` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`);

--
-- Constraints for table `supervisor`
--
ALTER TABLE `supervisor`
  ADD CONSTRAINT `supervisor_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE SET NULL;

--
-- Constraints for table `supervisor_marks`
--
ALTER TABLE `supervisor_marks`
  ADD CONSTRAINT `supervisor_marks_ibfk_1` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
