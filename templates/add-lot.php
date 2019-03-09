<?php
/**
* @var string[] $errors Массив ошибок
* @var string[] $categories Массив имен категорий
* @var array $lot Информация о лоте
*/
?>

<form class="form form--add-lot container <?=count($errors) ? "form--invalid" : ""; ?>" action="../add.php"
    method="post" enctype="multipart/form-data">
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?=!empty($errors['title']) ? "form__item--invalid" : ""; ?>">
            <label for="lot-name">Наименование</label>
            <input id="lot-name" type="text" name="title" placeholder="Введите наименование лота"
                value="<?=isset($lot['title']) ? htmlspecialchars($lot['title']) : ""; ?>" required>
            <?php if (isset($lot['title'])): ?>
            <span class="form__error"><?=$errors['title']; ?></span>
            <?php endif; ?>
        </div>

        <div class="form__item <?=!empty($errors['category']) ? "form__item--invalid" : ""; ?>">
            <label for="category">Категория</label>
            <select id="category" name="category" required>
                <option selected disabled>Выберите категорию</option>
                <?php foreach ($categories as $category): ?>
                <option value="<?=$category['id']; ?>"
                    <?php echo (intval($lot['category']) === intval($category['id']) && isset($lot['category'])) ? 'selected="true"' : '';  ?>>
                    <?=$category['name']; ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['category'])): ?>
            <span class="form__error"><?=$errors['category']; ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form__item form__item--wide <?=!empty($errors['description']) ? "form__item--invalid" : ""; ?>">
        <label for="message">Описание</label>
        <textarea id="message" name="description" placeholder="Напишите описание лота"
            required><?=isset($lot['description']) ? htmlspecialchars($lot['description']) : ""; ?></textarea>
        <?php if (isset($lot['description'])): ?>
        <span class="form__error"><?=$errors['description']; ?></span>
        <?php endif; ?>
    </div>

    <div class="form__item form__item--file <?=!empty($errors['file']) ? "form__item--invalid" : ""; ?>">

        <label>Изображение</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="img/avatar.jpg" width="113" height="113" alt="Изображение лота">
            </div>
        </div>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="img_url" id="photo2" value="">

            <label for="photo2">
                <span>+ Добавить</span>
            </label>
            <?php if (isset($errors['file'])): ?>
            <span class="form__error"><?=$errors['file']; ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="form__container-three">

        <div class="form__item form__item--small <?=!empty($errors['start_price'])? "form__item--invalid" : ""; ?>">
            <label for="lot-rate">Начальная цена</label>
            <input id="lot-rate" type="number" name="start_price" placeholder="0"
                value="<?=isset($lot['start_price']) ? htmlspecialchars($lot['start_price']) : ""; ?>" required>
            <?php if (isset($lot['start_price'])): ?>
            <span class="form__error"><?=$errors['start_price']; ?></span>
            <?php endif; ?>
        </div>

        <div class="form__item form__item--small <?=!empty($errors['bet_step']) ? "form__item--invalid" : ""; ?>">
            <label for="lot-step">Шаг ставки</label>
            <input id="lot-step" type="number" name="bet_step" placeholder="0"
                value="<?=isset($lot['bet_step']) ? htmlspecialchars($lot['bet_step']) : ""; ?>" required>
            <?php if (isset($lot['bet_step'])): ?>
            <span class="form__error"><?=$errors['bet_step']; ?></span>
            <?php endif; ?>
        </div>

        <div class="form__item <?=!empty($errors['date_expire']) ? "form__item--invalid" : ""; ?>">
            <label for="lot-date">Дата окончания торгов</label>
            <input class="form__input-date" id="lot-date" type="date" name="date_expire"
                value="<?=isset($lot['date_expire']) ? htmlspecialchars($lot['date_expire']) : ""; ?>" required>
            <?php if (isset($lot['date_expire'])): ?>
            <span class="form__error"><?=$errors['date_expire']; ?></span>
            <?php endif; ?>
        </div>
    </div>
    <button type="submit" class="button">Добавить лот</button>
</form>