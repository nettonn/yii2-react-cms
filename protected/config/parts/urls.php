<?php
return [
    '' => 'site/index',
    'files/<_path:.+>'=>'file-storage/thumb/get',
    [
        'class' => 'app\components\AdminClientUrlCreateRules',
        'restControllers' => [
            'posts' => 'admin/post',
            'pages' => 'admin/page',
            'users' => 'admin/user',
            'chunks' => 'admin/chunk',
            'redirects' => 'admin/redirect',
            'settings' => 'admin/setting',
        ]
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'auth' => 'admin/auth',
        ],
        'prefix' => 'admin-api',
        'patterns' => [
            'OPTIONS <action:.+>' => 'options',
            'POST registration' => 'registration',
            'POST email-confirm' => 'email-confirm',
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
        ],
        'prefix' => 'admin-api',
        'extraPatterns' => [
            'GET,HEAD model-options' => 'model-options',
            'GET,HEAD model-defaults' => 'model-defaults',
            'OPTIONS <action:.+>' => 'options',
        ]
    ],
];
