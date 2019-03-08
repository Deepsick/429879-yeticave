<?php
/**
* @var array $bet Информация о ставке 
*/
?>
<td class="rates__info">
    <div class="rates__img">
        <img src="<?=$bet['lot_img_url'] ?>" width="54" height="40" alt="<?=$bet['lot_title']; ?>">
    </div>
    <h3 class="rates__title"><a href="lot.php?id=<?=$bet['lot_id'] ?>"><?=$bet['lot_title']; ?></a></h3>
</td>
<td class="rates__category">
    <?=$bet['category'] ?>
</td>
<td class="rates__timer">
    <div class="timer <?php echo (strtotime($bet['lot_expire']) <=  (time() + 60*60)) ? 'timer--finishing' : ''; ?>">
        <?=get_short_time_left($bet['lot_expire']); ?></div>
</td>