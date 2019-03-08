<?php
/**
* @var array $lot лот
* @var array $bets Массив ставок
* @var string[] $errors Массив ошибок
* @var number $bet_price Цена ставки
* @var bool $is_form_shown Показать форму или нет
*/
?>

<section class="lot-item container">
    <h2><?=$lot['title'] ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?=$lot['img_url'] ?>" width="730" height="548" alt="<?=$lot['category'] ?>">
            </div>
            <p class="lot-item__category">Категория: <span><?=$lot['category']; ?></span></p>
            <p class="lot-item__description"><?=$lot['description']; ?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <div class="lot-item__timer timer" style="width: 100%;">
                    <?=get_time_left($lot['date_expire']);  ?>
                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span
                            class="lot-item__cost"><?=format_number($bets[0]['price'] ?? $lot['start_price']); ?></span>
                    </div>
                    <?php if ($is_form_shown): ?>
                    <div class="lot-item__min-cost">
                        Мин. ставка
                        <span><?=format_number(($bets[0]['price'] ?? $lot['start_price']) + $lot['bet_step']);  ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if ($is_form_shown): ?>
                    <form class="lot-item__form <?=count($errors) ? "form--invalid" : ""; ?>"
                        action="lot.php?id=<?=$lot['id']; ?>" method="post">
                        <p
                            class="lot-item__form-item form__item <?=!empty($errors['bet_price']) ? "form__item--invalid" : ""; ?>">
                            <label for="cost">Ваша ставка</label>
                            <input id="cost" type="number" name="bet_price"
                                placeholder="<?=($bets[0]['price'] ?? $lot['start_price']) + $lot['bet_step'];?>"
                                value="<?=isset($bet_price) ? $bet_price : ""; ?>" required>
                            <?php if (isset($bet_price)): ?>
                                <span class="form__error"><?=$errors['bet_price']; ?></span>
                            <?php endif; ?>
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="history">
                <?php if (count($bets)):?>
                    <h3>История ставок (<span><?=count($bets); ?></span>)</h3>
                    <table class="history__list">
                        <?php foreach($bets as $bet): ?>
                            <tr class="history__item">
                                <td class="history__name"><?=$bet['user_name']; ?></td>
                                <td class="history__price"><?=format_number($bet['price']); ?></td>
                                <td class="history__time"><?=get_format_date($bet['date_create']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <h3>История ставок отсутствует</h3>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>