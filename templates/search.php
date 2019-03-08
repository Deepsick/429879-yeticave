<?php
/**
* @var string $search Поисковый запрос
* @var array $lots лоты
* @var number $pages_count Количество страниц
* @var array $pages Массив страниц
*/
?>

<div class="container">
    <section class="lots">
        <h2>Результаты поиска по запросу «<span><?=$search; ?></span>»</h2>
        <ul class="lots__list">
            <?php if (count($lots)): ?>
            <?php foreach($lots as $lot): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=$lot['img_url'] ?>" width="350" height="260" alt="<?=$lot['title']; ?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=$lot['category']; ?></span>
                    <h3 class="lot__title"><a class="text-link"
                            href="lot.php?id=<?=$lot['id']; ?>"><?=$lot['title']; ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span
                                class="lot__amount"><?=$lot['bets_amount'] . ' ' . nounEnding(strval($lot['bets_amount']), ['ставка', 'ставки', 'ставок']); ?></span>
                            <span
                                class="lot__cost"><?=htmlspecialchars(format_number($lot['max_price'] ?? $lot['start_price'])); ?>
                            </span>
                        </div>
                        <div class="lot__timer timer">
                            <?=get_short_time_left($lot['date_expire']);  ?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
            <?php else: ?>
            <li>Ничего не найдено по вашему запросу</li>
            <?php endif; ?>
        </ul>
    </section>
    <?php if ($pages_count > 1): ?>
    <ul class="pagination-list">
        <? if (intval($cur_page) !== 1): ?>
        <li class="pagination-item pagination-item-prev">
            <a href="search.php?search=<?=$search; ?>&find=Найти&page=<?=intval($cur_page)-1; ?>">Назад</a>
        </li>
        <? endif; ?>
        <?php foreach ($pages as $page): ?>
        <li class="pagination-item <?php echo (intval($page) === intval($cur_page)) ? 'pagination-item-active' : '' ?>">
            <a href="search.php?search=<?=$search; ?>&find=Найти&page=<?=$page;?>"><?=$page;?></a>
        </li>
        <?php endforeach; ?>
        <? if (intval($cur_page) !== intval(end($pages))): ?>
        <li class="pagination-item pagination-item-next">
            <a href="search.php?search=<?=$search; ?>&find=Найти&page=<?=intval($cur_page)+1; ?>">Вперед</a>
        </li>
        <? endif; ?>
    </ul>
    <?php endif; ?>
</div>