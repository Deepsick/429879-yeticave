<?php
require_once 'session.php';

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit();
}

require_once 'db.php';
require_once 'functions.php';

$errors = [];
$lot = null;
$categories = get_categories($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lot = $_POST;

    $errors = validate_form();

    if ($_FILES['img_url']['tmp_name'] !== '') {
        $img_url = check_file();
    } else {
        $errors['file'] = 'Вы не загрузили файл';
    }

    if (!count($errors)) {
        $lot_properties =
            [
            'title' => $lot['title'],
            'category_id' => $lot['category'],
            'description' => $lot['description'],
            'img_url' => $img_url,
            'start_price' => $lot['start_price'],
            'bet_step' => $lot['bet_step'],
            'date_expire' => $lot['date_expire'],
            'user_id' => $_SESSION['user']['id'],
        ];
        $lot_id = insert_lot($connection, $lot_properties);
        if (is_null($lot_id)) {
            echo 'Ошибка сохранения в БД';
            exit;
        } else {
            header("Location: lot.php?id=" . $lot_id);
        }
    }
}

$add_page = include_template(
    'add-lot.php',
    [
        'page_title' => 'Добавление лота',
        'categories' => $categories,
        'errors' => $errors,
        'lot' => $lot,
    ]
);

echo $add_page;
