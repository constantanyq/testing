-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2022 at 03:44 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `petandingan`
--

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `idguru` varchar(3) NOT NULL,
  `namaguru` varchar(30) NOT NULL,
  `password` varchar(8) NOT NULL,
  `kategoriguru` varchar(10) NOT NULL,
  `sekolah` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`idguru`, `namaguru`, `password`, `kategoriguru`, `sekolah`) VALUES
('G01', 'Shim Shai', 'gdst', 'Rendah', 'SJK (C) KEPONG 3'),
('G02', 'Suraya', 'math', 'Menengah', 'SMK Taman Bukit Maluri'),
('G03', 'IU Li', 'blueming', 'Menengah', 'SMK Jalan Ipoh'),
('G04', 'Ipini', 'upin', 'Rendah', 'SK Selayang Baru'),
('G05', 'Doha', 'mature', 'Menengah', 'SMK Taman Bukit Maluri'),
('G06', 'Taehyung', 'head', 'Rendah', 'SJK(C)Kai Chee'),
('G07', 'Scratch Cat', 'code', 'Menengah', 'SMK Jalan Ipoh');

-- --------------------------------------------------------

--
-- Table structure for table `hakim`
--

CREATE TABLE `hakim` (
  `idhakim` varchar(3) NOT NULL,
  `password` varchar(8) NOT NULL,
  `namahakim` varchar(30) NOT NULL,
  `kategorihakim` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `hakim`
--

INSERT INTO `hakim` (`idhakim`, `password`, `namahakim`, `kategorihakim`) VALUES
('H01', 'abualia3', 'Joshua', 'Menengah'),
('H02', 'baixuesn', 'Snowei', 'Menengah'),
('H03', 'jieling', 'Jie Lin', 'Menengah'),
('H04', '30days', 'Gyubin', 'Menengah'),
('H05', 'abcd', 'qqwechat', 'Rendah'),
('H06', 'Adnan', 'leftenan', 'Rendah'),
('H07', 'Yin luo', 'fuheng', 'Rendah');

-- --------------------------------------------------------

--
-- Table structure for table `keputusan`
--

CREATE TABLE `keputusan` (
  `idpeserta` varchar(12) NOT NULL,
  `idpenilaian` varchar(3) NOT NULL,
  `markah` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `keputusan`
--

INSERT INTO `keputusan` (`idpeserta`, `idpenilaian`, `markah`, `jumlah`) VALUES
('P01', 'M01', 18, 72),
('P01', 'M02', 18, 72),
('P01', 'M03', 18, 72),
('P01', 'M04', 18, 72),
('P02', 'M01', 13, 73),
('P02', 'M02', 16, 73),
('P02', 'M03', 20, 73),
('P02', 'M04', 24, 73),
('P03', 'M01', 24, 91),
('P03', 'M02', 23, 91),
('P03', 'M03', 24, 91),
('P03', 'M04', 20, 91),
('P04', 'M01', 25, 96),
('P04', 'M02', 24, 96),
('P04', 'M03', 25, 96),
('P04', 'M04', 22, 96),
('P05', 'M01', 18, 74),
('P05', 'M02', 19, 74),
('P05', 'M03', 20, 74),
('P05', 'M04', 17, 74),
('P06', 'M01', 24, 93),
('P06', 'M02', 25, 93),
('P06', 'M03', 21, 93),
('P06', 'M04', 23, 93),
('P07', 'M01', 15, 67),
('P07', 'M02', 18, 67),
('P07', 'M03', 24, 67),
('P07', 'M04', 10, 67),
('P08', 'M01', 23, 91),
('P08', 'M02', 23, 91),
('P08', 'M03', 22, 91),
('P08', 'M04', 23, 91),
('P09', 'M01', 21, 81),
('P09', 'M02', 21, 81),
('P09', 'M03', 18, 81),
('P09', 'M04', 21, 81),
('P10', 'M01', 22, 88),
('P10', 'M02', 22, 88),
('P10', 'M03', 22, 88),
('P10', 'M04', 22, 88),
('P14', 'M01', 23, 83),
('P14', 'M02', 20, 83),
('P14', 'M03', 20, 83),
('P14', 'M04', 20, 83),
('P16', 'M01', 1, 65),
('P16', 'M02', 14, 65),
('P16', 'M03', 25, 65),
('P16', 'M04', 25, 65);

-- --------------------------------------------------------

--
-- Table structure for table `penilaian`
--

CREATE TABLE `penilaian` (
  `idpenilaian` varchar(3) NOT NULL,
  `aspek` varchar(30) NOT NULL,
  `markahpenuh` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `penilaian`
--

INSERT INTO `penilaian` (`idpenilaian`, `aspek`, `markahpenuh`) VALUES
('M01', 'Matematik', 25),
('M02', 'Sains', 25),
('M03', 'Fizik', 25),
('M04', 'Kimia', 25);

-- --------------------------------------------------------

--
-- Table structure for table `peserta`
--

CREATE TABLE `peserta` (
  `idpeserta` varchar(12) NOT NULL,
  `password` varchar(8) NOT NULL,
  `namapeserta` varchar(100) NOT NULL,
  `emel` varchar(100) NOT NULL,
  `sekolah` varchar(100) NOT NULL,
  `idhakim` varchar(3) NOT NULL,
  `idurusetia` varchar(3) NOT NULL,
  `idguru` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `peserta`
--

INSERT INTO `peserta` (`idpeserta`, `password`, `namapeserta`, `emel`, `sekolah`, `idhakim`, `idurusetia`, `idguru`) VALUES
('P', '1', '', '12', '23', 'H01', 'U01', 'G01'),
('P01', 'abu', 'Abu Bakar Mohemed Salleh', 'bakarsendiri@gmail.com', 'SMK Raja Ali', 'H03', 'U01', 'G01'),
('P02', 'blueming', 'IU', 'uaena@gmail.com', 'SMK St.Mary', 'H02', 'U03', 'G01'),
('P03', 'etyw', 'yeonwoo ha', 'yeonwoo@gmail.com', 'SMK Jalan Ipoh', 'H04', 'U05', 'G03'),
('P04', 'stella', 'euntae cha', 'euntaes@gmail.com', 'SMK St.John', 'H05', 'U05', 'G02'),
('P05', 'annabeh', 'Anna', 'annabelle@gmail.com', 'SMK Taman Bukit Maluri', 'H01', 'U01', 'G02'),
('P06', 'sarangha', 'Eunwoo Cha', 'eunwoistyq@gmail.com', 'SMK Sinar Bintang', 'H01', 'U01', 'G01'),
('P07', 'dontstea', 'Lia Wo', 'etisywnotlia@gmail.com', 'SMK St.Mary', 'H02', 'U04', 'G03'),
('P08', 'chunsung', 'Sooyoung', 'cafemeet@gmail.com', 'SMK St.Mary', 'H04', 'U05', 'G03'),
('P09', 'yong', 'carmen', 'yongcarmen@gmail.com', 'SJK (C) Kepong 3', 'H03', 'U02', 'G04'),
('P1', '54', '123', '123@gmail.com', 'SMK Taman Bukit Maluri', 'H01', 'U01', 'G01'),
('P10', 'dioran', 'Yap Xin Ran', 'xinranyp@gmail.com', 'SJK (C) Kai Chee', 'H03', 'U02', 'G04'),
('P11', 'sukiii', 'Suki', 'ratsuki@gmail.com', 'SMK Sinar Bintang', 'H03', 'U03', 'G03'),
('P12', 'aoi', 'Aoi Chan', 'naily@gmail.com', 'SMJK Chong Hwa', 'H04', 'U04', 'G02'),
('P13', 'muthu', 'Muthu', 'muthu@gmail.com', 'SMK Jlan Ipoh', 'H01', 'U01', 'G01'),
('P14', '1414', 'shi si', 'tentyfour@gmail.com', 'SMK DUA', 'H02', 'U01', 'G01'),
('P15', 'shuip', 'Kung Fu', 'kungfupanda@gmail.com', 'SMK CHINA KUNG FU', 'H04', 'U03', 'G03'),
('P16', '1234', '1', '1', '1', 'H01', 'U01', 'G01'),
('P18', 'abu', 'Ali', 'ali@gmail.com', 'SMK Raja Ali', 'H01', 'U01', 'G01'),
('P20', 'abubakar', 'Bakar Avu', 'avuvuevue@gmail.com', 'SMK ST.MARY', 'H02', 'U01', 'G03'),
('P21', 'nailyheh', 'Naily', 'nailychan@gmail.com', 'SMK ST.MARY', 'H02', 'U01', 'G03');

-- --------------------------------------------------------

--
-- Table structure for table `urusetia`
--

CREATE TABLE `urusetia` (
  `idurusetia` varchar(3) NOT NULL,
  `password` varchar(8) NOT NULL,
  `namaurusetia` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `urusetia`
--

INSERT INTO `urusetia` (`idurusetia`, `password`, `namaurusetia`) VALUES
('U01', 'ahkiqing', 'Ying Qi'),
('U02', 'ahshuan', 'Yin Xuen'),
('U03', '12345', 'Isaac'),
('U04', 'avuvu', 'Abu Bakar'),
('U05', 'happy', 'Jooyul');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`idguru`);

--
-- Indexes for table `hakim`
--
ALTER TABLE `hakim`
  ADD PRIMARY KEY (`idhakim`);

--
-- Indexes for table `keputusan`
--
ALTER TABLE `keputusan`
  ADD PRIMARY KEY (`idpeserta`,`idpenilaian`) USING BTREE,
  ADD KEY `idpenilaian` (`idpenilaian`),
  ADD KEY `idpeserta` (`idpeserta`) USING BTREE;

--
-- Indexes for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`idpenilaian`);

--
-- Indexes for table `peserta`
--
ALTER TABLE `peserta`
  ADD PRIMARY KEY (`idpeserta`),
  ADD KEY `idhakim` (`idhakim`),
  ADD KEY `idurusetia` (`idurusetia`),
  ADD KEY `idguru` (`idguru`) USING BTREE;

--
-- Indexes for table `urusetia`
--
ALTER TABLE `urusetia`
  ADD PRIMARY KEY (`idurusetia`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `keputusan`
--
ALTER TABLE `keputusan`
  ADD CONSTRAINT `keputusan_penilaian` FOREIGN KEY (`idpenilaian`) REFERENCES `penilaian` (`idpenilaian`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `keputusan_peserta` FOREIGN KEY (`idpeserta`) REFERENCES `peserta` (`idpeserta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `peserta`
--
ALTER TABLE `peserta`
  ADD CONSTRAINT `peserta_guru` FOREIGN KEY (`idguru`) REFERENCES `guru` (`idguru`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `peserta_hakim` FOREIGN KEY (`idhakim`) REFERENCES `hakim` (`idhakim`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `peserta_urusetia` FOREIGN KEY (`idurusetia`) REFERENCES `urusetia` (`idurusetia`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
