<?php
require_once 'session.php';

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit();
}

require_once 'db.php';
require_once 'functions.php';

$errors = [];
$lot = [];
$lot['category'] = null;
$categories = get_categories($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lot = $_POST;

    $errors = validate_form($lot);

    if (isset($_FILES['img_url']['tmp_name']) && $_FILES['img_url']['tmp_name'] !== '') {
        $img_url = check_file($_FILES['img_url']);

        if (is_null($img_url)) {
            $errors['file'] = 'Загрузите картинку в формате png или jpeg';
        }
    } else {
        $errors['file'] = 'Вы не загрузили файл';
    }

    if (!count($errors)) {
        $lot_properties =
            [
            'title' => $lot['title'],
            'category_id' => intval($lot['category']),
            'description' => $lot['description'],
            'img_url' => $img_url,
            'start_price' => intval($lot['start_price']),
            'bet_step' => intval($lot['bet_step']),
            'date_expire' => $lot['date_expire'],
            'user_id' => intval($_SESSION['user']['id']),
        ];
        $lot_id = insert_lot($connection, $lot_properties);
        if (is_null($lot_id)) {
            echo 'Ошибка сохранения в БД';
            exit;
        }
        header("Location: lot.php?id=" . $lot_id);
    }
}

$add_page_content = include_template(
    'add-lot.php',
    [
        'categories' => $categories,
        'errors' => $errors,
        'lot' => $lot,
    ]
);

$add_page = include_template(
    'inner-layout.php',
    [
        'categories' => $categories,
        'page_title' => 'Добавление лота',
        'page_content' => $add_page_content,
    ]
);

echo $add_page;
