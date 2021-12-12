<?php
require (__DIR__ . '/../utils/helpers.php');
$envars = require (__DIR__.'/envars.php');

$config = [
    'id' => 'Yii2 React CMS console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log',],
    'controllerNamespace' => 'app\commands',
    'aliases' => array_merge(require(__DIR__ . '/parts/aliases.php'), [
        '@runnerScript' => '@app/yii'
    ]),
    'modules' => [
        'file-storage' => [
            'class' => 'nettonn\yii2filestorage\Module',
        ]
    ],
    'language'=>'ru-RU',
    'timeZone' => 'Europe/Moscow',
    'controllerMap' => [
        'cron' => [
            'class' => 'denisog\cronjobs\CronController',
            'interpreterPath' => $envars['PHP_INTERPRETER_PATH'],
        ],
        'main' => [
            'class' => 'app\commands\MainController'
        ],
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => null,
            'migrationNamespaces' => [
                'app\migrations',
                'nettonn\yii2filestorage\migrations',
                'yii\queue\db\migrations',
            ],
        ],
    ],
    'components' => [
        'authManager' => require(__DIR__ . '/parts/authManager.php'),
        'formatter' => require(__DIR__ . '/parts/formatter.php'),
        'mailer' => require(__DIR__ . '/parts/mailer.php'),
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'class'=>'app\errors\ErrorHandlerConsole',
        ],
        'chunks' => [
            'class'=>'app\components\ChunkComponent'
        ],
        'settings' => [
            'class'=>'app\components\SettingComponent'
        ],
        'placeholders' => [
            'class'=> 'app\components\PlaceholderComponent',
        ],
        'queue' => require(__DIR__ . '/parts/queue.php'),
        'db' => require (__DIR__ . '/parts/db.php'),
        'urlManager' => array_merge(require(__DIR__.'/parts/urlManager.php'), [
            'baseUrl' => 'https://'.HOST.'/',
        ]),
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => (require __DIR__ . '/params.php'),
];

if (DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
