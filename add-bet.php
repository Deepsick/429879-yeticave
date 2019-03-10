<?php
if (isset($_POST['bet_price'])) {
    $bet_price = intval($_POST['bet_price']);
}
$lot = get_lot($connection, intval($_REQUEST['id']));
$bets = get_bets($connection, intval($_REQUEST['id']));

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
        'price' => intval($bet_price),
        'user_id' => intval($_SESSION['user']['id']),
        'lot_id' => intval($lot['id']),
    ];
    $bet_id = insert_bet($connection, $bet_properties);

    if (is_null($bet_id)) {
        echo 'Ошибка сохранения в БД';
        exit;
    }
    header("Location: lot.php?id=" . $_REQUEST['id']);
}
