<?php
$connection = mysqli_connect('localhost', 'root', '1', '429879-yeticave');

if (!$connection) {
    echo 'Ошибка подключения:' . mysqli_connect_error();
    exit('Невозможно подключиться к базе данных');
}

mysqli_set_charset($connection, 'utf8');
