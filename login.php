<?php
require_once 'session.php';
require_once 'db.php';
require_once 'functions.php';

$errors = [];
$login_info = null;
$user = null;
$categories = get_categories($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'do-login.php';
}

$login_page_content = include_template(
    'login.php',
    [
        'errors' => $errors,
        'login_info' => $login_info,
    ]
);

$login_page = include_template(
    'inner-layout.php',
    [
        'categories' => $categories,
        'page_title' => 'Вход',
        'page_content' => $login_page_content,
    ]
);

echo $login_page;
