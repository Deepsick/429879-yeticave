<?php
require_once 'session.php';
require_once 'db.php';
require_once 'functions.php';
require_once 'mysql_helper.php';

$categories = get_categories($connection);
$bets = null;
$lot = null;
$bet_price = null;


if (isset($_GET['id']) && $_GET['id'] !== '') {
    $lot = get_lot($connection, $_GET['id']);
    if (is_null($lot)) {
        $error_page = include_template(
            '404.php',
            [
                'categories' => $categories,
                'page_title' => 'Yeticave - 404 not found'
             ]
        );

        echo $error_page;
        exit;
    } 
    else {
        $bets = get_bets($connection, $_GET['id']);
    }
}

$errors = [];
$bet_price = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bet_price = intval($_POST['bet_price']);
    $lot = get_lot($connection, $_REQUEST['id']);
    $bets = get_bets($connection, $_REQUEST['id']);
    
    if (empty($_POST['bet_price'])) {
        $errors['bet_price'] = 'Это поле надо заполнить';
    }
    
    if (!is_int($bet_price) || $bet_price <= 0) {
        $errors['bet_price'] = 'Ставка должна быть целым положительным числом'; 
    }

    if ($bet_price <= (($bets[0]['price'] ??  $lot['start_price']) + $lot['bet_step'])) {
        $errors['bet_price'] = 'Ставка должна быть больше цены лота с прибавленным шагом'; 
    }

    if (!count($errors)) {
        $bet_properties = 
        [
            'price' => $bet_price, 
            'user_id' => $_SESSION['user']['id'], 
            'lot_id' => $lot['id']
        ];
        $bet_id = insert_bet($connection, $bet_properties);

        if (is_null($bet_id)) {
            echo 'Ошибка сохранения в БД';
            exit;
        } 
        else {
            header("Location: lot.php?id=" . $_REQUEST['id']);
        } 
    }
}

$lot_page = include_template(
    'lot.php',
    [
        'categories' => $categories,
        'lot' => $lot,
        'bets' => $bets,
        'errors' => $errors,
        'bet_price' => $bet_price
    ]
);

echo $lot_page;