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
    
    $errors = validate_user_form();

    if ($_FILES['avatar_url']['tmp_name'] !== '') {
		$img_url = check_avatar();
	} 
    
    if (check_user($connection, $user)) {
        $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
    }

    if (!count($errors)) {
        $user_properties = 
        [
            'name' => $user['name'], 
            'email' => $user['email'], 
            'password' => $user['password'], 
            'contacts' => $user['contacts'], 
            'avatar_url' => $img_url
        ];
        $user_id = insert_user($connection, $user_properties);
        if (is_null($user_id)) {
            echo 'Ошибка сохранения в БД';
            exit;
        } 
        else {
            header("Location: login.php");
        } 
    }
}

$sign_up_page = include_template(
    'sign-up.php',
    [
        'page_title' => 'Регистрация',
        'categories' => $categories,
        'errors' => $errors,
        'user' => $user
    ]
);

echo $sign_up_page;