-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2025 at 11:49 PM
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
-- Database: `meardemandandsupply`
--

-- --------------------------------------------------------

--
-- Table structure for table `coldstorageproduct`
--

CREATE TABLE `coldstorageproduct` (
  `id` int(11) NOT NULL,
  `storageid` int(11) NOT NULL,
  `wholesalerid` int(11) NOT NULL,
  `productType` varchar(50) NOT NULL,
  `entryDate` datetime NOT NULL,
  `expiryDate` date NOT NULL,
  `quantity` float NOT NULL,
  `InTransitQty` float NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coldstorageproduct`
--

INSERT INTO `coldstorageproduct` (`id`, `storageid`, `wholesalerid`, `productType`, `entryDate`, `expiryDate`, `quantity`, `InTransitQty`, `status`) VALUES
(1, 2, 1, 'Sheep', '2025-04-29 02:35:13', '2025-06-04', 40, 0, 'In Storage'),
(12, 1, 1, 'Beef', '2025-04-20 10:30:00', '2025-05-10', 800, 50, 'active'),
(15, 2, 2, 'Beef', '2025-04-26 08:45:00', '2025-05-15', 300, 20, 'active'),
(19, 2, 5, 'Goat', '2025-04-27 16:45:00', '2025-05-18', 600, 40, 'active'),
(20, 3, 5, 'Beef', '2025-04-22 13:30:00', '2025-05-08', 150, 5, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `cold_storage`
--

CREATE TABLE `cold_storage` (
  `StorageID` int(11) NOT NULL,
  `Capacity` float DEFAULT NULL,
  `Area` varchar(100) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `contact` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cold_storage`
--

INSERT INTO `cold_storage` (`StorageID`, `Capacity`, `Area`, `City`, `contact`) VALUES
(1, 5000.5, 'North Industrial Zone', 'Dhaka', ''),
(2, 7500, 'Agricultural Park', 'Chattogram', ''),
(3, 3000.25, 'Rural Storage Unit A', 'Rajshahi', ''),
(4, 10000, 'Mega Cold Hub', 'Khulna', ''),
(5, 4500.75, 'Central Depot', 'Sylhet', '');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `CustomerID` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Contact` varchar(20) DEFAULT NULL,
  `Area` varchar(100) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`CustomerID`, `Name`, `Contact`, `Area`, `City`) VALUES
(1, 'Aminul Haque', '01711112222', 'Dhanmondi', 'Dhaka'),
(2, 'Nusrat Jahan', '01855553333', 'Panchlaish', 'Chattogram'),
(3, 'Sakib Rahman', '01999994444', 'Kazla', 'Rajshahi'),
(5, 'Tanvir Hasan', '01577778888', 'Zindabazar', 'Sylhet');

-- --------------------------------------------------------

--
-- Table structure for table `customer_order`
--

CREATE TABLE `customer_order` (
  `OrderID` int(11) NOT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `RetailerID` int(11) DEFAULT NULL,
  `ProductType` varchar(50) NOT NULL,
  `OrderQuantity` int(11) DEFAULT NULL,
  `PricePerUnit` float DEFAULT NULL,
  `OrderDate` date DEFAULT NULL,
  `DeliveryDate` date DEFAULT NULL,
  `OrderStatus` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_order`
--

INSERT INTO `customer_order` (`OrderID`, `CustomerID`, `RetailerID`, `ProductType`, `OrderQuantity`, `PricePerUnit`, `OrderDate`, `DeliveryDate`, `OrderStatus`) VALUES
(2, 2, 2, 'Beef', 5, 620, '2025-04-03', '2025-04-07', 'Delivered'),
(3, 3, 3, 'Goat', 8, 760, '2025-04-06', '2025-04-10', 'Delivered'),
(5, 5, 5, 'Beef', 12, 600, '2025-04-10', '2025-04-14', 'Delivered'),
(6, 1, 2, 'Goat', 3, 900, '2025-04-24', '1970-01-01', 'Delivered'),
(8, 1, 2, 'Goat', 2, 900, '2025-04-24', '1970-01-01', 'Delivered'),
(9, 1, 2, 'Goat', 2, 900, '2025-04-24', '1970-01-01', 'Delivered'),
(10, 1, 2, 'Sheep', 5, 800, '2025-04-24', '1970-01-01', 'Delivered'),
(11, 1, 2, 'Beef', 2, 800, '2025-04-24', '2025-04-24', 'Delivered'),
(12, 2, 2, 'Beef', 3, 800, '2025-04-24', '2025-04-24', 'Delivered'),
(17, 1, 2, 'Sheep', 3, 850, '2025-04-24', '2025-04-24', 'Delivered'),
(22, 1, 2, 'Beef', 4, 850, '2025-04-26', '2025-04-28', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `farm`
--

CREATE TABLE `farm` (
  `FarmID` int(11) NOT NULL,
  `FarmName` varchar(100) DEFAULT NULL,
  `Area` varchar(100) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `ContactNumber` varchar(20) DEFAULT NULL,
  `FarmSize` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farm`
--

INSERT INTO `farm` (`FarmID`, `FarmName`, `Area`, `City`, `ContactNumber`, `FarmSize`) VALUES
(1, 'Green Valley Farm', 'Savar', 'Dhaka', '01710001111', 12.5),
(2, 'Sunny Agro Farm', 'Rupsha', 'Khulna', '01820002222', 18),
(3, 'Golden Fields', 'Shibganj', 'Bogura', '01930003333', 22.7),
(4, 'Fresh Harvest', 'Kanchpur', 'Narayanganj', '01640004444', 10.3);

-- --------------------------------------------------------

--
-- Table structure for table `farmer`
--

CREATE TABLE `farmer` (
  `FarmerID` int(11) NOT NULL,
  `FarmerName` varchar(100) DEFAULT NULL,
  `Area` varchar(100) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `FarmID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmer`
--

INSERT INTO `farmer` (`FarmerID`, `FarmerName`, `Area`, `City`, `FarmID`) VALUES
(1, 'Abdul Karim', 'Savar', 'Dhaka', 1),
(2, 'Rafiq Hossain', 'Rupsha', 'Khulna', 2),
(3, 'Jalal Uddin', 'Shibganj', 'Bogura', 3),
(4, 'Kamal Ahmed', 'Kanchpur', 'Narayanganj', 4),
(5, 'Shahin Mia', 'Paba', 'Rajshahi', 5);

-- --------------------------------------------------------

--
-- Table structure for table `government_officer`
--

CREATE TABLE `government_officer` (
  `OfficerID` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Role` varchar(50) DEFAULT NULL,
  `WorkingArea` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `government_officer`
--

INSERT INTO `government_officer` (`OfficerID`, `Name`, `Role`, `WorkingArea`) VALUES
(1, 'Anisur Rahman', 'Livestock Inspector', 'Tangail'),
(2, 'Salma Akter', 'Veterinary Officer', 'Barisal'),
(3, 'Md. Rashed Khan', 'Food Safety Officer', 'Comilla'),
(4, 'Nusrat Jahan', 'Field Supervisor', 'Gazipur'),
(5, 'Shamim Alam', 'Health & Safety Officer', 'Chittagong');

-- --------------------------------------------------------

--
-- Table structure for table `livestock`
--

CREATE TABLE `livestock` (
  `LivestockID` int(11) NOT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Color` varchar(50) DEFAULT NULL,
  `Weight` float DEFAULT NULL,
  `Birthdate` date DEFAULT NULL,
  `Age` int(11) DEFAULT NULL,
  `FarmID` int(11) NOT NULL,
  `VaccinationStatus` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `livestock`
--

INSERT INTO `livestock` (`LivestockID`, `Type`, `Color`, `Weight`, `Birthdate`, `Age`, `FarmID`, `VaccinationStatus`) VALUES
(3, 'Buffalo', 'Black', 550, '2021-12-05', 4, 3, ''),
(4, 'Sheep', 'Gray', 40.3, '2023-01-20', 2, 4, ''),
(5, 'Cow', 'Black', 470, '2022-06-25', 3, 5, ''),
(7, 'Cattle', 'Black', 60, '2023-12-31', 2, 2, 'Vaccinated'),
(8, 'Sheep', 'White', 60, '2024-06-17', 1, 2, 'Not Vaccinated'),
(9, 'Sheep', 'Brown', 40, '2022-06-06', 3, 3, 'Vaccinated'),
(10, 'Sheep', 'White', 50, '2024-01-01', 1, 1, 'Vaccinated'),
(11, 'Cow', 'Black', 100, '2024-01-15', 1, 3, 'Vaccinated'),
(12, 'Cow', 'Black', 100, '2024-01-15', 1, 3, 'Vaccinated');

-- --------------------------------------------------------

--
-- Table structure for table `meat_product`
--

CREATE TABLE `meat_product` (
  `ProductID` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  `origin` varchar(50) NOT NULL,
  `seasonality` varchar(50) NOT NULL,
  `cut` varchar(50) NOT NULL,
  `Quantity` float DEFAULT NULL,
  `Price` float DEFAULT NULL,
  `BatchID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meat_product`
--

INSERT INTO `meat_product` (`ProductID`, `type`, `origin`, `seasonality`, `cut`, `Quantity`, `Price`, `BatchID`) VALUES
(1, 'Beef', 'Tangail', 'Year-round', 'Ribeye', 150.5, 260, 1),
(2, 'Chicken', 'Barisal', 'Year-round', 'Chicken Wings', 200, 300, 2),
(3, 'Chicken', 'Chattogram', 'Year-round', 'Breast', 180.25, 350, 3),
(4, 'Goat', 'Rangpur', 'Winter', 'Goat Chops', 225, 300, 4),
(5, 'Beef', 'Rajshahi', 'Year-round', 'Sirloin', 140.75, 400, 5);

-- --------------------------------------------------------

--
-- Table structure for table `processing`
--

CREATE TABLE `processing` (
  `FarmID` int(11) NOT NULL,
  `LivestockID` int(11) NOT NULL,
  `SlaughterDate` date DEFAULT NULL,
  `SlaughterHouseID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `processing`
--

INSERT INTO `processing` (`FarmID`, `LivestockID`, `SlaughterDate`, `SlaughterHouseID`, `ProductID`) VALUES
(3, 3, '2025-04-12', 3, 3),
(4, 4, '2025-04-13', 4, 4),
(5, 5, '2025-04-14', 5, 5);

-- --------------------------------------------------------

--
-- Table structure for table `production_batch`
--

CREATE TABLE `production_batch` (
  `BatchID` int(11) NOT NULL,
  `ProductionDate` date DEFAULT NULL,
  `ExpiryDate` date DEFAULT NULL,
  `BatchQuantity` float DEFAULT NULL,
  `ProductionCost` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_batch`
--

INSERT INTO `production_batch` (`BatchID`, `ProductionDate`, `ExpiryDate`, `BatchQuantity`, `ProductionCost`) VALUES
(1, '2025-03-10', '2025-04-10', 500, 7500),
(2, '2025-03-15', '2025-04-15', 600, 8200),
(3, '2025-03-20', '2025-04-20', 450, 6800),
(4, '2025-03-25', '2025-04-25', 700, 9100),
(5, '2025-03-30', '2025-04-30', 550, 7900);

-- --------------------------------------------------------

--
-- Table structure for table `recommendation`
--

CREATE TABLE `recommendation` (
  `RecommendationID` int(11) NOT NULL,
  `SuggestedAction` text DEFAULT NULL,
  `Reasoning` text DEFAULT NULL,
  `RecommendationDate` date DEFAULT NULL,
  `FarmID` int(11) DEFAULT NULL,
  `OfficerID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recommendation`
--

INSERT INTO `recommendation` (`RecommendationID`, `SuggestedAction`, `Reasoning`, `RecommendationDate`, `FarmID`, `OfficerID`) VALUES
(1, 'Increase grazing area for livestock', 'The current grazing area is insufficient for optimal livestock health.', '2025-04-20', 1, 1),
(2, 'Implement a new feeding schedule', 'Current feeding times are inconsistent and can affect livestock growth.', '2025-04-21', 2, 2),
(3, 'Install automatic water dispensers', 'This will ensure a constant supply of water to livestock, improving hydration.', '2025-04-22', 3, 3),
(4, 'Improve livestock shelter conditions', 'The shelter is currently inadequate for extreme weather conditions.', '2025-04-23', 4, 4),
(5, 'Monitor health status of livestock more frequently', 'Frequent health checks will help in early detection of diseases.', '2025-04-24', 5, 5);

-- --------------------------------------------------------

--
-- Table structure for table `retailer`
--

CREATE TABLE `retailer` (
  `RetailerID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `area` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `MinimumDeliveryDays` int(2) NOT NULL,
  `contact` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `retailer`
--

INSERT INTO `retailer` (`RetailerID`, `name`, `area`, `city`, `MinimumDeliveryDays`, `contact`) VALUES
(2, 'Grameen Fresh', 'Agrabad', 'Chattogram', 2, '01937379707'),
(3, 'Pabna Mart', 'New Market', 'Rajshahi', 4, '01937379707'),
(4, 'Sundarban Retailers', 'Khulshi', 'Chattogram', 1, '01937379777'),
(5, 'Kheya Agro Shop', 'Shibbari Mor', 'Khulna', 3, '01937379707'),
(6, 'YZ Limited', 'Dhanmondi', 'Dhaka', 4, '01937379777'),
(7, 'Jobbar Store', 'Jatrabari', 'Dhaka', 2, '01717171717');

-- --------------------------------------------------------

--
-- Table structure for table `retailer_product`
--

CREATE TABLE `retailer_product` (
  `retailerID` int(11) NOT NULL,
  `ProductType` varchar(50) NOT NULL,
  `PricePerUnit` int(5) NOT NULL,
  `AvailableQuantity` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `retailer_product`
--

INSERT INTO `retailer_product` (`retailerID`, `ProductType`, `PricePerUnit`, `AvailableQuantity`) VALUES
(1, 'Beef', 600, 56),
(1, 'Goat', 900, 5),
(1, 'Sheep', 720, 50),
(2, 'Beef', 850, 17),
(2, 'Goat', 950, 35),
(2, 'Sheep', 850, 22),
(3, 'Beef', 800, 80),
(3, 'Goat', 950, 70),
(3, 'Sheep', 700, 25),
(4, 'Beef', 780, 10),
(4, 'Goat', 910, 35),
(4, 'Sheep', 730, 45),
(5, 'Beef', 860, 50),
(5, 'Goat', 920, 60),
(5, 'Sheep', 750, 15);

-- --------------------------------------------------------

--
-- Table structure for table `retailer_wholesaler_order`
--

CREATE TABLE `retailer_wholesaler_order` (
  `OrderID` int(11) NOT NULL,
  `ProductType` varchar(50) NOT NULL,
  `OrderQuantity` int(11) DEFAULT NULL,
  `PricePerUnit` float DEFAULT NULL,
  `TotalPrice` int(10) NOT NULL,
  `OrderDate` date DEFAULT NULL,
  `DeliveryDate` date DEFAULT NULL,
  `OrderStatus` varchar(50) DEFAULT NULL,
  `WholesalerID` int(11) DEFAULT NULL,
  `RetailerID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `retailer_wholesaler_order`
--

INSERT INTO `retailer_wholesaler_order` (`OrderID`, `ProductType`, `OrderQuantity`, `PricePerUnit`, `TotalPrice`, `OrderDate`, `DeliveryDate`, `OrderStatus`, `WholesalerID`, `RetailerID`) VALUES
(1, 'Beef', 20, 500, 10000, '2025-04-01', '2025-04-05', 'Delivered', 1, 1),
(3, 'Beef', 50, 450, 22500, '2025-04-06', '2025-04-10', 'Delivered', 3, 3),
(5, 'Beef', 30, 500, 15000, '2025-04-10', '2025-04-14', 'Shipped', 5, 5),
(9, 'Beef', 30, 500, 15000, '2025-04-24', '2025-04-28', 'Delivered', 1, 1),
(11, 'Sheep', 30, 600, 18000, '2025-04-24', '2025-04-28', 'Delivered', 1, 1),
(12, 'Sheep', 30, 600, 18000, '2025-04-24', '2025-04-28', 'Delivered', 1, 1),
(13, 'Beef', 20, 500, 10000, '2025-04-24', '2025-04-28', 'Delivered', 1, 1),
(14, 'Beef', 25, 500, 12500, '2025-04-24', '2025-04-28', 'Delivered', 1, 1),
(15, 'Sheep', 25, 600, 15000, '2025-04-24', '2025-04-28', 'Delivered', 1, 1),
(16, 'Beef', 30, 500, 15000, '2025-04-24', '2025-04-28', 'Delivered', 1, 1),
(19, 'Beef', 20, 500, 10000, '2025-04-24', '2025-04-28', 'Delivered', 1, 1),
(25, 'Beef', 20, 500, 10000, '2025-04-24', '2025-04-28', 'Delivered', 1, 1),
(26, 'Beef', 20, 500, 10000, '2025-04-24', '2025-04-28', 'Delivered', 1, 1),
(27, 'Beef', 25, 500, 12500, '2025-04-26', '2025-04-30', 'Delivered', 1, 3),
(28, 'Goat', 30, 700, 21000, '2025-04-26', '2025-04-30', 'Delivered', 2, 3),
(29, 'Goat', 25, 710, 17750, '2025-04-26', '2025-04-30', 'Delivered', 4, 2);

-- --------------------------------------------------------

--
-- Table structure for table `slaughtering_house`
--

CREATE TABLE `slaughtering_house` (
  `SlaughterHouseID` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Area` varchar(100) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slaughtering_house`
--

INSERT INTO `slaughtering_house` (`SlaughterHouseID`, `Name`, `Area`, `City`) VALUES
(1, 'Green Valley Slaughterhouse', 'North Zone', 'Dhaka'),
(2, 'Fresh Meat Facility', 'West Industrial Area', 'Chattogram'),
(3, 'Citywide Meat Processing', 'Central Business District', 'Rajshahi'),
(4, 'Prime Slaughterhouse', 'East Agricultural Area', 'Khulna'),
(5, 'Standard Meat Facility', 'South Agricultural Zone', 'Sylhet');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `userType` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `userType`, `password`) VALUES
(1, 'Admin', '12345678'),
(1, 'Cold Storage Manager', '12345678'),
(1, 'Customer', '12345678'),
(1, 'Farm Manager', '12345678'),
(1, 'Farmer', '12345678'),
(1, 'Government Officer', '12345678'),
(1, 'Retailer', '12345678'),
(1, 'Wholesaler', '12345678'),
(2, 'Cold Storage Manager', '12345678'),
(2, 'Customer', '12345678'),
(2, 'Farm Manager', '12345678'),
(2, 'Farmer', '12345678'),
(2, 'Government Officer', '12345678'),
(2, 'Retailer', '12345678'),
(2, 'Wholesaler', '12345678'),
(3, 'Cold Storage Manager', '12345678'),
(3, 'Customer', '12345678'),
(3, 'Farm Manager', '12345678'),
(3, 'Farmer', '12345678'),
(3, 'Government Officer', '12345678'),
(3, 'Retailer', '12345678'),
(3, 'Wholesaler', '12345678'),
(4, 'Cold Storage Manager', '12345678'),
(4, 'Customer', '12345678'),
(4, 'Farm Manager', '12345678'),
(4, 'Farmer', '12345678'),
(4, 'Government Officer', '12345678'),
(4, 'Retailer', '12345678'),
(4, 'Wholesaler', '12345678'),
(5, 'Cold Storage Manager', '12345678'),
(5, 'Customer', '12345678'),
(5, 'Farm Manager', '12345678'),
(5, 'Farmer', '12345678'),
(5, 'Government Officer', '12345678'),
(5, 'Retailer', '12345678'),
(5, 'Wholesaler', '12345678');

-- --------------------------------------------------------

--
-- Table structure for table `wholesaler`
--

CREATE TABLE `wholesaler` (
  `WholesalerID` int(11) NOT NULL,
  `BulkDiscountRate` float DEFAULT NULL,
  `DistributionArea` varchar(100) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `area` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `contact` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wholesaler`
--

INSERT INTO `wholesaler` (`WholesalerID`, `BulkDiscountRate`, `DistributionArea`, `name`, `area`, `city`, `contact`) VALUES
(1, 5, 'North Zone', 'Bangla Wholesale Mart', 'Karwan Bazar', 'Dhaka', '01937379777'),
(3, 10, 'Central Zone', 'Rajshahi Meat Supply', 'Shahed Bazar', 'Rajshahi', '01937373707'),
(4, 3.5, 'East Zone', 'Khulna Bulk Market', 'Rupsha Stand Road', 'Khulna', '01937373707'),
(5, 6, 'South Zone', 'Sylhet Mega Wholesale', 'Zindabazar', 'Sylhet', '01937373707');

-- --------------------------------------------------------

--
-- Table structure for table `wholesaler_order`
--

CREATE TABLE `wholesaler_order` (
  `OrderID` int(11) NOT NULL,
  `OrderQuantity` int(11) DEFAULT NULL,
  `PricePerUnit` float DEFAULT NULL,
  `OrderDate` date DEFAULT NULL,
  `DeliveryDate` date DEFAULT NULL,
  `OrderStatus` varchar(50) DEFAULT NULL,
  `BatchID` int(11) DEFAULT NULL,
  `WholesalerID` int(11) DEFAULT NULL,
  `StorageID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wholesaler_order`
--

INSERT INTO `wholesaler_order` (`OrderID`, `OrderQuantity`, `PricePerUnit`, `OrderDate`, `DeliveryDate`, `OrderStatus`, `BatchID`, `WholesalerID`, `StorageID`) VALUES
(1, 500, 400, '2025-04-01', '2025-04-10', 'Delivered', 1, 1, 1),
(2, 1000, 400, '2025-04-05', '2025-04-12', 'Shipped', 2, 2, 2),
(3, 150, 500, '2025-04-07', '2025-04-15', 'Pending', 3, 3, 3),
(4, 250, 350, '2025-04-10', '2025-04-18', 'Delivered', 4, 4, 4),
(5, 750, 320, '2025-04-12', '2025-04-20', 'Shipped', 5, 5, 5);

-- --------------------------------------------------------

--
-- Table structure for table `wholesaler_product`
--

CREATE TABLE `wholesaler_product` (
  `wholesalerID` int(11) NOT NULL,
  `ProductType` varchar(50) NOT NULL,
  `AvailableQuantity` int(7) NOT NULL,
  `MinimumOrderQty` varchar(4) NOT NULL,
  `PricePerUnit` decimal(4,0) NOT NULL,
  `EntryDate` datetime DEFAULT NULL,
  `ExpiryDate` date DEFAULT NULL,
  `InTransitQty` int(7) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wholesaler_product`
--

INSERT INTO `wholesaler_product` (`wholesalerID`, `ProductType`, `AvailableQuantity`, `MinimumOrderQty`, `PricePerUnit`, `EntryDate`, `ExpiryDate`, `InTransitQty`) VALUES
(1, 'Beef', 35, '20', 500, NULL, NULL, 0),
(1, 'Goat', 60, '15', 600, NULL, NULL, 0),
(1, 'Sheep', 60, '20', 600, NULL, NULL, 0),
(2, 'Beef', 120, '25', 550, NULL, NULL, 0),
(2, 'Goat', 120, '30', 700, NULL, NULL, 0),
(2, 'Sheep', 80, '25', 650, NULL, NULL, 0),
(3, 'Beef', 20, '15', 520, NULL, NULL, 0),
(3, 'Goat', 80, '20', 750, NULL, NULL, 0),
(3, 'Sheep', 90, '20', 640, NULL, NULL, 0),
(4, 'Beef', 100, '20', 530, NULL, NULL, 0),
(4, 'Goat', 95, '25', 710, NULL, NULL, 0),
(4, 'Sheep', 110, '25', 630, NULL, NULL, 0),
(5, 'Beef', 110, '20', 540, NULL, NULL, 0),
(5, 'Goat', 130, '30', 730, NULL, NULL, 0),
(5, 'Sheep', 75, '20', 660, NULL, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coldstorageproduct`
--
ALTER TABLE `coldstorageproduct`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk1` (`storageid`),
  ADD KEY `fk2` (`wholesalerid`);

--
-- Indexes for table `cold_storage`
--
ALTER TABLE `cold_storage`
  ADD PRIMARY KEY (`StorageID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`CustomerID`);

--
-- Indexes for table `customer_order`
--
ALTER TABLE `customer_order`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `RetailerID` (`RetailerID`);

--
-- Indexes for table `farm`
--
ALTER TABLE `farm`
  ADD PRIMARY KEY (`FarmID`);

--
-- Indexes for table `farmer`
--
ALTER TABLE `farmer`
  ADD PRIMARY KEY (`FarmerID`),
  ADD KEY `FarmID` (`FarmID`);

--
-- Indexes for table `government_officer`
--
ALTER TABLE `government_officer`
  ADD PRIMARY KEY (`OfficerID`);

--
-- Indexes for table `livestock`
--
ALTER TABLE `livestock`
  ADD PRIMARY KEY (`LivestockID`,`FarmID`),
  ADD KEY `FarmID` (`FarmID`);

--
-- Indexes for table `meat_product`
--
ALTER TABLE `meat_product`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `BatchID` (`BatchID`);

--
-- Indexes for table `processing`
--
ALTER TABLE `processing`
  ADD PRIMARY KEY (`FarmID`,`LivestockID`,`SlaughterHouseID`,`ProductID`),
  ADD KEY `LivestockID` (`LivestockID`),
  ADD KEY `SlaughterHouseID` (`SlaughterHouseID`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Indexes for table `production_batch`
--
ALTER TABLE `production_batch`
  ADD PRIMARY KEY (`BatchID`);

--
-- Indexes for table `recommendation`
--
ALTER TABLE `recommendation`
  ADD PRIMARY KEY (`RecommendationID`),
  ADD KEY `FarmID` (`FarmID`),
  ADD KEY `OfficerID` (`OfficerID`);

--
-- Indexes for table `retailer`
--
ALTER TABLE `retailer`
  ADD PRIMARY KEY (`RetailerID`);

--
-- Indexes for table `retailer_product`
--
ALTER TABLE `retailer_product`
  ADD PRIMARY KEY (`retailerID`,`ProductType`,`PricePerUnit`,`AvailableQuantity`);

--
-- Indexes for table `retailer_wholesaler_order`
--
ALTER TABLE `retailer_wholesaler_order`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `RetailerID` (`RetailerID`),
  ADD KEY `WholesalerID` (`WholesalerID`);

--
-- Indexes for table `slaughtering_house`
--
ALTER TABLE `slaughtering_house`
  ADD PRIMARY KEY (`SlaughterHouseID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`,`userType`);

--
-- Indexes for table `wholesaler`
--
ALTER TABLE `wholesaler`
  ADD PRIMARY KEY (`WholesalerID`);

--
-- Indexes for table `wholesaler_order`
--
ALTER TABLE `wholesaler_order`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `BatchID` (`BatchID`),
  ADD KEY `WholesalerID` (`WholesalerID`),
  ADD KEY `StorageID` (`StorageID`);

--
-- Indexes for table `wholesaler_product`
--
ALTER TABLE `wholesaler_product`
  ADD PRIMARY KEY (`wholesalerID`,`ProductType`,`MinimumOrderQty`,`PricePerUnit`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `coldstorageproduct`
--
ALTER TABLE `coldstorageproduct`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `customer_order`
--
ALTER TABLE `customer_order`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `livestock`
--
ALTER TABLE `livestock`
  MODIFY `LivestockID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `recommendation`
--
ALTER TABLE `recommendation`
  MODIFY `RecommendationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `retailer`
--
ALTER TABLE `retailer`
  MODIFY `RetailerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `retailer_wholesaler_order`
--
ALTER TABLE `retailer_wholesaler_order`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `wholesaler`
--
ALTER TABLE `wholesaler`
  MODIFY `WholesalerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `coldstorageproduct`
--
ALTER TABLE `coldstorageproduct`
  ADD CONSTRAINT `fk1` FOREIGN KEY (`storageid`) REFERENCES `cold_storage` (`StorageID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk2` FOREIGN KEY (`wholesalerid`) REFERENCES `wholesaler_product` (`wholesalerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customer_order`
--
ALTER TABLE `customer_order`
  ADD CONSTRAINT `customer_order_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`CustomerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_order_ibfk_2` FOREIGN KEY (`RetailerID`) REFERENCES `retailer` (`RetailerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `farm`
--
ALTER TABLE `farm`
  ADD CONSTRAINT `farmfk01` FOREIGN KEY (`FarmID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
