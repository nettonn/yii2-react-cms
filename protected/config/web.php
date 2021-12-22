<?php
require (__DIR__ . '/../utils/helpers.php');
$envars = require(__DIR__.'/envars.php');
$params = require (__DIR__ . '/params.php');

$config = [
    'id' => 'Yii2 React CMS',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
    ],
    'language'=>'ru-RU',
    'timeZone' => 'Europe/Moscow',
    'aliases' => require(__DIR__ . '/parts/aliases.php'),
    'components' => [
        'authManager' => require(__DIR__ . '/parts/authManager.php'),
        'formatter' => require(__DIR__ . '/parts/formatter.php'),
        'assetManager' => [
            'forceCopy' => DEV,
        ],
        'view' => [
            'class'=>'app\components\View',
        ],
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
            'class'=>'app\errors\ErrorHandler',
            'errorAction' => 'site/error',
        ],
        'mailer' => require(__DIR__ . '/parts/mailer.php'),
        'fileStorage' => [
            'class' => 'app\components\FileStorageComponent',
        ],
        'fileUpload' => [
            'class'=> 'app\components\FileUploadComponent',
        ],
        'chunks' => [
            'class'=>'app\components\ChunkComponent'
        ],
        'settings' => [
            'class'=>'app\components\SettingComponent'
        ],
        'placeholders' => [
            'class'=> 'app\components\PlaceholderComponent',
            'placeholders' => [
                'руб' => '₽',
                'rub' => '₽',
            ],
            'widgets' => [
                'chunk' => 'app\widgets\ChunkWidget',
            ],
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
        'search' => [
            'class' => 'app\components\SearchComponent',
            'indexModelClasses' => [
                'app\models\Page',
            ],
        ],
        'queue' => require(__DIR__ . '/parts/queue.php'),
        'db' => require (__DIR__ . '/parts/db.php'),
        'urlManager' => require(__DIR__.'/parts/urlManager.php'),
        'jwt' => require(__DIR__ . '/parts/jwt.php'),
        'log' => [
            'traceLevel' => DEV ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'app\log\DbLogTarget',
//                    'except' => ['yii\web\HttpException:401'],
                    'levels' => ['error', 'warning'],
                    'exceptUrls' => $params['logExceptUrls']
                ],
                [
                    'class' => 'app\log\EmailQueueTarget',
                    'except' => ['yii\web\HttpException:404',],
                    'levels' => ['error', 'warning'],
                    'message' => [
                        'to' => [$envars['ADMIN_EMAIL']],
                        'subject' => DOMAIN . ' - log',
                    ],
                ],
//                [
//                    'class' => 'app\log\EmailQueueTarget',
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
    'params' => $params,
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
        'allowedIPs' => array_merge(['127.0.0.1', '::1'], @array_map('trim', explode(',', $envars['DEV_IPS']))),
    ];
}

return $config;
