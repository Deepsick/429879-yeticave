<?php
require_once 'functions.php';
require_once 'user.php';

$connection = mysqli_connect('localhost', 'root', '1', '429879-yeticave');
if (!$connection) {
    echo 'Ошибка подключения:' . mysqli_connect_error();
    exit('Невозможно подключиться к базе данных');
}
mysqli_set_charset($connection, 'utf8');

$lots = get_lots($connection);
$category_names = get_categories($connection);
$user_info = get_user_info();

$index_content = include_template('index.php',
                                  [
    'category_names' => $category_names,
    'ads' => $lots
]);
$index_page = include_template('layout.php',
                               [
    'user_info' => $user_info,
    'category_names' => $category_names,
    'page_content' => $index_content
]);

echo $index_page;
