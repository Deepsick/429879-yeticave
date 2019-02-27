<?php
require_once 'mysql_helper.php';

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
 * @return string Время до окончания торгов
 */
function get_time_left(string $expiredAt = 'tomorrow'): string
{
	$minutes_in_hour = 60;
	$seconds_in_minute = 60;
	$hours_in_day = 24;
	$left_minutes = (strtotime($expiredAt) - time()) / $seconds_in_minute;
	if ($left_minutes >= ($hours_in_day * $minutes_in_hour)) {
		return date('d.m.y в H:i', strtotime($expiredAt));
	}
	elseif ($left_minutes >= $minutes_in_hour) {
		return 'До конца торгов: ' . floor(($left_minutes / $minutes_in_hour)). ' ' . nounEnding(floor(($left_minutes / $minutes_in_hour)), ['час', 'часа', 'часов']);
	} 
	elseif ($left_minutes >= 1) {
		return 'До конца торгов: ' . floor($left_minutes) . ' ' . nounEnding(floor($left_minutes), ['минута', 'минуты', 'минут']);
	} else {
		return 'прямо сейчас';
	}
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
			`b`.`user_id`,
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
			`l`.`date_expire`, 
			`l`.`user_id`,
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
 * Получает на вход соединение с БД, id. Возвращает id лота, отправленного в БД
 * 
 * @param mysqli $link  Ресурс соединения
 * @param array $lot_info  массив данных о лоте
 * 
 * @return number|string|null id лота
 */
function insert_lot(mysqli $link, array $lot_info) 
{	
	extract($lot_info);

	$lot_insert_sql = 
		"INSERT INTO 
			`lots`
			(
				`title`, 
				`category_id`, 
				`description`, 
				`img_url`, 
				`start_price`, 
				`bet_step`, 
				`date_expire`,
				`user_id`
			)  
		VALUES
			(?, ?, ?, ?, ?, ?, ?, ?)";

	$stmt = db_get_prepare_stmt
	(
		$link, 
		$lot_insert_sql, 
		[
			$title, 
			intval($category_id), 
			$description, 
			$img_url, 
			intval($start_price), 
			intval($bet_step), 
			$date_expire,
			intval($user_id)
		]
	);
	mysqli_stmt_execute($stmt);

	if (mysqli_insert_id($link)) {
		return  mysqli_insert_id($link);
	}
	
	return null;
}

/**
 * Получает на вход соединение с БД. Возвращает id пользователя, отправленного в БД
 * 
 * @param mysqli $link  Ресурс соединения
 * @param array $user_info  массив данных о пользователе
 * 
 * @return number|string|null id пользователя
 */
function insert_user(mysqli $link, array $user_info) 
{	
	extract($user_info);
	$hashed_password = password_hash($password, PASSWORD_DEFAULT);
	$user_insert_sql = 
		"INSERT INTO 
			`users`
			(
				`date_register`,
				`name`, 
				`email`, 
				`password`, 
				`contacts`, 
				`avatar_url`
			)  
		VALUES
			(NOW(), ?, ?, ?, ?, ?)";

	$stmt = db_get_prepare_stmt
	(
		$link, 
		$user_insert_sql, 
		[
			$name, 
			$email, 
			$hashed_password, 
			$contacts,  
			$avatar_url
		]
	);

	mysqli_stmt_execute($stmt);

	if (mysqli_insert_id($link)) {
		return  mysqli_insert_id($link);
	}
	
	return null;
}

/**
 * Получает на вход соединение с БД. Возвращает id ставки, отправленной в БД
 * 
 * @param mysqli $link  Ресурс соединения
 * @param array $bet_properties  массив данных о ставке
 * 
 * @return number|string|null id пользователя
 */
function insert_bet(mysqli $link, array $bet_properties)
{	
	extract($bet_properties);

	$bet_insert_sql = 
		"INSERT INTO 
			`bets`
			(
				`date_create`,
				`price`, 
				`user_id`, 
				`lot_id`
			)  
		VALUES
			(NOW(), ?, ?, ?)";

	$stmt = db_get_prepare_stmt
	(
		$link, 
		$bet_insert_sql, 
		[
			$price, 
			$user_id, 
			$lot_id
		]
	);

	mysqli_stmt_execute($stmt);

	if (mysqli_insert_id($link)) {
		return  mysqli_insert_id($link);
	}
	
	return null;
}

/**
 * Получает на вход соединение с БД и проверяет, зарегистрирован ли такой пользователь.
 * 
 * @param mysqli $link  Ресурс соединения
 * @param array $user_info  Данные о пользователе из формы
 * 
 * @return bool возвращает true, если пользователь зарегистрирован, иначе false.
 */
function check_user(mysqli $link, array $user_info): bool 
{	
	$email = mysqli_real_escape_string($link, $user_info['email']);
	$user_sql = 
		"SELECT 
			`id` 
		FROM 
			`users` 
		WHERE 
			`email` = '$email'";

	$res = mysqli_query($link, $user_sql);

	return boolval(mysqli_num_rows($res));
}

/**
 * Получает на вход соединение с БД и возвращает лоты по названию и описанию.
 * 
 * @param mysqli $link  Ресурс соединения
 * @param array $search_request  Поисковый запрос
 * 
 * @return array массив лотов
 */
function search_lots($link, $search_request, int $page_items, int $offset): array
{
	$search_sql = 
		"SELECT 
			`l`.`id`, 
			`l`.`title`, 
			`l`.`img_url`, 
			`l`.`date_expire`, 
			`l`.`start_price`, 
			`c`.`name` 
		FROM 
			`lots` `l`  
		JOIN 
			`categories` `c`
		ON 
			`c`.`id` = `l`.`category_id` 
		WHERE 
			MATCH(`title`, `description`) AGAINST(?)
		ORDER BY 
			`l`.`date_create` 
		DESC 
		LIMIT " . $page_items . ' OFFSET ' . $offset;

	$stmt = db_get_prepare_stmt($link, $search_sql, [$search_request]);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);

	return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Получает на вход соединение с БД и возвращаем пользователя из БД, если он существует.
 * 
 * @param mysqli $link  Ресурс соединения
 * @param array $login_info  Данные о пользователе из формы
 * 
 * @return array|null возвращает данные о пользователе из БД, если пользователь существует и ввел верные данные, иначе null.
 */
function check_user_login(mysqli $link, array $login_info): ?array 
{	
    $email = mysqli_real_escape_string($link, $login_info['email']);
	$login_sql = 
		"SELECT 
			* 
		FROM 
			`users` 
		WHERE 
			`email` = '$email'";

	$res = mysqli_query($link, $login_sql);

	return $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;
}

/**
 * Получает на вход данные из формы и проверяем, соответствует ли пароль БД.
 * 
 * @param array $login_info  Данные о пользователе из формы
 * @param array $user  Данные о пользователе из БД
 * 
 * @return bool возвращает true, если пароль верный, иначе false.
 */
function check_user_password(array $login_info, array $user): bool 
{	
	if (password_verify($login_info['password'], $user['password'])) {
		$_SESSION['user'] = $user;
		return true;
	}
	return false;
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
		return date('d.m.y в H:i', strtotime($date));
	}
	elseif ($passed_minutes >= $minutes_in_hour) {
		return floor(($passed_minutes / $minutes_in_hour)). ' ' . nounEnding(floor(($passed_minutes / $minutes_in_hour)), ['час', 'часа', 'часов']);
	} 
	elseif ($passed_minutes >= 1) {
		return floor($passed_minutes) . ' ' . nounEnding(floor($passed_minutes), ['минута', 'минуты', 'минут']);
	} else {
		return 'только что';
	}
}

/**
 * Получает на вход количество часов(минут) и возвращает правильное окончание для переданного количества
 * 
 * @param string $number  Количество часов или минут
 * @param string[] $words  массив окончаний
 * 
 * @return string возвращает правильно отформатированное окончание для дат
 */
function nounEnding(string $number, array $words = ['one', 'two', 'many']): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    switch (true) {
        case ($number >= 10 && $number <= 20):
            return $words[2];
        
        case ($mod10 > 5):
            return $words[2];
        
        case ($mod10 === 1):
            return $words[0];
        
        case ($mod10 === 2 || $mod10 === 3):
            return $words[1];
        
        default:
            return $words[2];
    }
}

/**
 * Валидирует форму
 * 
 * @return array Возвращает массив ошибок
 */
function validate_form(): array 
{
	$required_fields = ['title', 'category', 'description', 'start_price', 'bet_step', 'date_expire'];

    $errors = [];

    foreach ($required_fields as $field) {
		if (empty($_POST[$field])) {
            $errors[$field] = 'Это поле надо заполнить';
		}
	}
		
    if ($_POST['start_price'] <= 0) {
        $errors['start_price'] = 'Введите число больше нуля';
    }

    if ($_POST['bet_step'] <= 0) {
        $errors['bet_step'] = 'Введите число больше нуля';
	}

	$date_format = 'Y-m-d';
    if (!validate_date($_POST['date_expire'], $date_format)) {
        $errors['date_expire'] = 'Введите дату в верном формате';
	}
	
    if ($_POST['date_expire'] < date($date_format, strtotime('+1 day'))) {
        $errors['date_expire'] = 'Дата должна быть больше текущей хотя бы на один день';
    } 
	
	return $errors;
}

/**
 * Валидирует форму регистрации
 * 
 * @return array Возвращает массив ошибок
 */
function validate_user_form(): array 
{
	$required_fields = ['email', 'password', 'name', 'contacts'];

    $errors = [];

    foreach ($required_fields as $field) {
		if (empty($_POST[$field])) {
            $errors[$field] = 'Это поле надо заполнить';
		}
	}
		
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введен некорректный email';
	}
		
	return $errors;
}

/**
 * Валидирует форму входа
 * 
 * @param string[] $user_input  массив данных о пользователе
 * 
 * @return array Возвращает массив ошибок
 */
function validate_login_form($user_input): array 
{
	$required_fields = ['email', 'password'];

    $errors = [];

    foreach ($required_fields as $field) {
		if (empty($_POST[$field])) {
            $errors[$field] = 'Это поле надо заполнить';
		}
	}
		
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введен некорректный email';
	}
		
	return $errors;
}


/**
 * Если картинка есть, то перемещает в папку img и возвращает путь, иначе возвращает null
 * 
 * @return string|null Возвращает путь до картинки или null
 */
function check_file(): ?string
{
	$file_type = mime_content_type($_FILES['img_url']['tmp_name']);
	$allowed_types = ['image/png', 'image/jpeg'];
	if (!in_array($file_type, $allowed_types)) {
		$errors['file'] = 'Загрузите картинку в формате png или jpeg';
	}
	else {
		$file_path = __DIR__ . '/img/';
		$img_url = $file_path . $_FILES['img_url']['name'];
		move_uploaded_file($_FILES['img_url']['tmp_name'], $img_url);
		return 'img/' . $_FILES['img_url']['name'];
	}

	return null;
}

/**
 * Если аватар есть, то перемещает в папку img и возвращает путь, иначе возвращает null
 * 
 * @return string|null Возвращает путь до аватара или null
 */
function check_avatar(): ?string
{
	$file_type = mime_content_type($_FILES['avatar_url']['tmp_name']);
	$allowed_types = ['image/png', 'image/jpeg'];
	if (!in_array($file_type, $allowed_types)) {
		$errors['file'] = 'Загрузите картинку в формате png или jpeg';
	}
	else {
		$file_path = __DIR__ . '/img/';
		$img_url = $file_path . $_FILES['avatar_url']['name'];
		move_uploaded_file($_FILES['avatar_url']['tmp_name'], $img_url);
		return 'img/' . $_FILES['avatar_url']['name'];
	}

	return null;
}

/**
 * Проверка на правильность формата даты
 * 
 * @param string $date  Дата
 * @param string $format  формат, на соответствие которого проверяется дата
 * 
 * @return string true/false
 */
function validate_date(string $date, string $format): string 
{  
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}