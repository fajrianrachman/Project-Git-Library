-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 17, 2026 at 05:41 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `bookcategory`
--

CREATE TABLE `bookcategory` (
  `CategoryID` int NOT NULL,
  `CategoryName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookcategory`
--

INSERT INTO `bookcategory` (`CategoryID`, `CategoryName`) VALUES
(2, 'Fiction'),
(4, 'History'),
(3, 'Science'),
(5, 'Self-Improvement'),
(1, 'Technology');

-- --------------------------------------------------------

--
-- Table structure for table `borrow`
--

CREATE TABLE `borrow` (
  `BorrowID` int NOT NULL,
  `MemberID` int NOT NULL,
  `LibrarianID` int DEFAULT NULL,
  `IssueDate` date NOT NULL,
  `DueDate` date NOT NULL,
  `Status` enum('Pending','Borrowed','Returned','Overdue') NOT NULL DEFAULT 'Pending'
) ;

--
-- Dumping data for table `borrow`
--

INSERT INTO `borrow` (`BorrowID`, `MemberID`, `LibrarianID`, `IssueDate`, `DueDate`, `Status`) VALUES
(1, 1, 1, '2026-09-01', '2026-09-08', 'Returned'),
(2, 2, 2, '2026-09-10', '2026-09-17', 'Returned'),
(3, 3, 1, '2026-10-01', '2026-10-08', 'Returned'),
(4, 4, 1, '2026-10-02', '2026-10-09', 'Returned'),
(5, 5, 1, '2026-09-20', '2026-09-27', 'Returned'),
(6, 6, 1, '2026-09-25', '2026-10-02', 'Returned'),
(15, 11, 1, '2026-10-13', '2026-10-20', 'Returned'),
(16, 11, 1, '2026-10-13', '2026-10-20', 'Returned'),
(17, 11, 1, '2026-10-13', '2026-10-20', 'Returned'),
(18, 11, 1, '2026-10-13', '2026-10-20', 'Returned'),
(19, 11, 1, '2026-10-13', '2026-10-20', 'Returned'),
(20, 11, 1, '2026-10-13', '2026-10-20', 'Returned'),
(21, 11, 1, '2026-10-13', '2026-10-20', 'Returned'),
(25, 11, 1, '2026-10-13', '2026-10-20', 'Returned'),
(26, 11, 1, '2026-10-13', '2026-10-20', 'Returned'),
(34, 11, 1, '2026-10-13', '2026-10-20', 'Returned'),
(35, 11, 1, '2026-10-13', '2026-10-20', 'Returned'),
(36, 1, 1, '2026-10-15', '2026-10-22', 'Returned'),
(37, 1, 1, '2026-10-15', '2026-10-22', 'Returned'),
(38, 1, 1, '2026-10-15', '2026-10-22', 'Returned'),
(39, 1, 1, '2026-10-15', '2026-10-22', 'Returned'),
(40, 1, 1, '2026-10-15', '2026-10-22', 'Borrowed');

--
-- Triggers `borrow`
--
DELIMITER $$
CREATE TRIGGER `trg_after_borrow_insert` AFTER INSERT ON `borrow` FOR EACH ROW BEGIN
    IF NEW.Status = 'Borrowed' THEN
        UPDATE member 
        SET CurrentBorrowedCount = CurrentBorrowedCount + 1 
        WHERE MemberID = NEW.MemberID;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_after_borrow_update` AFTER UPDATE ON `borrow` FOR EACH ROW BEGIN
    -- Jika Pustakawan meng-ACC (Pending -> Borrowed), tambah angka pinjaman
    IF OLD.Status = 'Pending' AND NEW.Status = 'Borrowed' THEN
        UPDATE member 
        SET CurrentBorrowedCount = CurrentBorrowedCount + 1 
        WHERE MemberID = NEW.MemberID;
        
    -- Jika Buku dikembalikan (Borrowed -> Returned), kurangi angka pinjaman
    ELSEIF OLD.Status = 'Borrowed' AND NEW.Status = 'Returned' THEN
        UPDATE member 
        SET CurrentBorrowedCount = GREATEST(0, CurrentBorrowedCount - 1) 
        WHERE MemberID = NEW.MemberID;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `borrowdetail`
--

CREATE TABLE `borrowdetail` (
  `DetailID` int NOT NULL,
  `BorrowID` int NOT NULL,
  `BookID` int NOT NULL,
  `Quantity` int NOT NULL DEFAULT '1',
  `ReturnDate` date DEFAULT NULL
) ;

--
-- Dumping data for table `borrowdetail`
--

INSERT INTO `borrowdetail` (`DetailID`, `BorrowID`, `BookID`, `Quantity`, `ReturnDate`) VALUES
(1, 1, 1, 1, '2026-09-07'),
(2, 2, 3, 1, '2026-09-20'),
(3, 3, 2, 1, '2026-10-13'),
(4, 4, 9, 1, '2026-10-13'),
(5, 5, 5, 1, '2026-10-13'),
(6, 6, 7, 1, '2026-10-13'),
(15, 15, 11, 1, '2026-10-13'),
(16, 16, 11, 1, '2026-10-13'),
(17, 17, 10, 1, '2026-10-13'),
(18, 18, 10, 1, '2026-10-13'),
(19, 19, 9, 1, '2026-10-13'),
(20, 20, 11, 1, '2026-10-13'),
(21, 21, 11, 1, '2026-10-13'),
(25, 25, 11, 1, '2026-10-13'),
(26, 26, 11, 1, '2026-10-13'),
(34, 34, 9, 1, '2026-10-13'),
(35, 35, 1, 1, '2026-10-13'),
(36, 36, 12, 1, '2026-10-15'),
(37, 37, 11, 1, '2026-10-15'),
(38, 38, 1, 1, '2026-10-15'),
(39, 39, 12, 1, '2026-10-15'),
(40, 40, 12, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fine`
--

CREATE TABLE `fine` (
  `FineID` int NOT NULL,
  `BorrowID` int NOT NULL,
  `Amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Status` enum('Unpaid','Paid','Cancelled') NOT NULL DEFAULT 'Unpaid'
) ;

--
-- Dumping data for table `fine`
--

INSERT INTO `fine` (`FineID`, `BorrowID`, `Amount`, `Status`) VALUES
(1, 2, 15000.00, 'Paid'),
(2, 5, 20000.00, 'Unpaid'),
(3, 6, 10000.00, 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `finepayment`
--

CREATE TABLE `finepayment` (
  `PaymentID` int NOT NULL,
  `FineID` int NOT NULL,
  `PaymentMethodID` int NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `finepayment`
--

INSERT INTO `finepayment` (`PaymentID`, `FineID`, `PaymentMethodID`, `Amount`, `PaymentDate`) VALUES
(1, 1, 4, 15000.00, '2026-09-20 14:30:00'),
(2, 3, 5, 10000.00, '2026-10-05 10:15:00');

--
-- Triggers `finepayment`
--
DELIMITER $$
CREATE TRIGGER `trg_after_finepayment_insert` AFTER INSERT ON `finepayment` FOR EACH ROW BEGIN
    DECLARE total_paid DECIMAL(10,2);
    DECLARE fine_amount DECIMAL(10,2);
    
    SELECT IFNULL(SUM(Amount), 0) INTO total_paid FROM finepayment WHERE FineID = NEW.FineID;
    SELECT Amount INTO fine_amount FROM fine WHERE FineID = NEW.FineID;
    
    IF total_paid >= fine_amount THEN
        UPDATE fine SET Status = 'Paid' WHERE FineID = NEW.FineID;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `librarian`
--

CREATE TABLE `librarian` (
  `LibrarianID` int NOT NULL,
  `EmployeeID` varchar(50) NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `Status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `librarian`
--

INSERT INTO `librarian` (`LibrarianID`, `EmployeeID`, `Name`, `Email`, `Phone`, `Password`, `Status`) VALUES
(1, 'LIB001', 'Budi Santoso', 'budi.santoso@library.com', '081234567801', '123456', 'Active'),
(2, 'LIB002', 'Siti Aminah', 'siti.aminah@library.com', '081234567802', '123456', 'Active'),
(3, 'LIB003', 'Ahmad Fauzi', 'ahmad.fauzi@library.com', '081234567803', '123456', 'Active'),
(4, 'LIB004', 'Dewi Lestari', 'dewi.lestari@library.com', '081234567804', '123456', 'Active'),
(5, 'LIB005', 'Eko Prasetyo', 'eko.prasetyo@library.com', '081234567805', '123456', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `MemberID` int NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Address` text,
  `Status` enum('Active','Inactive','Suspended') NOT NULL DEFAULT 'Active',
  `CurrentBorrowedCount` int NOT NULL DEFAULT '0',
  `Password` varchar(255) NOT NULL
) ;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`MemberID`, `Name`, `Email`, `Phone`, `Address`, `Status`, `CurrentBorrowedCount`, `Password`) VALUES
(1, 'Andi Pratama', 'andi.pratama@gmail.com', '082111110001', 'Jl. Merdeka No. 10, Jakarta', 'Active', 1, '123456'),
(2, 'Siti Rahma', 'siti.rahma@gmail.com', '082111110002', 'Jl. Sudirman No. 15, Bandung', 'Active', 0, '123456'),
(3, 'Rizky Febian', 'rizky.febian@gmail.com', '082111110003', 'Jl. Gatot Subroto No. 20, Surabaya', 'Active', 0, '123456'),
(4, 'Nengsih', 'nengsih@gmail.com', '082111110004', 'Jl. Asia Afrika No. 5, Bandung', 'Active', 0, '123456'),
(5, 'Dedi Mulyadi', 'dedi.mulyadi@gmail.com', '082111110005', 'Jl. Diponegoro No. 8, Semarang', 'Active', 0, '123456'),
(6, 'Putri Titian', 'putri.titian@gmail.com', '082111110006', 'Jl. Cipto No. 12, Cirebon', 'Active', 0, '123456'),
(7, 'Reza Rahadian', 'reza.rahadian@gmail.com', '082111110007', 'Jl. Pahlawan No. 22, Malang', 'Active', 0, '123456'),
(8, 'Citra Scholastika', 'citra.scholastika@gmail.com', '082111110008', 'Jl. Ahmad Yani No. 30, Yogyakarta', 'Active', 0, '123456'),
(9, 'Fajar Nugraha', 'fajar.nugraha@gmail.com', '082111110009', 'Jl. Kenanga No. 7, Bogor', 'Active', 0, '123456'),
(10, 'Lestari', 'lestari@gmail.com', '082111110010', 'Jl. Melati No. 9, Depok', 'Active', 0, '123456'),
(11, 'DarellAha', 'testemail@aha.com', '08676767', 'alamat menuju kebenaran', 'Active', 1, '123456');

-- --------------------------------------------------------

--
-- Table structure for table `paymentmethod`
--

CREATE TABLE `paymentmethod` (
  `PaymentMethodID` int NOT NULL,
  `MethodName` varchar(100) NOT NULL,
  `Provider` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `paymentmethod`
--

INSERT INTO `paymentmethod` (`PaymentMethodID`, `MethodName`, `Provider`) VALUES
(1, 'Cash', 'Manual Cashier'),
(2, 'Bank Transfer', 'BCA'),
(3, 'Bank Transfer', 'Mandiri'),
(4, 'E-Wallet', 'GoPay'),
(5, 'QRIS', 'All Payment Gateway');

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
-- Indexes for table `bookcategory`
--
ALTER TABLE `bookcategory`
  ADD PRIMARY KEY (`CategoryID`),
  ADD UNIQUE KEY `uk_category_name` (`CategoryName`);

--
-- Indexes for table `borrow`
--
ALTER TABLE `borrow`
  ADD PRIMARY KEY (`BorrowID`),
  ADD KEY `idx_borrow_member` (`MemberID`),
  ADD KEY `idx_borrow_librarian` (`LibrarianID`);

--
-- Indexes for table `borrowdetail`
--
ALTER TABLE `borrowdetail`
  ADD PRIMARY KEY (`DetailID`),
  ADD KEY `idx_detail_borrow` (`BorrowID`),
  ADD KEY `idx_detail_book` (`BookID`);

--
-- Indexes for table `fine`
--
ALTER TABLE `fine`
  ADD PRIMARY KEY (`FineID`),
  ADD KEY `idx_fine_borrow` (`BorrowID`);

--
-- Indexes for table `finepayment`
--
ALTER TABLE `finepayment`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `idx_payment_fine` (`FineID`),
  ADD KEY `idx_payment_method` (`PaymentMethodID`);

--
-- Indexes for table `librarian`
--
ALTER TABLE `librarian`
  ADD PRIMARY KEY (`LibrarianID`),
  ADD UNIQUE KEY `uk_librarian_employee` (`EmployeeID`),
  ADD UNIQUE KEY `uk_librarian_email` (`Email`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`MemberID`),
  ADD UNIQUE KEY `uk_member_email` (`Email`);

--
-- Indexes for table `paymentmethod`
--
ALTER TABLE `paymentmethod`
  ADD PRIMARY KEY (`PaymentMethodID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `BookID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookcategory`
--
ALTER TABLE `bookcategory`
  MODIFY `CategoryID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `borrow`
--
ALTER TABLE `borrow`
  MODIFY `BorrowID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borrowdetail`
--
ALTER TABLE `borrowdetail`
  MODIFY `DetailID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fine`
--
ALTER TABLE `fine`
  MODIFY `FineID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `finepayment`
--
ALTER TABLE `finepayment`
  MODIFY `PaymentID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `librarian`
--
ALTER TABLE `librarian`
  MODIFY `LibrarianID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `MemberID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paymentmethod`
--
ALTER TABLE `paymentmethod`
  MODIFY `PaymentMethodID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `fk_book_category` FOREIGN KEY (`CategoryID`) REFERENCES `bookcategory` (`CategoryID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `borrow`
--
ALTER TABLE `borrow`
  ADD CONSTRAINT `fk_borrow_librarian` FOREIGN KEY (`LibrarianID`) REFERENCES `librarian` (`LibrarianID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_borrow_member` FOREIGN KEY (`MemberID`) REFERENCES `member` (`MemberID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `borrowdetail`
--
ALTER TABLE `borrowdetail`
  ADD CONSTRAINT `fk_detail_book` FOREIGN KEY (`BookID`) REFERENCES `book` (`BookID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_borrow` FOREIGN KEY (`BorrowID`) REFERENCES `borrow` (`BorrowID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fine`
--
ALTER TABLE `fine`
  ADD CONSTRAINT `fk_fine_borrow` FOREIGN KEY (`BorrowID`) REFERENCES `borrow` (`BorrowID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `finepayment`
--
ALTER TABLE `finepayment`
  ADD CONSTRAINT `fk_payment_fine` FOREIGN KEY (`FineID`) REFERENCES `fine` (`FineID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_method` FOREIGN KEY (`PaymentMethodID`) REFERENCES `paymentmethod` (`PaymentMethodID`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
