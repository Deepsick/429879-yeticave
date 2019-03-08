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
    $lot = get_lot($connection, $_GET['id']);
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
        $bets = get_bets($connection, $_GET['id']);

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
    $bet_price = intval($_POST['bet_price']);
    $lot = get_lot($connection, $_REQUEST['id']);
    $bets = get_bets($connection, $_REQUEST['id']);

    $is_bet_placed = count(array_filter($bets, function ($bet) {
        return intval($bet['user_id']) === intval($_SESSION['user']['id']);
    }
    ));

    $is_form_shown = isset($_SESSION['user'])
    && !(intval($_SESSION['user']['id']) === intval($lot['user_id']))
    && !(strtotime($lot['date_expire']) <= time())
    && !$is_bet_placed;

    if (empty($_POST['bet_price'])) {
        $errors['bet_price'] = 'Это поле надо заполнить';
    }

    if (!is_int($bet_price) || $bet_price <= 0) {
        $errors['bet_price'] = 'Ставка должна быть целым положительным числом';
    }

    if ($bet_price < (($bets[0]['price'] ?? $lot['start_price']) + $lot['bet_step'])) {
        $errors['bet_price'] = 'Минимальная ставка - ' . (($bets[0]['price'] ?? $lot['start_price']) + $lot['bet_step']);
    }

    if (!count($errors)) {
        $bet_properties =
            [
            'price' => $bet_price,
            'user_id' => $_SESSION['user']['id'],
            'lot_id' => $lot['id'],
        ];
        $bet_id = insert_bet($connection, $bet_properties);

        if (is_null($bet_id)) {
            echo 'Ошибка сохранения в БД';
            exit;
        } else {
            header("Location: lot.php?id=" . $_REQUEST['id']);
        }
    }
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
