<?php
/**
* @var string[] $errors Массив ошибок
* @var array $user Информация о пользователе
*/
?>

<form class="form container <?=count($errors) ? "form--invalid" : ""; ?>" action="../sign-up.php" method="post"
    enctype="multipart/form-data">
    <h2>Регистрация нового аккаунта</h2>
    <div class="form__item <?=!empty($errors['email']) ? "form__item--invalid" : ""; ?>">
        <label for="email">E-mail*</label>
        <input id="email" type="email" name="email" placeholder="Введите e-mail"
            value="<?=isset($user['email']) ? $user['email'] : ""; ?>" required>
        <?php if (isset($user['email'])): ?>
        <span class="form__error"><?=$errors['email']; ?></span>
        <?php endif; ?>
        <?php if (isset($user['user'])): ?>
        <span class="form__error"><?=$errors['user']; ?></span>
        <?php endif; ?>
    </div>
    <div class="form__item <?=!empty($errors['password']) ? "form__item--invalid" : ""; ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" required>
        <?php if (isset($user['password'])): ?>
        <span class="form__error"><?=$errors['password']; ?></span>
        <?php endif; ?>
    </div>
    <div class="form__item <?=!empty($errors['name']) ? "form__item--invalid" : ""; ?>">
        <label for="name">Имя*</label>
        <input id="name" type="text" name="name" placeholder="Введите имя"
            value="<?=isset($user['name']) ? $user['name'] : ""; ?>" required>
        <?php if (isset($user['name'])): ?>
        <span class="form__error"><?=$errors['name']; ?></span>
        <?php endif; ?>
    </div>
    <div class="form__item <?=!empty($errors['contacts'])? "form__item--invalid" : ""; ?>">
        <label for="message">Контактные данные*</label>
        <textarea id="message" name="contacts" placeholder="Напишите как с вами связаться"
            required><?=isset($user['contacts']) ? $user['contacts'] : ""; ?></textarea>
        <?php if (isset($user['contacts'])): ?>
        <span class="form__error"><?=$errors['contacts']; ?></span>
        <?php endif; ?>
    </div>
    <div
        class="form__item form__item--file form__item--last <?=!empty($errors['file']) ? "form__item--invalid" : ""; ?>">
        <label>Аватар</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="img/avatar.jpg" width="113" height="113" alt="Ваш аватар">
            </div>
        </div>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="avatar_url" id="photo2">
            <label for="photo2">
                <span>+ Добавить</span>
            </label>
        </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="login.php">Уже есть аккаунт</a>
</form>