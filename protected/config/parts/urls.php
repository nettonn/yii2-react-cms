<?php
return [
    [
        'class' => 'app\components\SiteUrlRule',
    ],
    'files/<_path:.+>'=>'file-thumb/get',
    'ajax/<action:\w+>'=> 'site-ajax/<action>',
    [
        'class' => 'app\components\AdminClientUrlCreateRules',
        'restControllers' => [
            'pages' => 'admin/page',
            'users' => 'admin/user',
            'chunks' => 'admin/chunk',
            'redirects' => 'admin/redirect',
            'settings' => 'admin/setting',
            'seo' => 'admin/seo',
            'menu' => 'admin/menu',
            ['path' => 'menu/<menu_id>/items', 'controller' => 'admin/menu-item', 'params' => ['menu_id']],
            'versions' => 'admin/version',
            'logs' => 'admin/log',
            'queues' => 'admin/queue',
            'orders' => 'admin/order',
            'blocks' => 'admin/block',
            ['path' => 'blocks/<block_id>/items', 'controller' => 'admin/block-item', 'params' => ['block_id']],
            'post-sections' => 'admin/post-section',
            ['path' => 'post-sections/<section_id>/posts', 'controller' => 'admin/post', 'params' => ['section_id']],
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
            'blocks' => 'admin/block',
            'block-items' => 'admin/block-item',
            'posts' => 'admin/post',
            'post-sections' => 'admin/post-section',
        ],
        'prefix' => 'admin-api',
        'extraPatterns' => [
            'GET,HEAD model-options' => 'model-options',
            'GET,HEAD model-defaults' => 'model-defaults',
            'OPTIONS <action:.+>' => 'options',
        ]
    ],
];
