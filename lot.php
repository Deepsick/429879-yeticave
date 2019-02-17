<?php
require_once 'db.php';
require_once 'functions.php';
require_once 'mysql_helper.php';
require_once 'user.php';

$categories = get_categories($connection);
$user_info = get_user_info();

if (isset($_GET['id']) && $_GET['id'] !== '') {
    $lot = get_lot($connection, $_GET['id']);
    $bets = get_bets($connection, $_GET['id']);

    if ($lot) {
        $lot_content = include_template(
            'lot.php',
            [
            'categories' => $categories,
            'lot' => $lot[0],
            'bets' => $bets,
        ]
        );

        $lot_page = include_template(
            'layout.php',
            [
            'user_info' => $user_info,
            'categories' => $categories,
            'page_content' => $lot_content,
        ]
        );

        echo $lot_page;
    } else {
        http_response_code(404);
    }
}

