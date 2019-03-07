<?php
require_once 'session.php';
require_once 'db.php';
require_once 'functions.php';

$errors = [];
$login_info = null;
$user = null;
$categories = get_categories($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_info = $_POST;

    $errors = validate_login_form($login_info);

    $user = check_user_login($connection, $login_info);
    if (!$user) {
        $errors['email'] = 'Такой пользователь не найден';
    } else {
        if (!check_user_password($login_info, $user)) {
            $errors['password'] = 'Неверный пароль';
        }
    }

    if (!count($errors)) {
        $_SESSION['user'] = $user;
        header("Location: index.php");
    }
}

$login_page = include_template(
    'login.php',
    [
        'page_title' => 'Вход',
        'categories' => $categories,
        'errors' => $errors,
        'login_info' => $login_info,
    ]
);

echo $login_page;
