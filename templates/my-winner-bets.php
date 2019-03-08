<?php
/**
* @var array $bet Информация о ставке 
*/
?>
<td class="rates__info">
    <div class="rates__img">
        <img src="<?=$bet['lot_img_url'] ?>" width="54" height="40" alt="<?=$bet['lot_title']; ?>">
    </div>
    <div>
        <h3 class="rates__title"><a href="lot.php?id=<?=$bet['lot_id'] ?>"><?=$bet['lot_title']; ?></a></h3>
        <p>Телефон +7 900 667-84-48, Скайп: Vlas92. Звонить с 14 до 20</p>
    </div>
</td>
<td class="rates__category">
    <?=$bet['category'] ?>
</td>
<td class="rates__timer">
    <div class="timer timer--win">Ставка выиграла</div>
</td>