<?php
date_default_timezone_set('Europe/Moscow');

require_once 'session.php';
require_once 'db.php';
require_once 'functions.php';
require_once 'getwinner.php';

$lots = get_lots($connection);
$categories = get_categories($connection);

$index_content = include_template(
    'index.php',
    [
        'categories' => $categories,
        'ads' => $lots,
    ]
);
$index_page = include_template(
    'layout.php',
    [
        'categories' => $categories,
        'page_content' => $index_content,
        'page_title' => 'Yeticave - Главная страница',
    ]
);

echo $index_page;
