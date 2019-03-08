<?php
/**
* @var array $_SESSION Данные о сессии пользователя
* @var array $bets Информация о ставке на лот
*/
?>

<section class="rates container">
    <h2>Мои ставки</h2>
    <?php if (count($bets)): ?>
    <table class="rates__list">
        <?php foreach($bets as $bet): ?>
        <tr
            class="rates__item <?php echo (intval($bet['winner_id']) === intval($_SESSION['user']['id'])) ? 'rates__item--win' : ''; ?>">
            <?php if (strtotime($bet['lot_expire']) <= time() && intval($bet['winner_id']) === intval($_SESSION['user']['id'])) {
                                $bet_content = include_template('my-winner-bets.php', ['bet' => $bet]); 
                            } else {
                                $bet_content = include_template('my-other-bets.php', ['bet' => $bet]); 
                            }
                            echo  $bet_content;
                        ?>
            <td class="rates__price">
                <?=format_number($bet['price']); ?>
            </td>
            <td class="rates__time">
                <?=get_format_date($bet['date_create']); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <h1>Вы пока не делали ставок</h1>
    <a href="index.php">Выбрать лот и сделать ставку</a>
    <?php endif; ?>
</section>