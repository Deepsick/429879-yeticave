<?php
$login_info = $_POST;

$errors = validate_login_form($login_info);

if (!count($errors)) {
    $user = check_user_login($connection, $login_info);
    if (!$user) {
        $errors['email'] = 'Такой пользователь не найден';
    } else {
        if (!check_user_password($login_info, $user)) {
            $errors['password'] = 'Неверный пароль';
        }
    }
}

if (!count($errors)) {
    $_SESSION['user'] = $user;
    header("Location: index.php");
}
