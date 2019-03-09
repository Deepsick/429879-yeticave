<?php
/**
* @var array $bet Информация о ставке 
*/
?>
<td class="rates__info">
    <div class="rates__img">
        <img src="<?=htmlspecialchars($bet['lot_img_url']); ?>" width="54" height="40" alt="<?=htmlspecialchars($bet['lot_title']); ?>">
    </div>
    <h3 class="rates__title"><a href="lot.php?id=<?=htmlspecialchars($bet['lot_id']); ?>"><?=htmlspecialchars($bet['lot_title']); ?></a></h3>
</td>
<td class="rates__category">
    <?=htmlspecialchars($bet['category']); ?>
</td>
<td class="rates__timer">
    <div class="timer <?php echo (strtotime($bet['lot_expire']) <=  (time() + 60*60)) ? 'timer--finishing' : ''; ?>" style="width: 150px;">
        <?=(strtotime($bet['lot_expire']) >=  time()) ? get_short_time_left($bet['lot_expire']) : 'Торги окончены'; ?></div>
</td>