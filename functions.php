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
 * Получает на вход соединение с БД, запрос sql. Возвращает массив данных
 * 
 * @param mysqli $link  Ресурс соединения
 * @param string $sql  SQL запрос 
 * 
 * @return array массив данных
 */
function get_data (mysqli $link, string $sql): array 
{
	$data = mysqli_query($link, $sql);

	if (!$data) {
		$error = mysqli_error($link);
		print('Ошибка MySQL: ${$error}');
	};

	return mysqli_fetch_all($data, MYSQLI_ASSOC); 
}