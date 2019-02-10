CREATE DATABASE yeticave
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

USE yeticave;
 
CREATE TABLE categories (
id INT AUTO_INCREMENT PRIMARY KEY,
name CHAR(64)
);

CREATE TABLE lots (
id INT AUTO_INCREMENT PRIMARY KEY,
title CHAR(64),
description CHAR(128),
img_url CHAR,
start_price INT,
creation DATETIME,
expiration DATETIME,
bet_step INT,
category_id INT 
);

CREATE TABLE bets (
id INT AUTO_INCREMENT PRIMARY KEY,
creation DATETIME,
price INT,
lot_id INT
);

CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
registration DATETIME,
email CHAR(64),
name CHAR(64),
password CHAR(64),
avatar_url CHAR,
contacts CHAR(128),
bet_id INT,
lot_id INT
);

CREATE UNIQUE INDEX category_name ON categories(name);
CREATE UNIQUE INDEX user_email ON users(email);
CREATE UNIQUE INDEX user_password ON users(password);
CREATE UNIQUE INDEX user_contacts ON users(contacts);

CREATE INDEX lot_title ON lots(title);
CREATE INDEX lot_price ON lots(start_price);
CREATE INDEX user_name ON users(name);
CREATE INDEX bet_price ON bets(price);
 