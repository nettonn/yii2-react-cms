<?php

require __DIR__.'/../protected/config/constants.php';

if(DEV) {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
} else {
    ini_set('display_errors', 'off');
    error_reporting(E_ERROR);
//    die();
}

require __DIR__ . '/../protected/vendor/autoload.php';
require __DIR__ . '/../protected/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../protected/config/web.php';

(new yii\web\Application($config))->run();
