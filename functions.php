<?php 
/**
 * Принимает на вход имя шаблона и данные для шаблона, возвращает html-код с подставленными данными.
 *
 * @param $name Имя шаблона
 * @param $data Массив с данными
 *
 * @return string Html-код с подставленными данными
 */
function include_template(string $name, array $data)
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
 * @param $number Число в виде строки для форматирования
 *
 * @return string Отформатированная цена
 */
function format_number(string $number)
{
    $rounded_number = ceil($number);
    $formatted_number = number_format($rounded_number, 0, ',', ' ');
    $price = $formatted_number.' ₽';

    return $price;
}

/**
 * Возвращает оставшееся время до начала следующего дня.
 *
 * @return string Время до окончания дня в формате ЧЧ:ММ
 */
function get_time_left()
{
    $now = time();
    $tomorrow = strtotime('tomorrow');
    $seconds_left = $tomorrow - $now;
    $hours_left = floor($seconds_left / 3600);
    $minutes_left = floor(($seconds_left % 3600) / 60);
    if ($hours_left < 10) {
        $hours_left = 0 .$hours_left;
    }
    if ($minutes_left < 10) {
        $minutes_left = 0 .$minutes_left;
    }
    $timer = $hours_left.':'.$minutes_left;

    return $timer;
}
?>