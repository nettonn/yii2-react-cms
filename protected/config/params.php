<?php

$envars = require(__DIR__.'/envars.php');

return [
    'transliterateUrl' => true,

    'adminEmail' => $envars['ADMIN_EMAIL'],
    'adminDefaultPassword' => $envars['ADMIN_DEFAULT_PASSWORD'],
    'senderEmail' => $envars['ADMIN_EMAIL'],
    'supportEmail' => $envars['ADMIN_EMAIL'],
    'senderName' => $envars['ADMIN_NAME'],
    'user.passwordResetTokenExpire' => 3600,
    'jwt' => [
        'issuer' => $envars['APP_NAME'],  //name of your project (for information only)
        'audience' => $envars['APP_NAME'],  //description of the audience, eg. the website using the authentication (for info only)
        'id' => $envars['JWT_ID'],  //a unique identifier for the JWT, typically a random string
        'expire' => 300,  //the short-lived JWT token is here set to expire after 5 min.
    ],
    'cronJobs' => require (__DIR__.'/parts/cron.php'),
    'logExceptUrls' => require (__DIR__.'/parts/logExceptUrls.php'),
];
