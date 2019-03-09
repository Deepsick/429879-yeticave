<?php
/**
* @var array $winner Информация о победителе
* @var array $lot Информация о лоте
* @var array $_SERVER Информация о сервере
*/
?>

<html>

<head>
    <title>Поздравляем победителя</title>
</head>

<body>
    <h1>Поздравляем с победой</h1>
    <p>Здравствуйте, <?=$winner['user_name']; ?> </p>
    <p>Ваша ставка для лота <a href="http<?php $_SERVER["REQUEST_SCHEME"] === 'https' ? 's' : ''; ?>://<?=$_SERVER['HTTP_HOST']; ?>/lot.php?id=<?=$lot['id']; ?>"><?=htmlspecialchars($lot['title']); ?></a>
        победила.</p>
    <p>Перейдите по ссылке <a href="http://<?=$_SERVER['HTTP_HOST']; ?>/my-bets.php">мои ставки</a>,
        чтобы связаться с автором объявления</p>
    <small>Интернет Аукцион "YetiCave"</small>
</body>

</html>