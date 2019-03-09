<?php
require_once 'session.php';
require_once 'db.php';
require_once 'functions.php';

$categories = get_categories($connection);
$bets = null;
$lot = null;
$bet_price = null;
$is_form_shown = null;

if (isset($_GET['id']) && $_GET['id'] !== '') {
    $lot = get_lot($connection, intval($_GET['id']));
    if (is_null($lot)) {
        $error_page_content = include_template(
            '404.php', []);

        $error_page = include_template(
            'inner-layout.php',
            [
                'categories' => $categories,
                'page_title' => 'Yeticave - 404 not found',
                'page_content' => $error_page_content
            ]
        );

        echo $error_page;
        exit;
    } else {
        $bets = get_bets($connection, intval($_GET['id']));

        $is_bet_placed = count(array_filter($bets, function ($bet) {
            return intval($bet['user_id']) === intval($_SESSION['user']['id']);
        }
        ));

        $is_form_shown = isset($_SESSION['user'])
        && !(intval($_SESSION['user']['id']) === intval($lot['user_id']))
        && !(strtotime($lot['date_expire']) <= time())
        && !$is_bet_placed;
    }
}

$errors = [];
$bet_price = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'add-bet.php';
}

$lot_page_content = include_template(
    'lot.php',
    [
        'lot' => $lot,
        'bets' => $bets,
        'errors' => $errors,
        'bet_price' => $bet_price,
        'is_form_shown' => $is_form_shown,
    ]
);

$lot_page = include_template(
    'inner-layout.php',
    [
        'categories' => $categories,
        'page_title' => $lot['title'],
        'page_content' => $lot_page_content
    ]
);

echo $lot_page;
