<?php
/**
* @var string[] $errors Массив ошибок
* @var array $login_info Информация о пользователе
*/
?>

<form class="form container <?=count($errors) ? "form--invalid" : ""; ?>" action="../login.php" method="post">
    <h2>Вход</h2>
    <div class="form__item <?=!empty($errors['email']) ? "form__item--invalid" : ""; ?>">
        <label for="email">E-mail*</label>
        <input id="email" type="email" name="email" placeholder="Введите e-mail"
            value="<?=isset($login_info['email']) ? htmlspecialchars($login_info['email']) : ""; ?>" required>
        <?php if (isset($login_info['email'])): ?>
        <span class="form__error"><?=$errors['email']; ?></span>
        <?php endif; ?>
    </div>
    <div class="form__item form__item--last <?=!empty($errors['password']) ? "form__item--invalid" : ""; ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" required>
        <?php if (isset($login_info['password'])): ?>
        <span class="form__error"><?=$errors['password']; ?></span>
        <?php endif; ?>
    </div>
    <button type="submit" class="button">Войти</button>
</form>