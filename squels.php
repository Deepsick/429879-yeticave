<?php
$get_lots = "SELECT `l`.`title`, `l`.`start_price`, `l`.`img_url`, MAX(`b`.`price`) AS `max_price`, `c`.`name` AS `category` FROM `lots` `l`
LEFT JOIN `bets` `b`
ON `l`.`id` = `b`.`lot_id`
JOIN `categories` `c`
ON `l`.`category_id` = `c`.`id`
WHERE `l`.`date_expire` > NOW() AND `l`.`winner_id` IS NULL
GROUP BY `l`.`id`	
ORDER BY `l`.`date_create` DESC;";

$get_categories = "SELECT * FROM `categories`;";
