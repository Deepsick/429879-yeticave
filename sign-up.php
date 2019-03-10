<?php
require_once 'session.php';
require_once 'db.php';
require_once 'functions.php';

$errors = [];
$user = null;
$img_url = null;
$categories = get_categories($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST;

    $errors = validate_user_form($user);

    if (isset($_FILES['avatar_url']['tmp_name']) && $_FILES['avatar_url']['tmp_name'] !== '') {
        $img_url = check_file($_FILES['avatar_url']);

        if (is_null($img_url)) {
            $errors['file'] = 'Загрузите картинку в формате png или jpeg';
        }
    }

    if (empty($errors['email']) && check_user($connection, $user)) {
        $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
    }

    if (!count($errors)) {
        $hashed_password = password_hash($user['password'], PASSWORD_DEFAULT);

        $user_properties =
            [
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => $hashed_password,
            'contacts' => $user['contacts'],
            'avatar_url' => $img_url,
        ];
        $user_id = insert_user($connection, $user_properties);
        if (is_null($user_id)) {
            echo 'Ошибка сохранения в БД';
            exit;
        }
        header("Location: login.php");
    }
}

$sign_up_page_content = include_template(
    'sign-up.php',
    [
        'errors' => $errors,
        'user' => $user,
    ]
);

$sign_up_page = include_template(
    'inner-layout.php',
    [
        'categories' => $categories,
        'page_title' => 'Регистрация',
        'page_content' => $sign_up_page_content,
    ]
);

echo $sign_up_page;
