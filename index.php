<?php
require_once 'db.php';
require_once 'functions.php';
require_once 'user.php';

$lots = get_lots($connection);
$categories = get_categories($connection);
$user_info = get_user_info();

$index_content = include_template(
    'index.php',
    [
        'categories' => $categories,
        'ads' => $lots
    ]
);

$index_page = include_template(
    'layout.php',
    [
        'user_info' => $user_info,
        'categories' => $categories,
        'page_content' => $index_content
    ]
);

echo $index_page;
