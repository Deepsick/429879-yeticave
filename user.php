<?php
/**
 * Возвращает информацию о пользователе.
 *
 * @return array Массив с данными о пользователе
 */
function get_user_info()
{
    return [
        'is_auth' => rand(0, 1),
        'user_name' => 'Артем',
    ];
}
?>