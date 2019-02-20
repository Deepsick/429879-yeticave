<?php
/**
 * Принимает на вход имя шаблона и данные для шаблона, возвращает html-код с подставленными данными.
 *
 * @param string $name Имя шаблона
 * @param array $data Массив с данными
 *
 * @return string Html-код с подставленными данными
 */
function include_template(string $name, array $data): string
{
	$name = 'templates/'.$name;
	$result = '';

	if (!is_readable($name)) {
		return $result;
	}

	ob_start();
	extract($data);
	require $name;

	$result = ob_get_clean();

	return $result;
}

/**
 * Принимает на вход число и возвращает отформатированную цену.
 *
 * @param string $number Число в виде строки для форматирования
 *
 * @return string Отформатированная цена
 */
function format_number(string $number): string
{
	return number_format(ceil($number), 
						 0, 
						 ',', 
						 ' ')
		.' ₽';
}

/**
 * Возвращает оставшееся время до начала следующего дня.
 *
 * @param string $expiredAt Время экспирации лота
 * 
 * @return string Время до окончания дня в формате ЧЧ:ММ
 */
function get_time_left(string $expiredAt = 'tomorrow'): string
{
	$seconds_left = strtotime($expiredAt) - time();
	$hours_left = floor($seconds_left / 3600);
	$minutes_left = floor(($seconds_left % 3600) / 60);

	if ($hours_left < 10) {
		$hours_left = 0 . $hours_left;
	}

	if ($minutes_left < 10) {
		$minutes_left = 0 . $minutes_left;
	}

	return $hours_left .':'. $minutes_left;
}

/**
 * Получает на вход соединение с БД. Возвращает массив лотов
 * 
 * @param mysqli $link  Ресурс соединения
 * 
 * @return array массив лотов
 */
function get_lots (mysqli $link): array 
{
	$lots_sql = 
		"SELECT
			`l`.id, 
			`l`.`title`, 
			`l`.`start_price`, 
			`l`.`img_url`,
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
			`l`.`category_id` = `c`.`id`
		WHERE 
			`l`.`date_expire` > NOW() 
		AND 
			`l`.`winner_id` IS NULL
		GROUP BY 
			`l`.`id`	
		ORDER BY 
			`l`.`date_create` DESC;";

	$data = mysqli_query($link, $lots_sql);

	return mysqli_fetch_all($data, MYSQLI_ASSOC) ?? []; 
}

/**
 * Получает на вход соединение с БД. Возвращает массив категорий
 * 
 * @param mysqli $link  Ресурс соединения
 * 
 * @return array массив категорий
 */
function get_categories (mysqli $link): array 
{
	$categories_sql = "SELECT * FROM `categories`;";

	$data = mysqli_query($link, $categories_sql);

	return mysqli_fetch_all($data, MYSQLI_ASSOC) ?? []; 
}

/**
 * Получает на вход соединение с БД, id. Возвращает ставки по id лота
 * @param mysqli $link  Ресурс соединения
 * @param string $id  id лота
 * 
 * @return array массив ставок
 */
function get_bets (mysqli $link, string $id = ''): array 
{
	$bets_sql = 
		"SELECT 
			`b`.`price`,
			`b`.date_create,
			`u`.`name` AS `user_name`
		FROM 
			`lots` `l`
		JOIN 
			`bets` `b`
		ON 
			`b`.`lot_id` = `l`.`id`
		JOIN 
			`users` `u`
		ON 
			`b`.`user_id` = `u`.`id`
		WHERE 
			`l`.`id` = ?
		ORDER BY 
			`b`.`date_create` DESC";

	$stmt = db_get_prepare_stmt($link, $bets_sql, [$id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
	return mysqli_fetch_all($result, MYSQLI_ASSOC) ?? []; 
}

/**
 * Получает на вход соединение с БД, id. Возвращает лот по id
 * 
 * @param mysqli $link  Ресурс соединения
 * @param string $id  id лота
 * 
 * @return array|null лот
 */
function get_lot(mysqli $link, string $id = ''): ?array 
{	
	$lot_sql = 
		"SELECT 
			`l`.`id`, 
			`l`.`title`, 
			`l`.`start_price`, 
			`l`.`img_url`,
			`l`.`description`,
			`l`.`bet_step`, 
			`c`.`name` 
		AS 
			`category`
		FROM 
			`lots` `l`
		JOIN 
			`categories` `c`
		ON 
			`c`.`id` = `l`.`category_id`
		WHERE 
			`l`.`id` = ?;";

	$stmt = db_get_prepare_stmt($link, $lot_sql, [$id]);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);

	return mysqli_fetch_assoc($result); 
}

/**
 * Получает на вход дату ставки и форматирует ее в соответствии с шаблоном
 * 
 * @param string $date  Дата в виде строки
 * 
 * @return string возвращает отформатированную дату ставки
 */
function get_format_date(string $date): string
{	
	$minutes_in_hour = 60;
	$seconds_in_minute = 60;
	$hours_in_day = 24;
	$passed_minutes = (time() - strtotime($date)) / $seconds_in_minute;
	if ($passed_minutes >= ($hours_in_day * $minutes_in_hour)) {
		return date('d:m:y H:i', strtotime($date));
	}
	elseif ($passed_minutes >= $minutes_in_hour) {
		return floor(($passed_minutes / $minutes_in_hour)). ' часов назад';
	} 
	elseif ($passed_minutes >= 1) {
		return floor($passed_minutes) . ' минут назад';
	} else {
		return 'только что';
	}
}
