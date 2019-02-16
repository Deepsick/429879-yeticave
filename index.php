<?php
require_once 'functions.php';
require_once 'user.php';
require_once 'categories.php';
require_once 'ads.php';
require_once 'squels.php';

$connection = mysqli_connect('localhost', 'root', '1', '429879-yeticave');
if (!$connection) {
    print('Ошибка подключения: ${mysqli_connect_error()}');
}
mysqli_set_charset($connection, 'utf8');

$lots = get_data($connection, $get_lots);
$category_names = get_data($connection, $get_categories);
$user_info = get_user_info();

$data = array_merge($user_info, ['category_names' => $category_names], ['ads' => $lots]);

$index_content = include_template('index.php', $data);
$data['page_content'] = $index_content;
$index_page = include_template('layout.php', $data);

echo $index_page;

