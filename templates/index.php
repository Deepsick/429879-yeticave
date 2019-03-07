<?php
/**
* @var array $categories Массив имен категорий
* @var array $ads Массив лотов
* @var array $bets Массив ставок
*/
?>
<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное
        снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $category): ?>
        <li class="promo__item promo__item--<?=$category['class_name']; ?>">
            <a class="promo__link" href="category.php?id=<?=$category['id']; ?>"><?=$category['name']; ?></a>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <?php foreach($ads as $ad): ?>
        <li class="lots__item lot">
            <div class="lot__image">
                <img src="<?=htmlspecialchars($ad['img_url']); ?>" width="350" height="260" alt="">
            </div>
            <div class="lot__info">
                <span class="lot__category"><?=htmlspecialchars($ad['category']); ?></span>
                <h3 class="lot__title"><a class="text-link"
                        href="lot.php?id=<?=$ad['id']  ?>"><?=htmlspecialchars($ad['title']); ?></a></h3>
                <div class="lot__state">
                    <div class="lot__rate">
                        <span class="lot__amount">Текущая цена</span>
                        <span class="lot__cost"><?=htmlspecialchars(format_number($ad['max_price'] ?? $ad['start_price'])); ?></span>
                    </div>
                    <div class="lot__timer timer">
                        <?=get_short_time_left($ad['date_expire']);  ?>
                    </div>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</section>