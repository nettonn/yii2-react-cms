<?php

$envars = require(__DIR__.'/envars.php');

return [
    'transliterateUrl' => true,

    'adminEmail' => $envars['ADMIN_EMAIL'],
    'senderEmail' => $envars['ADMIN_EMAIL'],
    'supportEmail' => $envars['ADMIN_EMAIL'],
    'senderName' => $envars['ADMIN_NAME'],
    'user.passwordResetTokenExpire' => 3600,
    'api_prefix' => '/admin-api',
    'jwt' => [
        'issuer' => 'DL CMS',  //name of your project (for information only)
        'audience' => 'DL CMS',  //description of the audience, eg. the website using the authentication (for info only)
        'id' => $envars['JWT_ID'],  //a unique identifier for the JWT, typically a random string
        'expire' => 300,  //the short-lived JWT token is here set to expire after 5 min.
    ],
];
