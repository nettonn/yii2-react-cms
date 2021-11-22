<?php
require (__DIR__ . '/../utils/helpers.php');
$envars = require(__DIR__.'/envars.php');

$config = [
    'id' => 'Yii2 React CMS',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
    ],
    'modules' => [
        'file-storage' => [
            'class' => 'nettonn\yii2filestorage\Module',
        ]
    ],
    'language'=>'ru-RU',
    'timeZone' => 'Europe/Moscow',
    'aliases' => require(__DIR__ . '/parts/aliases.php'),
    'components' => [
        'authManager' => require(__DIR__ . '/parts/authManager.php'),
        'formatter' => require(__DIR__ . '/parts/formatter.php'),
        'request' => [
            'enableCsrfValidation' => false,
            'enableCsrfCookie' => false,
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => $envars['COOKIE_VALIDATION_KEY'],
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
//        'session' => [
//            'class' => 'yii\web\DbSession',
//        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => require(__DIR__ . '/parts/mailer.php'),
        'ajaxFileUpload' => [
            'class'=> 'app\components\AjaxFileUploadComponent',
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
        'microdata' => [
            'class'=> 'app\components\MicrodataComponent'
        ],
        'seo' => [
            'class'=>'app\components\SeoComponent'
        ],
        'admin' => [
            'class' => 'app\components\AdminComponent',
        ],
        'queue' => require(__DIR__ . '/parts/queue.php'),
        'db' => require (__DIR__ . '/parts/db.php'),
        'urlManager' => require(__DIR__.'/parts/urlManager.php'),
        'jwt' => require(__DIR__ . '/parts/jwt.php'),
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'app\components\EmailQueueTarget',
                    'except' => ['yii\web\HttpException:404',],
                    'levels' => ['error', 'warning'],
                    'message' => [
                        'to' => [$envars['ADMIN_EMAIL']],
                        'subject' => DOMAIN . ' - log',
                    ],
                ],
//                [
//                    'class' => 'app\modules\main\components\EmailQueueTarget',
//                    'categories' => ['yii\web\HttpException:404'],
//                    'levels' => ['error', 'warning'],
//                    'message' => [
//                        'to' => [$params['adminEmail']],
//                        'subject' => DOMAIN . ' - 404 log',
//                    ],
//                ],
            ],
        ],

    ],
    'params' => require (__DIR__ . '/params.php'),
];

if (DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
