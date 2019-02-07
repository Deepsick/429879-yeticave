<?php 

/** 
 * Принимает на вход имя шаблона и данные для шаблона, возвращает html-код с подставленными данными
 * 
 * @param $name 
 * @param $data 
 * @return string
 */
function include_template($name, $data) {
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
};

/**
 * Принимает на вход число и возвращает отформатированную цену
 * 
 * @param $number
 * @return string
 */
function format_number($number) {
    $rounded_number = ceil($number);
    $formatted_number = number_format($rounded_number, 0, ',', ' ');
    $price = $formatted_number . ' ₽';
    return $price;
};
?>