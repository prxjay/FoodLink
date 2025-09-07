-- Clean Database Structure for Food Link System
-- Drop existing database and create fresh one

DROP DATABASE IF EXISTS `sample`;
CREATE DATABASE `sample` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sample`;

-- --------------------------------------------------------

-- Table structure for table `login` (Users)
CREATE TABLE `login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(60) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` text NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `food_donations`
CREATE TABLE `food_donations` (
  `Fid` int(11) NOT NULL AUTO_INCREMENT,
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
  `delivery_by` int(11) DEFAULT NULL,
  `status` enum('available','claimed','delivered') NOT NULL DEFAULT 'available',
  PRIMARY KEY (`Fid`),
  KEY `email` (`email`),
  KEY `assigned_to` (`assigned_to`),
  KEY `location` (`location`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `user_feedback`
CREATE TABLE `user_feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(60) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`feedback_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `password_resets`
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Sample data for testing (optional - remove if you want completely clean database)

-- Sample users
INSERT INTO `login` (`name`, `email`, `phone`, `password`, `gender`) VALUES
('Test User', 'test@example.com', '9876543210', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'male'),
('Demo User', 'demo@example.com', '9876543211', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'female');

-- Sample food donations
INSERT INTO `food_donations` (`name`, `email`, `food`, `type`, `category`, `quantity`, `address`, `location`, `phoneno`) VALUES
('Test User', 'test@example.com', 'Rice and Curry', 'veg', 'cooked-food', '5 plates', '123 Main Street', 'chennai', '9876543210'),
('Demo User', 'demo@example.com', 'Bread and Jam', 'veg', 'packed-food', '10 packets', '456 Park Avenue', 'madurai', '9876543211');

-- --------------------------------------------------------

-- Add foreign key constraints (optional - for better data integrity)
-- ALTER TABLE `food_donations`
--   ADD CONSTRAINT `fk_food_donations_email` FOREIGN KEY (`email`) REFERENCES `login` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
--   ADD CONSTRAINT `fk_food_donations_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `login` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- --------------------------------------------------------

-- Indexes for better performance
CREATE INDEX `idx_food_donations_date` ON `food_donations` (`date`);
CREATE INDEX `idx_food_donations_type` ON `food_donations` (`type`);
CREATE INDEX `idx_food_donations_category` ON `food_donations` (`category`);

-- --------------------------------------------------------

-- Views for easier querying (optional)
CREATE VIEW `available_food` AS
SELECT 
    f.*,
    l.name as donor_name,
    l.phone as donor_phone
FROM `food_donations` f
JOIN `login` l ON f.email = l.email
WHERE f.status = 'available' AND f.assigned_to IS NULL;

CREATE VIEW `user_donations` AS
SELECT 
    f.*,
    CASE 
        WHEN f.assigned_to IS NOT NULL THEN 'Claimed'
        ELSE 'Available'
    END as donation_status
FROM `food_donations` f
ORDER BY f.date DESC;

-- --------------------------------------------------------

-- Stored procedures for common operations (optional)
DELIMITER //

CREATE PROCEDURE `ClaimFood`(
    IN p_food_id INT,
    IN p_claimer_email VARCHAR(60)
)
BEGIN
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
END //

DELIMITER ;

-- --------------------------------------------------------

-- Grant permissions (adjust as needed for your setup)
-- GRANT ALL PRIVILEGES ON `sample`.* TO 'root'@'localhost';
-- FLUSH PRIVILEGES;

-- --------------------------------------------------------

-- Database setup complete!
-- You can now import this file into phpMyAdmin
