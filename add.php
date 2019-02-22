<?php
require_once 'db.php';
require_once 'functions.php';
require_once 'mysql_helper.php';

$categories = get_categories($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lot = $_POST;

    $required_fields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
    $dictionary = [
        'lot-name' => 'Наименование лота', 
        'category' => 'Категория', 
        'message' => 'Описание', 
        'lot-rate' => 'Начальная ставка', 
        'lot-step' => 'Шаг ставки',
        'lot-date' => 'Дата окончания торгов'
    ];
    $errors = [];

    foreach ($required_fields as $field) {
		if (empty($_POST[$field])) {
            $errors[$field] = 'Это поле надо заполнить';
		}
    }

    if (!is_int($_POST['lot-rate']) || !$_POST['lot-rate'] <= 0) {
        $errors['lot-rate'] = 'Введите число больше нуля';
    }

    if (!is_int($_POST['lot-step']) || !$_POST['lot-step'] <= 0) {
        $errors['lot-step'] = 'Введите число больше нуля';
    }

    $date = \Datetime::createFromFormat('d.m.Y', $_POST['lot-date']);
    if (! $date) {
        $errors['lot-date'] = 'Введите дату в верном формате';
    }

    if ($date < new \Datetime('+1 day')) {
        $errors['lot-date'] = 'Дата должна быть больше текущей хотя бы на один день';
    } 

    if (isset($_FILES['lot_img']['name'])) {
        $file_path = $_FILES['lot_img'];
        var_dump($file_path);

        $file_type = mime_content_type($file_path);
        if ($file_type !== "image/png" || $file_type !== "image/jpeg") {
			$errors['file'] = 'Загрузите картинку в формате GIF';
		} else {
            $file_name = uniqid() . '.png';
            $file_path = __DIR__ . '/img/';
        
            move_uploaded_file($_FILES['lot_img']['tmp_name'], $file_path . $file_name);
        }
    } else {
		$errors['file'] = 'Вы не загрузили файл';
    }
    
    if (count($errors)) {
		$error_page = include_template(
            '404.php',
            [
                'categories' => $categories,
                'page_title' => 'Yeticave - 404 not found'
             ]
        );

        echo $error_page;
	}
	else {
        $found_key = array_search($lot['category'], array_column($categories, 'name'));
        insert_lot($connection, $lot['lot-name'], $categories[$found_key]['id'], $lot['message'], $file_url, $lot['lot-rate'], $lot['lot-step'], $lot['lot-date']);
	}
    var_dump($lot);
}
else {
    $categories = get_categories($connection);

    $add_page = include_template(
        'add-lot.php',
        [
            'categories' => $categories
        ]
    );
    
    echo $add_page;
}