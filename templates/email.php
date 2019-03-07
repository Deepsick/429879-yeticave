<?php
/**
* @var array $winner Информация о победителе
* @var array $lot Информация о лоте
*/
?>
<html>

<head>
    <title>Поздравляем победителя</title>
</head>

<body>
    <h1>Поздравляем с победой</h1>
    <p>Здравствуйте, <?=$winner['user_name']; ?> </p>
    <p>Ваша ставка для лота <a href="http://yeticave.local/lot.php?id=<?=$lot['id']; ?>"><?=$lot['title']; ?></a>
        победила.</p>
    <p>Перейдите по ссылке <a href="http://yeticave.local/my-bets.php">мои ставки</a>,
        чтобы связаться с автором объявления</p>
    <small>Интернет Аукцион "YetiCave"</small>
</body>

</html>