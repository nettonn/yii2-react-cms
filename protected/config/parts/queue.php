<?php

return [
    'class' => 'yii\queue\db\Queue',
    'db' => 'db', // DB connection component or its config
    'tableName' => '{{%queue}}', // Table name
    'channel' => 'default', // Queue channel key
    'mutex' => 'yii\mutex\MysqlMutex', // Mutex used to sync queries
    'ttr' => 60, // Max time for job execution
    'attempts' => 99, // Max number of attempts
    'deleteReleased' => false,
];
