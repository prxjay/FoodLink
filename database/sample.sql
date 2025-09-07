-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:4307
-- Generation Time: Sep 07, 2025 at 12:19 PM
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
-- Database: `sample`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `ClaimFood` (IN `p_food_id` INT, IN `p_claimer_email` VARCHAR(60))   BEGIN
    DECLARE v_claimer_id INT;
    DECLARE v_donor_email VARCHAR(60);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Get claimer ID
    SELECT id INTO v_claimer_id FROM login WHERE email = p_claimer_email;
    
    -- Check if user is trying to claim their own food
    SELECT email INTO v_donor_email FROM food_donations WHERE Fid = p_food_id;
    
    IF v_donor_email = p_claimer_email THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot claim your own food donation';
    END IF;
    
    -- Check if food is already claimed
    IF EXISTS (SELECT 1 FROM food_donations WHERE Fid = p_food_id AND assigned_to IS NOT NULL) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Food donation already claimed';
    END IF;
    
    -- Claim the food
    UPDATE food_donations 
    SET assigned_to = v_claimer_id, status = 'claimed' 
    WHERE Fid = p_food_id;
    
    COMMIT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `available_food`
-- (See below for the actual view)
--
CREATE TABLE `available_food` (
`Fid` int(11)
,`name` varchar(50)
,`email` varchar(60)
,`food` varchar(50)
,`type` enum('veg','non-veg')
,`category` enum('raw-food','cooked-food','packed-food')
,`quantity` varchar(50)
,`date` datetime
,`address` text
,`location` varchar(50)
,`phoneno` varchar(15)
,`assigned_to` int(11)
,`status` enum('available','claimed','delivered')
,`donor_name` varchar(50)
,`donor_phone` varchar(15)
);

-- --------------------------------------------------------

--
-- Table structure for table `food_donations`
--

CREATE TABLE `food_donations` (
  `Fid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(60) NOT NULL,
  `food` varchar(50) NOT NULL,
  `type` enum('veg','non-veg') NOT NULL,
  `category` enum('raw-food','cooked-food','packed-food') NOT NULL,
  `quantity` varchar(50) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `address` text NOT NULL,
  `location` varchar(50) NOT NULL,
  `phoneno` varchar(15) NOT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `status` enum('available','claimed','delivered') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(60) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` text NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_donations`
-- (See below for the actual view)
--
CREATE TABLE `user_donations` (
`Fid` int(11)
,`name` varchar(50)
,`email` varchar(60)
,`food` varchar(50)
,`type` enum('veg','non-veg')
,`category` enum('raw-food','cooked-food','packed-food')
,`quantity` varchar(50)
,`date` datetime
,`address` text
,`location` varchar(50)
,`phoneno` varchar(15)
,`assigned_to` int(11)
,`status` enum('available','claimed','delivered')
,`donation_status` varchar(9)
);

-- --------------------------------------------------------

--
-- Structure for view `available_food`
--
DROP TABLE IF EXISTS `available_food`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `available_food`  AS SELECT `f`.`Fid` AS `Fid`, `f`.`name` AS `name`, `f`.`email` AS `email`, `f`.`food` AS `food`, `f`.`type` AS `type`, `f`.`category` AS `category`, `f`.`quantity` AS `quantity`, `f`.`date` AS `date`, `f`.`address` AS `address`, `f`.`location` AS `location`, `f`.`phoneno` AS `phoneno`, `f`.`assigned_to` AS `assigned_to`, `f`.`status` AS `status`, `l`.`name` AS `donor_name`, `l`.`phone` AS `donor_phone` FROM (`food_donations` `f` join `login` `l` on(`f`.`email` = `l`.`email`)) WHERE `f`.`assigned_to` is null OR `f`.`assigned_to` = 0 ;

-- --------------------------------------------------------

--
-- Structure for view `user_donations`
--
DROP TABLE IF EXISTS `user_donations`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_donations`  AS SELECT `f`.`Fid` AS `Fid`, `f`.`name` AS `name`, `f`.`email` AS `email`, `f`.`food` AS `food`, `f`.`type` AS `type`, `f`.`category` AS `category`, `f`.`quantity` AS `quantity`, `f`.`date` AS `date`, `f`.`address` AS `address`, `f`.`location` AS `location`, `f`.`phoneno` AS `phoneno`, `f`.`assigned_to` AS `assigned_to`, `f`.`status` AS `status`, CASE WHEN `f`.`assigned_to` is not null AND `f`.`assigned_to` <> 0 THEN 'Claimed' ELSE 'Available' END AS `donation_status` FROM `food_donations` AS `f` ORDER BY `f`.`date` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `food_donations`
--
ALTER TABLE `food_donations`
  ADD PRIMARY KEY (`Fid`),
  ADD KEY `email` (`email`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `location` (`location`),
  ADD KEY `status` (`status`),
  ADD KEY `idx_food_donations_date` (`date`),
  ADD KEY `idx_food_donations_type` (`type`),
  ADD KEY `idx_food_donations_category` (`category`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `food_donations`
--
ALTER TABLE `food_donations`
  MODIFY `Fid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
