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
 * @return array массив данных или ошибку MYSQL
 */
function get_lots (mysqli $link): array 
{
	$lots_sql = "SELECT `l`.`title`, `l`.`start_price`, `l`.`img_url`, MAX(`b`.`price`) AS `max_price`, `c`.`name` AS `category` FROM `lots` `l`
				 LEFT JOIN `bets` `b`
				 ON `l`.`id` = `b`.`lot_id`
				 JOIN `categories` `c`
				 ON `l`.`category_id` = `c`.`id`
				 WHERE `l`.`date_expire` > NOW() AND `l`.`winner_id` IS NULL
				 GROUP BY `l`.`id`	
				 ORDER BY `l`.`date_create` DESC;";

	$data = mysqli_query($link, $lots_sql);

	if (!$data) {
		$error = mysqli_error($link);
		return 'Ошибка MySQL: {$error}';
	};

	return mysqli_fetch_all($data, MYSQLI_ASSOC); 
}

/**
 * Получает на вход соединение с БД. Возвращает массив имен категорий
 * 
 * @param mysqli $link  Ресурс соединения
 * 
 * @return array массив данных или ошибку MYSQL
 */
function get_categories (mysqli $link): array 
{
	$categories_sql = "SELECT * FROM `categories`;";

	$data = mysqli_query($link, $categories_sql);

	if (!$data) {
		$error = mysqli_error($link);
		return 'Ошибка MySQL: {$error}';
	};

	return mysqli_fetch_all($data, MYSQLI_ASSOC); 
}
