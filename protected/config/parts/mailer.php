<?php

$envars = require(__DIR__ . '/../envars.php');

return [
    'class' => 'yii\swiftmailer\Mailer',
    'viewPath'=>'@app/views/mail',
    'messageConfig' => [
        'from' => $envars['MAIL_FROM'],
    ],
    'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => $envars['SMTP_HOST'],
        'username' => $envars['SMTP_USERNAME'],
        'password' => $envars['SMTP_PASSWORD'],
        'port' => $envars['SMTP_PORT'],
        'encryption' => $envars['SMTP_ENCRYPTION'],
    ],
];
