<?php
/**
* @var array $bet Информация о ставке 
*/
?>
<td class="rates__info">
    <div class="rates__img">
        <img src="<?=htmlspecialchars($bet['lot_img_url']); ?>" width="54" height="40" alt="<?=htmlspecialchars($bet['lot_title']); ?>">
    </div>
    <div>
        <h3 class="rates__title"><a href="lot.php?id=<?=htmlspecialchars($bet['lot_id']); ?>"><?=htmlspecialchars($bet['lot_title']); ?></a></h3>
        <p><?=htmlspecialchars($bet['contacts']); ?></p>
    </div>
</td>
<td class="rates__category">
    <?=htmlspecialchars($bet['category']); ?>
</td>
<td class="rates__timer">
    <div class="timer timer--win">Ставка выиграла</div>
</td>