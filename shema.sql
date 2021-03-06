CREATE DATABASE `429879-yeticave`
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

USE `429879-yeticave`;
 
CREATE TABLE `categories` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`name` VARCHAR(64) NOT NULL UNIQUE,
`class_name` VARCHAR(64) NOT NULL UNIQUE
);

CREATE TABLE `lots` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`title` VARCHAR(128) NOT NULL,
`description` VARCHAR(255) NOT NULL,
`img_url` VARCHAR(255) NOT NULL, 
`start_price` INT NOT NULL,
`date_create` DATETIME DEFAULT NOW(),
`date_expire` DATETIME NOT NULL,
`bet_step` INT NOT NULL,
`category_id` INT NOT NULL,
`user_id` INT NOT NULL,
`winner_id` INT DEFAULT NULL
);

CREATE TABLE `bets` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`date_create` DATETIME DEFAULT NOW(),
`price` INT NOT NULL,
`user_id` INT NOT NULL,
`lot_id` INT NOT NULL 
);

CREATE TABLE `users` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`date_register` DATETIME DEFAULT NOW(),
`email` VARCHAR(64) NOT NULL UNIQUE,
`name` VARCHAR(64) NOT NULL,
`password` VARCHAR(64) NOT NULL,
`avatar_url` VARCHAR(255) DEFAULT NULL,
`contacts` VARCHAR(255) NOT NULL
);

CREATE FULLTEXT INDEX lot_ft_search
ON lots(title, description);

 