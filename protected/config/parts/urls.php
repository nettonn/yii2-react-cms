<?php
return [
    'files/<_path:.+>'=>'file-thumb/get',
    [
        'class' => 'app\components\SiteUrlRule',
    ],
    'ajax/<action:\w+>'=> 'site-ajax/<action>',
    [
        'class' => 'app\components\AdminClientUrlCreateRules',
        'restControllers' => [
            'posts' => 'admin/post',
            'pages' => 'admin/page',
            'users' => 'admin/user',
            'chunks' => 'admin/chunk',
            'redirects' => 'admin/redirect',
            'settings' => 'admin/setting',
            'seo' => 'admin/seo',
            'menu' => 'admin/menu',
            'menu-items' => 'admin/menu-item',
            'versions' => 'admin/version',
            'logs' => 'admin/log',
            'queues' => 'admin/queue',
            'orders' => 'admin/order',
        ]
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'helpers' => 'admin/helper',
        ],
        'prefix' => 'admin-api',
        'patterns' => [
            'GET,POST <action:.+>' => '<action>',
            'OPTIONS <action:.+>' => 'options',
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'auth' => 'admin/auth',
        ],
        'prefix' => 'admin-api',
        'patterns' => [
            'OPTIONS <action:.+>' => 'options',
//            'POST registration' => 'registration',
//            'POST email-confirm' => 'email-confirm',
            'POST login' => 'login',
            'POST,DELETE refresh-token' => 'refresh-token',
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'files' => 'admin/file'
        ],
        'prefix' => 'admin-api',
        'patterns' => [
            'OPTIONS' => 'options',
            'OPTIONS <action:.+>' => 'options',
            'POST create-image' => 'create-image',
            'POST' => 'create',
            'GET,HEAD' => 'index',
         ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'posts' => 'admin/post',
            'pages' => 'admin/page',
            'users' => 'admin/user',
            'chunks' => 'admin/chunk',
            'redirects' => 'admin/redirect',
            'settings' => 'admin/setting',
            'seo' => 'admin/seo',
            'menu' => 'admin/menu',
            'menu-items' => 'admin/menu-item',
            'versions' => 'admin/version',
            'logs' => 'admin/log',
            'queues' => 'admin/queue',
            'orders' => 'admin/order',
        ],
        'prefix' => 'admin-api',
        'extraPatterns' => [
            'GET,HEAD model-options' => 'model-options',
            'GET,HEAD model-defaults' => 'model-defaults',
            'OPTIONS <action:.+>' => 'options',
        ]
    ],
];
