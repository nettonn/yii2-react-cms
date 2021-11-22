<?php

return [
    '@webroot' => realpath(__DIR__ . '/../../../public_html'),
    '@web' => '/',
    '@bower' => '@vendor/bower-asset',
    '@npm'   => '@vendor/npm-asset',
    '@tests' => '@app/tests',
    "@nettonn/yii2filestorage" => '@vendor/nettonn/yii2-file-storage/src',
];
