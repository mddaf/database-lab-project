-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2024 at 08:04 PM
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
-- Database: `automob`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `Username` varchar(50) DEFAULT NULL,
  `ProductID` int(11) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `AddedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`Username`, `ProductID`, `Quantity`, `AddedAt`) VALUES
('', 20, 1, '2024-09-15 05:30:46'),
('', 16, 1, '2024-09-15 05:31:38');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `Name` varchar(100) NOT NULL,
  `Username` varchar(30) NOT NULL,
  `Email` varchar(80) NOT NULL,
  `Phone` varchar(15) NOT NULL,
  `Address` varchar(150) NOT NULL,
  `Gender` enum('Male','Female','Others') NOT NULL,
  `Password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`Name`, `Username`, `Email`, `Phone`, `Address`, `Gender`, `Password`) VALUES
('Fayed', 'daf', 'alfayed348@gmail.com', '123', 'badda', 'Male', '456789'),
('emon', 'sar', 'ds@gmail.com', '123', 'badda', 'Male', '456789'),
('sasuke', 'saringan', 'crow@gmail.com', '41489', 'leaf', 'Male', '123456');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `OrderDate` date NOT NULL,
  `ShippingAddress` varchar(255) NOT NULL,
  `PaymentType` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrderID`, `Username`, `OrderDate`, `ShippingAddress`, `PaymentType`) VALUES
(34, 'sar', '2024-09-14', 'd', 'Cash On Delivery'),
(35, 'sar', '2024-09-14', 'h', 'Mobile Banking'),
(36, 'daf', '2024-09-15', 'h', 'Cash On Delivery'),
(37, 'daf', '2024-09-15', 'tfy', 'Mobile Banking'),
(38, 'daf', '2024-09-15', 'Badda', 'Mobile Banking'),
(39, 'daf', '2024-09-15', 'Middle Badda', 'Cash On Delivery'),
(40, 'daf', '2024-09-15', 'yhrt', 'Mobile Banking'),
(41, 'daf', '2024-09-15', 'retger', 'Cash On Delivery'),
(42, 'daf', '2024-09-16', 'fgb', 'Cash On Delivery');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `DetailsID` int(11) NOT NULL,
  `OrderID` int(11) DEFAULT NULL,
  `Quantity` int(11) NOT NULL,
  `ProductID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`DetailsID`, `OrderID`, `Quantity`, `ProductID`) VALUES
(66, 34, 4, 18),
(67, 35, 3, 20),
(68, 36, 112, 18),
(69, 37, 2, 18),
(70, 38, 1, 16),
(71, 38, 10003, 18),
(72, 39, 1078, 16),
(73, 40, 1, 18),
(74, 41, 10002, 18),
(75, 42, 1, 20);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `PaymentID` int(11) NOT NULL,
  `OrderID` int(11) DEFAULT NULL,
  `AccountNumber` varchar(255) DEFAULT NULL,
  `TransactionID` varchar(255) DEFAULT NULL,
  `BankingOption` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`PaymentID`, `OrderID`, `AccountNumber`, `TransactionID`, `BankingOption`) VALUES
(22, 34, '', '', 'Bkash'),
(23, 35, '3', 'fgh', 'Bkash'),
(24, 36, '', '', 'Bkash'),
(25, 37, 'gf', 'g', 'Bkash'),
(26, 38, '46514651465', 'FTfg54fgvYu', 'Bkash'),
(27, 39, '', '', 'Bkash'),
(28, 40, '658458', 'rgrt', 'Nagad'),
(29, 41, '', '', 'Bkash'),
(30, 42, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `ProductID` int(100) NOT NULL,
  `Seller` varchar(100) NOT NULL,
  `ProductName` varchar(100) NOT NULL,
  `Category` varchar(100) NOT NULL,
  `ProductDetails` varchar(200) NOT NULL,
  `Price` varchar(100) NOT NULL,
  `Availability` varchar(10) NOT NULL DEFAULT 'YES',
  `ProductImage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`ProductID`, `Seller`, `ProductName`, `Category`, `ProductDetails`, `Price`, `Availability`, `ProductImage`) VALUES
(16, 'daf', 'Iphone 16', 'Mobile', 'ef', '89', 'NO', 'uploads/66e611a179318.jpeg'),
(18, 'daf', 'Tire', 'Bike Parts', 'Michelin Palsur Tire', '10', 'YES', 'uploads/66e5f86057797.jpeg'),
(20, 'sar', 'Iphone 16', 'Mobile', 'vhyu tfgyuyugyub ', '1000', 'YES', 'uploads/66dd4f8830afe.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `seller`
--

CREATE TABLE `seller` (
  `Name` varchar(150) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Email` varchar(80) NOT NULL,
  `Phone` varchar(15) NOT NULL,
  `Address` varchar(150) NOT NULL,
  `Gender` enum('Male','Female','Others') NOT NULL,
  `Password` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller`
--

INSERT INTO `seller` (`Name`, `Username`, `Email`, `Phone`, `Address`, `Gender`, `Password`) VALUES
('Fayed', 'daf', 'alfayed348@gmail.com', '123', 'badda', 'Male', '456789'),
('emon', 'sar', 'ds@gmail.com', '123', 'badda', 'Male', '456789'),
('naruto', 'shadow', 'clone@gmail.com', '41489', 'leaf', 'Male', '123456');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD KEY `Username` (`Username`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`Username`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`DetailsID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `OrderID` (`OrderID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`ProductID`);

--
-- Indexes for table `seller`
--
ALTER TABLE `seller`
  ADD PRIMARY KEY (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `DetailsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `ProductID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
