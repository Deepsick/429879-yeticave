<?php
require_once 'session.php';

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit();
}

require_once 'db.php';
require_once 'functions.php';
require_once 'mysql_helper.php';

$categories = get_categories($connection);
$lots = null;
$user_id = intval($_SESSION['user']['id']);
$bets = get_user_bets($connection, $user_id);

$my_bets_page_content = include_template(
    'my-bets.php',
    [
        'bets' => $bets
    ]
);

$my_bets_page = include_template(
    'inner-layout.php',
    [
        'categories' => $categories,
        'page_title' => 'Мои ставки',
        'page_content' => $my_bets_page_content
    ]
);

echo $my_bets_page;
