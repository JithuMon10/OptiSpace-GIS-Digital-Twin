-- Create Database
CREATE DATABASE IF NOT EXISTS `optispace_db`;
USE `optispace_db`;

-- Vehicle Types Table
CREATE TABLE IF NOT EXISTS `vehicle_types` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `size_rank` INT NOT NULL, -- 1: Small, 2: Medium, 3: Large, 4: XL
    `hourly_rate` DECIMAL(10, 2) NOT NULL,
    `co2_savings` DECIMAL(10, 2) NOT NULL -- kg per entry
);

-- Seed Vehicle Types
INSERT INTO `vehicle_types` (`name`, `size_rank`, `hourly_rate`, `co2_savings`) VALUES
('Bike', 1, 10.00, 0.50),
('Car', 2, 20.00, 1.20),
('SUV', 3, 50.00, 2.50),
('Bus', 4, 100.00, 5.00);

-- Parking Slots Table
CREATE TABLE IF NOT EXISTS `parking_slots` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `slot_name` VARCHAR(50) NOT NULL,
    `zone_name` VARCHAR(50) NOT NULL,
    `size_rank` INT NOT NULL, -- 1: S, 2: M, 3: L, 4: XL
    `status` ENUM('free', 'occupied', 'inefficient') DEFAULT 'free',
    `current_vehicle_type` INT DEFAULT NULL,
    `coordinates` TEXT NOT NULL, -- JSON formatted polygon coordinates
    FOREIGN KEY (`current_vehicle_type`) REFERENCES `vehicle_types`(`id`)
);

-- Seed Parking Slots (Centered around Kochi 10.055, 76.355)
INSERT INTO `parking_slots` (`slot_name`, `zone_name`, `size_rank`, `coordinates`) VALUES
('Slot S1', 'Commuter A', 1, '[[10.0551, 76.3551], [10.0551, 76.3552], [10.0552, 76.3552], [10.0552, 76.3551]]'),
('Slot S2', 'Commuter A', 1, '[[10.0551, 76.3553], [10.0551, 76.3554], [10.0552, 76.3554], [10.0552, 76.3553]]'),
('Slot M1', 'General B', 2, '[[10.0553, 76.3551], [10.0553, 76.3552], [10.0554, 76.3552], [10.0554, 76.3551]]'),
('Slot M2', 'General B', 2, '[[10.0553, 76.3553], [10.0553, 76.3554], [10.0554, 76.3554], [10.0554, 76.3553]]'),
('Slot XL1', 'Logistics Hub D', 4, '[[10.0555, 76.3551], [10.0555, 76.3554], [10.0557, 76.3554], [10.0557, 76.3551]]');

-- Global Stats Table
CREATE TABLE IF NOT EXISTS `simulation_stats` (
    `id` INT PRIMARY KEY DEFAULT 1,
    `total_revenue` DECIMAL(15, 2) DEFAULT 0.00,
    `total_co2` DECIMAL(15, 2) DEFAULT 0.00
);

INSERT INTO `simulation_stats` (`id`, `total_revenue`, `total_co2`) VALUES (1, 0.00, 0.00)
ON DUPLICATE KEY UPDATE `id`=`id`;
