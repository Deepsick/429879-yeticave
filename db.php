<?php
if (!file_exists('db_config.php')) {
    exit('Нет конфигурации для БД');
}

require_once 'db_config.php';

$connection = mysqli_connect($database_host, $database_user, $database_password, $database_name);

if (!$connection) {
    echo 'Ошибка подключения:' . mysqli_connect_error();
    exit('Невозможно подключиться к базе данных');
}

mysqli_options($connection, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
mysqli_set_charset($connection, 'utf8');
