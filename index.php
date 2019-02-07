<?php
require_once('functions.php');
require_once('user.php');
require_once('categories.php');
require_once('ads.php');

$user_info = get_user_info();
$category_names = get_category_names();
$ads = get_ads();

$data = array_merge($user_info, $category_names, $ads);

$index_content = include_template('index.php', $data);
$data['page_content'] = $index_content;
$index_page = include_template('layout.php', $data);

print($index_page);
?>
