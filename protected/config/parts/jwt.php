<?php

$envars = require(__DIR__ . '/../envars.php');

return [
    'class' => 'sizeg\jwt\Jwt',
    'key' => $envars['JWT_KEY'],  //  a long random string
    'jwtValidationData' => 'app\components\JwtValidationData',
];
