<?php

$envars = require(__DIR__ . '/../envars.php');

return [
    'class' => 'yii\db\Connection',
//    'dsn' => 'sqlite:' . __DIR__  .'/../sqlite/main.db',
    'dsn' => "mysql:host={$envars['DB_HOST']};dbname={$envars['DB_NAME']}",
    'username' => $envars['DB_USERNAME'],
    'password' => $envars['DB_PASSWORD'],
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    'enableSchemaCache' => !DEV,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
