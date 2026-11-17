-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 17, 2026 at 03:38 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projeklibrary`
--

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `BookID` int NOT NULL,
  `ISBN` varchar(20) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `CategoryID` int DEFAULT NULL,
  `Author` varchar(150) DEFAULT NULL,
  `Publisher` varchar(150) DEFAULT NULL,
  `Edition` varchar(50) DEFAULT NULL,
  `Status` enum('Available','Borrowed','Maintenance','Lost') NOT NULL DEFAULT 'Available',
  `Stock` int NOT NULL DEFAULT '1',
  `CoverImage` varchar(255) DEFAULT NULL,
  `Summary` text
) ;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`BookID`, `ISBN`, `Title`, `CategoryID`, `Author`, `Publisher`, `Edition`, `Status`, `Stock`, `CoverImage`, `Summary`) VALUES
(1, 'ISBN001', 'Clean Code', 1, 'Robert C. Martin', 'Prentice Hall', '1st', 'Available', 3, '1791897018_6ace2dba65ec1.webp', NULL),
(2, 'ISBN002', 'Design Patterns', 1, 'Erich Gamma et al.', 'Addison-Wesley', '1st', 'Available', 2, '1791897738_6ace308a3221b.jpg', NULL),
(3, 'ISBN003', 'Laskar Pelangi', 2, 'Andrea Hirata', 'Bentang Pustaka', '1st', 'Available', 3, '1791897676_6ace304c3d149.jpg', NULL),
(4, 'ISBN004', 'Bumi Manusia', 2, 'Pramoedya Ananta Toer', 'Lentera Dipantara', '1st', 'Available', 4, '1791897525_6ace2fb531734.png', NULL),
(5, 'ISBN005', 'Cosmos', 3, 'Carl Sagan', 'Random House', '1st', 'Available', 2, '1791897616_6ace3010472d6.png', NULL),
(6, 'ISBN006', 'A Brief History of Time', 3, 'Stephen Hawking', 'Bantam Books', '1st', 'Available', 3, '1791897234_6ace2e92c5497.jpg', NULL),
(7, 'ISBN007', 'Sapiens', 4, 'Yuval Noah Harari', 'Harper', '1st', 'Available', 3, '1791897161_6ace2e4906855.jpg', NULL),
(8, 'ISBN008', 'Nusantara', 4, 'Bernard H.M. Vlekke', 'Kepustakaan Populer Gramedia', '1st', 'Available', 2, '1791897105_6ace2e11f1c1d.jpg', NULL),
(9, 'ISBN009', 'Atomic Habits', 5, 'James Clear', 'Avery', '1st', 'Available', 4, '1791897044_6ace2dd4b024e.jpg', NULL),
(10, 'ISBN010', 'Deep Work', 5, 'Cal Newport', 'Grand Central Publishing', '1st', 'Available', 2, '1791897054_6ace2ddec9bdc.jpg', NULL),
(11, 'ISBN011', '7 DOSA DARELL', 4, 'Aha', 'ElationCO', '1st', 'Available', 3, '1791899212_6ace364c1a073.jpeg', 'Buku tentang sejarah kelam Darell'),
(12, 'ISBN012', 'Shuumatsu no Valkyrie / Record of Ragnarok', 2, 'Shinya Umemura', 'Ivrea', '1st', 'Available', 2, '1791899616_6ace37e06936a.jpg', 'Dalam Record of Ragnarok Volume 17, pertarungan Ronde ke-8 berlanjut sengit antara sang ilmuwan jenius Nikola Tesla dan dewa berdarah dingin Beelzebub. Menggunakan zirah canggih berbasis sains (Automaton Beta), Tesla mengandalkan manipulasi listrik dan teknik teleportasi untuk menembus pertahanan getaran mematikan milik Beelzebub. Di tengah adu kekuatan dan kilas balik masa lalu Beelzebub yang kelam, volume ini menyoroti benturan filosofis mendalam: optimisme kemajuan sains manusia melawan kegelapan serta keputusasaan yang melingkupi sang dewa.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`BookID`),
  ADD UNIQUE KEY `uk_book_isbn` (`ISBN`),
  ADD KEY `idx_book_category` (`CategoryID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `BookID` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `fk_book_category` FOREIGN KEY (`CategoryID`) REFERENCES `bookcategory` (`CategoryID`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
