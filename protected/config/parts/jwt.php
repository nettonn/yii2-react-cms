<?php

$envars = require(__DIR__ . '/../envars.php');

return [
    'class' => \sizeg\jwt\Jwt::class,
    'key' => $envars['JWT_KEY'],  //  a long random string
    'jwtValidationData' => \app\components\JwtValidationData::class,
];
