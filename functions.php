<?php
require_once 'mysql_helper.php';

/**
 * Возвращает html-код с подставленными данными.
 *
 * @param string $name Имя шаблона
 * @param array $data Массив с данными
 * @return string Html-код с подставленными данными
 */
function include_template(string $name, array $data): string
{
    $name = 'templates/' . $name;
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
 * Возвращает отформатированную цену.
 *
 * @param string $number Число в виде строки для форматирования
 * @return string Отформатированная цена
 */
function format_number(string $number): string
{
    return number_format(ceil($number),
        0,
        ',',
        ' ')
        . ' ₽';
}

/**
 * Возвращает оставшееся время до начала следующего дня.
 *
 * @param string $expiredAt Время экспирации лота
 * @return string Время до окончания торгов
 */
function get_time_left(string $expiredAt = 'tomorrow'): string
{
    return 'Окончание торгов через ' . get_short_time_left($expiredAt);
}

/**
 * Возвращает оставшееся время до начала следующего дня.
 *
 * @param string $expiredAt Время экспирации лота
 * @return string Время до окончания торгов
 */
function get_short_time_left(string $expiredAt = 'tomorrow'): string
{
    $minutes_in_hour = 60;
    $seconds_in_minute = 60;
    $hours_in_day = 24;
    $left_minutes = (strtotime($expiredAt) - time()) / $seconds_in_minute;

    if ($left_minutes >= ($hours_in_day * $minutes_in_hour)) {
        return date('d.m.y в H:i', strtotime($expiredAt));
    } elseif ($left_minutes >= $minutes_in_hour) {
        return floor($left_minutes / $minutes_in_hour) . ' ' . 
            nounEnding(
                floor($left_minutes / $minutes_in_hour), 
                ['час', 'часа', 'часов']
            );
    } elseif ($left_minutes >= 1) {
        return floor($left_minutes) . ' ' . 
            nounEnding(
                floor($left_minutes), 
                ['минута', 'минуты', 'минут']
            );
    }
    
    return 'несколько секунд';
}

/**
 * Возвращает массив лотов.
 *
 * @param mysqli $link  Ресурс соединения
 * @return array массив лотов
 */
function get_lots(mysqli $link): array
{
    $lots_sql =
        "SELECT
			`l`.`id`,
			`l`.`title`,
			`l`.`start_price`,
			`l`.`img_url`,
			`l`.`date_expire`,
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
			`l`.`id`, `l`.`date_create`
		ORDER BY
			`l`.`date_create` DESC";

    $data = mysqli_query($link, $lots_sql);

    return mysqli_fetch_all($data, MYSQLI_ASSOC) ?? [];
}

/**
 * Возвращает массив категорий.
 *
 * @param mysqli $link  Ресурс соединения
 * @return array массив категорий
 */
function get_categories(mysqli $link): array
{
    $categories_sql = "SELECT * FROM `categories`";

    $data = mysqli_query($link, $categories_sql);

    return mysqli_fetch_all($data, MYSQLI_ASSOC) ?? [];
}

/**
 * Возвращает ставки по id лота.
 * 
 * @param mysqli $link  Ресурс соединения
 * @param int $id  id лота
 * @return array массив ставок
 */
function get_bets(mysqli $link, int $id): array
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
 * Возвращает количество лотов по в данной категории.
 *
 * @param mysqli $link  Ресурс соединения
 * @param string $id  Id категории
 * @return int|null Количество лотов
 */
function category_count_of_lots($link, $id): ?int
{
    $lots_amount_sql =
        "SELECT
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
			`l`.`category_id` = ?
		AND
			`l`.`date_expire` > NOW()
		GROUP BY
			`l`.`id`, `l`.`date_create`
		ORDER BY
			`l`.`date_create` DESC";

    $stmt = db_get_prepare_stmt($link, $lots_amount_sql, [$id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_num_rows($result);
}

/**
 * Возвращает массив лотов из этой категории.
 * 
 * @param mysqli $link  Ресурс соединения
 * @param int $id  id категории
 * @param int $limit Лотов на странице
 * @param int $offset Сколько пропустить лотов от начала
 * @return array массив лотов
 */
function get_lot_by_category(mysqli $link, int $id, int $limit, int $offset): array
{
    $lots_sql =
        "SELECT
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
			`l`.`category_id` = ?
		AND
			`l`.`date_expire` > NOW()
		GROUP BY
		 	`l`.`id`, `l`.`date_create`
		ORDER BY
			`l`.`date_create`
		DESC
		LIMIT " . $limit . ' OFFSET ' . $offset;

    $stmt = db_get_prepare_stmt($link, $lots_sql, [$id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC) ?? [];
}

/**
 * Возвращает лот по id.
 *
 * @param mysqli $link  Ресурс соединения
 * @param int $id  id лота
 * @return array|null лот
 */
function get_lot(mysqli $link, int $id): ?array
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
			`l`.`id` = ?";

    $stmt = db_get_prepare_stmt($link, $lot_sql, [$id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

/**
 * Возвращает категорию по id.
 *
 * @param mysqli $link  Ресурс соединения
 * @param int $id  id категории
 * @return array Категория
 */
function get_category(mysqli $link, int $id): array
{
    $category_sql =
        "SELECT
			`c`.`name`,
			`c`.`id`
		FROM
			`categories` `c`
		WHERE
			`c`.`id` = ?";

    $stmt = db_get_prepare_stmt($link, $category_sql, [$id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result) ?? [];
}

/**
 * Возвращает ставки пользователя по id.
 *
 * @param mysqli $link  Ресурс соединения
 * @param int $user_id  id пользователя
 * @return array Ставки
 */
function get_user_bets(mysqli $link, int $user_id): array
{
    $bets_sql =
        "SELECT
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
		`u`.`id` = `l`.`user_id`
	WHERE
		`b`.`user_id` = ?";

    $stmt = db_get_prepare_stmt($link, $bets_sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC) ?? [];
}

/**
 * Возвращает id лота, отправленного в БД.
 *
 * @param mysqli $link  Ресурс соединения
 * @param array $lot_info  массив данных о лоте
 * [
 *  'title' => 'Доска для спуска',
*   'category_id' => 2,
*   'description' => 'Классная доска, сам катал',
*   'img_url' => 'img/picture-1.png',
*   'start_price' => 400,
*   'bet_step' => 20,
*   'date_expire' => '03-20-2019',
*   'user_id' => 5
 * ]
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
            $category_id,
            $description,
            $img_url,
            $start_price,
            $bet_step,
            $date_expire,
            $user_id,
        ]
    );
    mysqli_stmt_execute($stmt);

    $id = mysqli_insert_id($link);
    if ($id) {
        return $id;
    }

    return null;
}

/**
 * Возвращает id пользователя, отправленного в БД.
 *
 * @param mysqli $link  Ресурс соединения
 * @param array $user_info  Массив данных о пользователе
 * [
 *  'name' => 'Пользователь',
 *  'email' => '1@1mail.ru',
 *  'password' => '$2y$10$.BeRPi07fTNGi.kYAWTxJuvbquK3oQKJFQY/3fAJgZgVU/t/x4FCO',
 *  'contacts' => '+8732462379 звоните',
 *  'avatar_url' => 'img/category-3.jpg'
 * ]
 * @return number|string|null id пользователя
 */
function insert_user(mysqli $link, array $user_info)
{
    extract($user_info);

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
            $password,
            $contacts,
            $avatar_url,
        ]
    );

    mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($link);

    if ($id) {
        return $id;
    }

    return null;
}

/**
 * Возвращает id ставки, отправленной в БД.
 *
 * @param mysqli $link  Ресурс соединения
 * @param array $bet_properties  массив данных о ставке
 * [
 *     'price' => 12234,
 *     'user_id' => 3,
 *     'lot_id' => 5
 * ]
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
            $lot_id,
        ]
    );

    mysqli_stmt_execute($stmt);

    $id = mysqli_insert_id($link);
    if ($id) {
        return $id;
    }

    return null;
}

/**
 * Проверяет, зарегистрирован ли такой пользователь.
 *
 * @param mysqli $link  Ресурс соединения
 * @param array $user_info  Данные о пользователе из формы
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
 * Возвращает истекшие лоты без победителя.
 *
 * @param  mixed $link
 * @return array
 */
function get_expired_lots(mysqli $link): array
{
    $expired_lots_sql =
        "SELECT
			`l`.`title`,
			`l`.`id`,
			MAX(`b`.`price`) AS `max_price`
		FROM
			`lots` `l`
		JOIN
			`bets` `b`
		ON
			`b`.`lot_id` = `l`.`id`
		WHERE
			`l`.`date_expire` <= NOW()
		AND
			`l`.`winner_id` IS NULL
		GROUP BY
			`l`.`title`, `l`.`id`";

    $stmt = db_get_prepare_stmt($link, $expired_lots_sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC) ?? [];
}

/**
 * Возвращает массив победителей.
 *
 * @param  mysqli $link
 * @param  array $expired_lots Истекшие лоты без победителя
 * @return array
 */
function get_winners(mysqli $link, array &$expired_lots): array
{
    $winners = [];
    $winner_info_sql =
        "SELECT
			`u`.`name` AS `user_name`,
			`u`.`id` AS `user_id`,
			`u`.`email`,
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
			`b`.`price` = ?
		AND
			`l`.`id` = ?";

    foreach ($expired_lots as &$lot) {
        $stmt = db_get_prepare_stmt(
            $link, 
            $winner_info_sql, 
            [
                $lot['max_price'], 
                $lot['id']
            ]
        );

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $winner = mysqli_fetch_assoc($result);

        $lot['winner'] = $winner;
    }


    return $expired_lots;
}

/**
 * Добавляет id победителей в БД.
 *
 * @param  mysqli $link Ресурся соединения
 * @param  array $lots Истекшие лоты с победителем
 * @return array
 */
function insert_winners(mysqli $link, array &$lots): array
{
    $winner_insert_sql =
        "UPDATE
			`lots` `l`
		SET
			`winner_id` = ?
		WHERE
			`l`.`id` = ?";

    foreach($lots as &$lot) {
        $stmt = db_get_prepare_stmt
            (
                $link,
                $winner_insert_sql,
                [
                    intval($lot['winner']['user_id']),
                    intval($lot['id'])
                ]
            );

        mysqli_stmt_execute($stmt);

        if (mysqli_affected_rows($link) === 0) {
            unset($lot['winner']);
        } 
    }

    return $lots;
}

/**
 * Возвращает sql-запрос полнотекстового поиска лотов по названию и описанию.
 *
 * @return string
 */
function get_search_lot_sql(): string
{
    return
        "SELECT
			`l`.`id`,
			`l`.`title`,
			`l`.`img_url`,
			`l`.`date_expire`,
			`l`.`description`,
			`l`.`start_price`,
			MAX(`b`.`price`) AS `max_price`,
			COUNT(`b`.`price`) AS `bets_amount`,
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
			MATCH(`title`, `description`) AGAINST(?)
		GROUP BY
			`l`.`id`, `l`.`date_create`
		ORDER BY
			`l`.`date_create` DESC";
}

/**
 * Возвращает лоты по названию и описанию.
 *
 * @param mysqli $link  Ресурс соединения
 * @param string $search_request  Поисковый запрос
 * @param int $limit Лотов на странице
 * @param int $offset Сколько пропустить лотов от начала
 * @return array массив лотов
 */
function search_lots($link, $search_request, int $limit, int $offset): array
{
    $search_sql = get_search_lot_sql() . ' LIMIT ' . $limit . ' OFFSET ' . $offset;

    $stmt = db_get_prepare_stmt($link, $search_sql, [$search_request]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC) ?? [];
}

/**
 * Возвращает количество лотов по названию и описанию.
 *
 * @param mysqli $link  Ресурс соединения
 * @param string $search_request  Поисковый запрос
 * @return int|null Количество лотов
 */
function search_count_of_lots($link, $search_request): ?int
{
    $search_sql = get_search_lot_sql();

    $stmt = db_get_prepare_stmt($link, $search_sql, [$search_request]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_num_rows($result);
}

/**
 * Возвращает пользователя из БД, если он существует.
 *
 * @param mysqli $link  Ресурс соединения
 * @param array $login_info  Данные о пользователе из формы
 * @return array|null возвращает данные о пользователе из БД, если пользователь существует и ввел верные данные, иначе null
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
 * Проверяет, соответствует ли пароль паролю в БД.
 *
 * @param array $login_info  Данные о пользователе из формы
 * @param array $user  Данные о пользователе из БД
 * @return bool возвращает true, если пароль верный, иначе false
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
 * Форматирует дату ставки в соответствии с шаблоном.
 *
 * @param string $date  Дата в виде строки
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
    } elseif ($passed_minutes >= $minutes_in_hour) {
        return floor(($passed_minutes / $minutes_in_hour)) . ' ' . 
            nounEnding(
                floor(($passed_minutes / $minutes_in_hour)), 
                ['час', 'часа', 'часов']
            );
    } elseif ($passed_minutes >= 1) {
        return floor($passed_minutes) . ' ' . 
            nounEnding(
                floor($passed_minutes), 
                ['минута', 'минуты', 'минут']
            );
    } else {
        return 'только что';
    }
}

/**
 * Возвращает правильное окончание для переданного количества.
 *
 * @param string $number  Количество часов или минут
 * @param string[] $words  массив окончаний
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
 * Валидирует форму.
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
        $errors['start_price'] = 'Введите целое число больше нуля';
    }

    if ($_POST['bet_step'] <= 0) {
        $errors['bet_step'] = 'Введите целое число больше нуля';
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
 * Валидирует форму регистрации.
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
 * Валидирует форму входа.
 *
 * @param string[] $user_input  массив данных о пользователе
 * @return array Возвращает массив ошибок
 */
function validate_login_form($user_input): array
{
    $required_fields = ['email', 'password'];

    $errors = [];

    foreach ($required_fields as $field) {
        if (empty($user_input[$field])) {
            $errors[$field] = 'Это поле надо заполнить';
        }
    }

    if (!filter_var($user_input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введен некорректный email';
    }

    return $errors;
}

/**
 * Если картинка есть, то перемещает в папку img и возвращает путь, иначе возвращает null.
 *
 * @param array $fileElement Фаил, который проверяется
 * @return string|null Возвращает путь до картинки или null
 */
function check_file($fileElement): ?string
{
    $file_type = mime_content_type($fileElement['tmp_name']);
    $allowed_types = ['image/png', 'image/jpeg'];

    if (in_array($file_type, $allowed_types)) {
        $file_path = __DIR__ . '/img/';
        $img_url = $file_path . $fileElement['name'];
        move_uploaded_file($fileElement['tmp_name'], $img_url);
        return 'img/' . $fileElement['name'];
    }

    return null;
}

/**
 * Проверяет на правильность формат переданной даты.
 *
 * @param string $date  Дата
 * @param string $format  формат, на соответствие которого проверяется дата
 * @return string true/false
 */
function validate_date(string $date, string $format): string
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
