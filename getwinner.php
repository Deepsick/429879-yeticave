<?php
require_once 'vendor/autoload.php';
require_once 'session.php';
require_once 'db.php';
require_once 'functions.php';

$transport = new Swift_SmtpTransport("phpdemo.ru", 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");

$mailer = new Swift_Mailer($transport);

$logger = new Swift_Plugins_Loggers_ArrayLogger();
$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

$expired_lots = get_expired_lots($connection);
$winners = get_winners($connection, $expired_lots);
insert_winner($connection, $winners, $expired_lots);

$recipients = [];

foreach ($winners as $winner) {
    $recipients[$winner['email']] = $winner['user_name'];
}

for ($i = 0; $i < count($winners); $i++) {
    $message = new Swift_Message();
    $message->setSubject('Ваша ставка победила');
    $message->setFrom(['keks@phpdemo.ru' => 'Yeticave.local']);
    $message->setTo([$winners[$i]['email'] => $winners[$i]['user_name'], 'mav96@bk.ru' => 'Артем']);

    $msg_content = include_template(
        'email.php',
        [
            'lot' => $expired_lots[$i],
            'winner' => $winners[$i],
        ]
    );
    $message->setBody($msg_content, 'text/html');

    $result = $mailer->send($message);

    if ($result) {
        print("Письмо успешно отправлено");
    } else {
        print("Не удалось отправить письмо: " . $logger->dump());
    }
}
