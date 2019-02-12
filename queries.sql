USE `429879-yeticave`;

-- Добавляем категории в БД 
INSERT INTO `categories` 
SET `name` = 'Доски и лыжи';

INSERT INTO `categories` 
SET `name` = 'Крепления';

INSERT INTO `categories` 
SET `name` = 'Ботинки';

INSERT INTO `categories` 
SET `name` = 'Одежда';

INSERT INTO `categories` 
SET `name` = 'Инструменты';

INSERT INTO `categories` 
SET `name` = 'Разное';

-- Добавляем пользователей в БД
INSERT INTO `users` 
(`email`, `name`, `password`, `contacts`) 
VALUES ('potato@mail.ru', 'lazyPotato', 'sdasasddsw1', 'не пишите мне'), ('coolguy@mail.ru', 'Papyrus', 'ckdib4', '+8732462379 звоните');

-- Добавляем объявления в БД 
INSERT INTO `lots`
(`title`, `description`, `img_url`, `start_price`, `date_expire`, `bet_step`, `category_id`, `user_id`)
VALUES ('2014 Rossignol District Snowboard', 'good', 'img/lot-1.jpg', 10999, '2019-03-20', 200, 1, 2),
('DC Ply Mens 2016/2017 Snowboard', 'good', 'img/lot-2.jpg', 159999, '2019-03-20', 200, 1, 2),
('Крепления Union Contact Pro 2015 года размер L/XL', 'good', 'img/lot-3.jpg', 8000, '2019-03-20', 200, 2, 2),
('Ботинки для сноуборда DC Mutiny Charocal', 'good', 'img/lot-4.jpg', 10999, '2019-03-20', 200, 3, 1),
('Куртка для сноуборда DC Mutiny Charocal', 'good', 'img/lot-5.jpg', 7500, '2019-03-20', 200, 4, 2),
('Маска Oakley Canopy', 'good', 'img/lot-6.jpg', 5400, '2019-03-20', 200, 6, 1);

-- Добавляем ставки в БД
INSERT INTO `bets`
(`price`, `user_id`,`lot_id`) 
VALUES (11000, 1, 4), (15000, 2, 3); 
-- 

-- Получаем все категории
SELECT * FROM `categories`;

-- Получаем новые открытые лоты
SELECT `l`.`title`, `l`.`start_price`, `l`.`img_url`, `b`.`price`, `c`.`name`  FROM `lots` `l`
JOIN `bets` `b`
ON `l`.`id` = `b`.`lot_id`
JOIN `categories` `c`
ON `l`.`category_id` = `c`.`id`	
ORDER BY `l`.`date_create` DESC;

-- Показываем лот по id
SELECT `l`.`id`, `c`.`name` FROM `lots` `l`
JOIN `categories` `c`
ON `c`.`id` = `l`.`category_id`
WHERE `l`.`id` = 3;

-- Обновляем название лота по идентификатору
UPDATE `lots` SET `title` = 'Новое название'
WHERE `id` = 3;

-- Получаем свежие ставки по id лота
SELECT `b`.`price` FROM `lots` `l`
JOIN `bets` `b`
ON `b`.`lot_id` = `l`.`id`
WHERE `l`.`id` = 3
ORDER BY `b`.`date_create` DESC;


 



