<?php
date_default_timezone_set('Europe/Moscow');

require_once 'db.php';
require_once 'functions.php';
require_once 'mysql_helper.php';
require_once 'user.php';

$categories = get_categories($connection);

if (isset($_GET['id']) && $_GET['id'] !== '') {
    $lot = get_lot($connection, $_GET['id']);

    if (!is_null($lot)) {
        $bets = get_bets($connection, $_GET['id']);

        $lot_page = include_template(
            'lot.php',
            [
            'categories' => $categories,
            'lot' => $lot,
            'bets' => $bets
        ]
        );

        echo $lot_page;
    } else {
        $error_page = include_template(
            '404.php',
            [
            'categories' => $categories,
            'page_title' => 'Yeticave - 404 not found'
        ]
        );

        echo $error_page;
    }
}
