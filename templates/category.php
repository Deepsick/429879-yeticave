<?php
/**
* @var array $category Информация о категории
* @var array $lots Массив лотов в данной категории
* @var array $pages_count Количество страниц
* @var array $pages Массив всех страниц
* @var string|int $cur_page Текущая страница
*/
?>

<div class="container">
    <section class="lots">
        <h2>Все лоты в категории <span><?=$category['name']; ?></span></h2>
        <?php if (count($lots)): ?>
        <ul class="lots__list">
            <?php foreach ($lots as $lot): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=htmlspecialchars($lot['img_url']); ?>" width="350" height="260" alt="<?=htmlspecialchars($lot['category']); ?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=$category['name']; ?></span>
                    <h3 class="lot__title"><a class="text-link"
                            href="lot.php?id=<?=$lot['id']; ?>"><?=htmlspecialchars($lot['title']); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Текущая цена</span>
                            <span
                                class="lot__cost"><?=format_number($lot['max_price'] ?? $lot['start_price']); ?></span>
                        </div>
                        <div class="lot__timer timer">
                            <?=get_short_time_left($lot['date_expire']);  ?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <h1>Нет лотов в данной категории</h1>
        <?php endif; ?>
    </section>
    <?php if ($pages_count > 1): ?>
    <ul class="pagination-list">
        <? if (intval($cur_page) !== 1): ?>
        <li class="pagination-item pagination-item-prev">
            <a href="category.php?id=<?=$category['id']; ?>&page=<?=intval($cur_page)-1 ;?>">Назад</a>
        </li>
        <?php endif; ?>
        <?php foreach ($pages as $page): ?>
        <li class="pagination-item <?php echo (intval($page) === intval($cur_page)) ? 'pagination-item-active' : '' ?>">
            <a href="category.php?id=<?=$category['id']; ?>&page=<?=$page;?>"><?=$page;?></a>
        </li>
        <?php endforeach; ?>
        <? if (intval($cur_page) !== intval(end($pages))): ?>
        <li class="pagination-item pagination-item-next">
            <a href="category.php?id=<?=$category['id']; ?>&page=<?=intval($cur_page)+1 ;?>">Вперед</a>
        </li>
        <?php endif; ?>
    </ul>
    <?php endif; ?>
</div>