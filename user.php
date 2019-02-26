<?php
/**
 * Возвращает информацию о пользователе.
 *
 * @return array Массив с данными о пользователе
 */
function get_user_info(): array
{
    return [
        'is_auth' => isset($_SESSION['user']) ? 1 : 0,
        'user_name' => isset($_SESSION['user']) ? $_SESSION['user']['name'] : 'Аноним',
    ];
}
