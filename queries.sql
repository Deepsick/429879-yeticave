USE `429879-yeticave`;

-- Добавляем категории в БД 
INSERT INTO `categories` 
(`name`, `class_name`) 
VALUES 
('Доски и лыжи', 'boards'), 
('Крепления', 'attachment'), 
('Ботинки', 'boots'),
('Одежда', 'clothing'), 
('Инструменты', 'tools'),
('Разное', 'other');

-- Добавляем пользователей в БД
INSERT INTO `users` 
(`email`, `name`, `password`, `contacts`) 
VALUES 
('potato@mail.ru', 'lazyPotato', 'sdasasddsw1', 'не пишите мне'),
('nariman@mail.ru', 'nariman', 'asdascxcs', 'Отвечаю на почту с 11 до 20'),
('boom@mail.ru', 'DoctorBoom', 'llvclfdfl2', 'не пишите мне'),
('nagibator@mail.ru', 'Nagibator', 'cveieh133', 'Потом'),
('wizard@mail.ru', 'Wizard', '1233fda', 'пишите мне'),
('lead@mail.ru', 'leader', 'sdasafffdd1', 'не пишите мне'),
('artem@mail.ru', 'Artem', 'ckdib4', '+8732462379 звоните');

-- Добавляем объявления в БД 
INSERT INTO `lots`
(`title`, `description`, `img_url`, `start_price`, `date_expire`, `bet_step`, `category_id`, `user_id`)
VALUES 
('2014 Rossignol District Snowboard', 'good', 'img/lot-1.jpg', 10999, '2017-03-20', 200, 1, 2),
('DC Ply Mens 2016/2017 Snowboard', 'good', 'img/lot-2.jpg', 159999, '2019-03-20', 200, 1, 2),
('Крепления Union Contact Pro 2015 года размер L/XL', 'good', 'img/lot-3.jpg', 8000, '2019-03-20', 200, 2, 2),
('Ботинки для сноуборда DC Mutiny Charocal', 'good', 'img/lot-4.jpg', 10999, '2019-03-20', 200, 3, 1),
('Куртка для сноуборда DC Mutiny Charocal', 'good', 'img/lot-5.jpg', 7500, '2017-03-20', 200, 4, 2),
('Маска Oakley Canopy', 'good', 'img/lot-6.jpg', 5400, '2019-03-20', 200, 6, 1);

-- Добавляем ставки в БД
INSERT INTO `bets`
(`price`, `user_id`,`lot_id`) 
VALUES 
(11000, 3, 4), 
(15000, 1, 3), 
(12000, 4, 3), 
(13000, 2, 2), 
(14000, 6, 5),
(14000, 6, 1),
(15000, 3, 2),
(15000, 3, 1),
(15000, 3, 3);

CREATE INDEX lot_title ON `lots`(`title`);
CREATE INDEX start_price ON `lots`(`start_price`);
CREATE INDEX lot_creation ON `lots`(`date_create`);
CREATE INDEX new_lot ON `lots`(`date_expire`, `winner_id`);

CREATE INDEX bet_creation ON `bets`(`date_create`);

-- Получаем все категории
SELECT * FROM `categories`;

-- Получаем новые открытые лоты
SELECT `l`.`title`, `l`.`start_price`, `l`.`img_url`, MAX(`b`.`price`) AS `max_price`, `c`.`name` AS `category` FROM `lots` `l`
LEFT JOIN `bets` `b`
ON `l`.`id` = `b`.`lot_id`
JOIN `categories` `c`
ON `l`.`category_id` = `c`.`id`
WHERE `l`.`date_expire` > NOW() AND `l`.`winner_id` IS NULL
GROUP BY `l`.`id`	
ORDER BY `l`.`date_create` DESC;

-- Показываем лот по id
SELECT `l`.`id`, `l`.`title`, `l`.`start_price`, `l`.`img_url`, `c`.`name`  FROM `lots` `l`
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
WHERE `l`.`id` = 1
ORDER BY `b`.`date_create` DESC;

-- Полнотекстовый поиск лотов
SELECT 
			`l`.`id`, 
			`l`.`title`, 
			`l`.`img_url`, 
			`l`.`date_expire`,
            `l`.`description`,
			`l`.`start_price`, 
			MAX(`b`.`price`) AS `max_price`,
			`c`.`name` 
		FROM 
			`lots` `l`
		LEFT JOIN 
			`bets` `b`
		ON 
			`l`.`id` = `b`.`lot_id`  
		JOIN 
			`categories` `c`
		ON 
			`c`.`id` = `l`.`category_id` 
		WHERE 
			MATCH(`title`, `description`) AGAINST('csadsfdsaads')
             GROUP BY
             `l`.`id`
		ORDER BY 
			`l`.`date_create` 
		DESC
        LIMIT 2
        OFFSET 0;


-- Показываем категорию по id
SELECT 
			`c`.`name`, 
			`c`.`id` 
		FROM 
			`categories` `c`
		WHERE 
			`c`.`id` = 3;

-- Показываем свежие лоты по id категории
SELECT 
			`l`.`title`,
			`l`.`id`,
			`l`.`date_create`,
			`l`.`date_expire`,
			`l`.`img_url`,
			`l`.`start_price`,
			MAX(`b`.`price`) AS `max_price`,
			`c`.`name` AS `category`
		FROM 
			`lots` `l`
		LEFT JOIN 
			`bets` `b`
		ON 
			`l`.`id` = `b`.`lot_id`  
		JOIN 
			`categories` `c`
		ON 
			`c`.`id` = `l`.`category_id`
		WHERE 
			`l`.`category_id` = 2
		AND 
			`l`.`date_expire` > NOW()
		GROUP BY
		 	`l`.`id` 	
		ORDER BY 
			`l`.`date_create` DESC;
            
-- Получаем количество ставок по id лота
SELECT COUNT(*) FROM `bets` `b` WHERE `b`.`lot_id` = 16;

-- Получаем все ставки пользователя
SELECT 
	`b`.`id`, 
    `b`.`price`, 
    `b`.`date_create`, 
    `l`.`title` AS `lot_title`,
    `l`.`img_url` AS `lot_img_url`,
    `l`.`id` AS `lot_id`,
    `l`.`winner_id`,
    `l`.`date_expire` AS `lot_expire`,
    `c`.`name` AS `category`,
    `u`.`contacts`,
    `u`.`name` 
FROM
	`bets` `b`
JOIN 
	`lots` `l`
ON
	`l`.`id` = `b`.`lot_id`
JOIN
	`categories` `c`
ON
	`l`.`category_id` = `c`.`id`
JOIN 
	`users` `u`
ON
	`u`.`id` = `b`.`user_id`
WHERE 
	`b`.`user_id` = 8;
    
SELECT 
			`l`.`id`, 
			`l`.`title`, 
			`l`.`img_url`, 
			`l`.`date_expire`,
			`l`.`description`,
			`l`.`start_price`, 
			MAX(`b`.`price`) AS `max_price`,
			`c`.`name` 
		FROM 
			`lots` `l`
		LEFT JOIN 
			`bets` `b`
		ON 
			`l`.`id` = `b`.`lot_id`  
		JOIN 
			`categories` `c`
		ON 
			`c`.`id` = `l`.`category_id` 
		WHERE 
			MATCH(`title`, `description`) AGAINST('sdsds')
		GROUP BY
			`l`.`id`
		ORDER BY 
			`l`.`date_create` 
		DESC
		LIMIT 3 OFFSET 3;




SELECT
	`u`.`name` AS `user_name`,
    `b`.`price`
FROM
	`bets` `b`
JOIN
	`users` `u`
ON
	`u`.`id` = `b`.`user_id`
JOIN
	`lots` `l`
ON
	`l`.`id` = `b`.`lot_id`
WHERE
	`b`.`price` = 14000
AND
	`l`.`id` = 5;
    
    UPDATE `lots`
    SET `winner_id` = null
    WHERE `id` > 0;
    
  

    
