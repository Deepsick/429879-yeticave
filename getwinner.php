<?php
require_once 'vendor/autoload.php';
require_once 'session.php';
require_once 'db.php';
require_once 'functions.php';

$transport = new Swift_SmtpTransport("phpdemo.ru", 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");

$mailer = new Swift_Mailer($transport);

$expired_lots = get_expired_lots($connection);
$lots_with_winners = get_winners($connection, $expired_lots);
$lots_with_winners_and_notify_status = insert_winners($connection, $lots_with_winners);


foreach ($lots_with_winners_and_notify_status as $lot) {
    if (isset($lot['winner'])) {
        $message = new Swift_Message();
        $message->setSubject('Ваша ставка победила');
        $message->setFrom(['keks@phpdemo.ru' => 'Интернет-аукцион Yeticave']);
        $message->setTo([$lot['winner']['email'] => $lot['winner']['user_name']]);
    
        $msg_content = include_template(
            'email.php',
            [
                'lot' => $lot
            ]
        );
        $message->setBody($msg_content, 'text/html');
    
        $result = $mailer->send($message);
    }
}
