<?php
require_once 'session.php';
require_once 'db.php';
require_once 'functions.php';

$categories = get_categories($connection);
$category = null;
$lots = null;
$cur_page = null;
$pages_count = null;
$pages = null;

if (isset($_GET['id']) && $_GET['id'] !== '') {
    $id = $_GET['id'];

    $category = get_category($connection, $id);

    if (!$category) {
        $error_page = include_template(
            '404.php',
            [
                'categories' => $categories,
                'page_title' => 'Yeticave - 404 not found',
            ]
        );

        echo $error_page;
        exit;
    }

    $cur_page = $_GET['page'] ?? 1;
    $page_items = 9;

    $items_count = category_count_of_lots($connection, $id);
    $pages_count = ceil($items_count / $page_items);
    $offset = ($cur_page - 1) * $page_items;

    $pages = range(1, $pages_count);

    $lots = get_lot_by_category($connection, $id, $page_items, $offset);
}

$category_page_content = include_template(
    'category.php',
    [
        'category' => $category,
        'lots' => $lots,
        'pages' => $pages,
        'pages_count' => $pages_count,
        'cur_page' => $cur_page
    ]
);

$category_page = include_template(
    'inner-layout.php',
    [
        'categories' => $categories,
        'page_title' => $category['name'],
        'page_content' => $category_page_content
    ]
);

echo $category_page;
