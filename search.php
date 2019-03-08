<?php
require_once 'session.php';
require_once 'db.php';
require_once 'functions.php';

$categories = get_categories($connection);
$lots = null;
$pages_count = null;
$cur_page = null;
$pages = null;
$search = null;

if (isset($_GET['search']) && $_GET['search'] !== '' && strlen($_GET['search']) >= 3) {
    $search = trim($_GET['search']) ?? '';

    if ($search) {
        $cur_page = $_GET['page'] ?? 1;
        $page_items = 9;

        $items_count = search_count_of_lots($connection, $search);
        $pages_count = ceil($items_count / $page_items);
        $offset = ($cur_page - 1) * $page_items;

        $pages = range(1, $pages_count);

        $lots = search_lots($connection, $search, $page_items, $offset);
    }

    $search_page = include_template(
        'search.php',
        [
            'page_title' => 'Результаты поиска',
            'categories' => $categories,
            'lots' => $lots,
            'search' => $search,
            'pages' => $pages,
            'pages_count' => $pages_count,
            'cur_page' => $cur_page,
        ]
    );

    echo $search_page;
    exit;
}
header("Location: " . $_SERVER['HTTP_REFERER']);

