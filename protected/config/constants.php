<?php

$envars = require(__DIR__.'/envars.php');

date_default_timezone_set('Europe/Moscow');

setlocale(LC_ALL, 'ru_RU');

mb_internal_encoding('UTF-8');

defined('DEV_IPS') or define('DEV_IPS', $envars['DEV_IPS']);

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

defined('APP_ROOT') || define('APP_ROOT', realpath(__DIR__).'/..');

if(defined('CONSOLE_APP') && CONSOLE_APP) {
    define('DEV', true);
    defined('DOCROOT') || define('DOCROOT', realpath(__DIR__ . '/../../public_html'));
    defined('DOMAIN') || define('DOMAIN', $envars['SITE_HOST']);
    defined('HOST') || define('HOST', $envars['SITE_HOST']);
    defined('HOST_INFO') || define('HOST_INFO', $envars['SITE_IS_SECURE']? 'https://'.HOST : 'http://'.HOST);
    defined('IS_SECURE') || define('IS_SECURE', $envars['SITE_IS_SECURE']);
} else {
    defined('CONSOLE_APP') || define('CONSOLE_APP', false);
    define('DEV', in_array($_SERVER['REMOTE_ADDR'], @array_map('trim', explode(',', DEV_IPS))));
    defined('DOCROOT') || define('DOCROOT', $_SERVER['DOCUMENT_ROOT']);
    defined('DOMAIN') || define('DOMAIN', $_SERVER['SERVER_NAME']);
    defined('HOST') || define('HOST', DOMAIN . ($_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : ''));
    defined('IS_ADMIN_ROUTE') || define('IS_ADMIN_ROUTE',
        stripos($_SERVER['REQUEST_URI'], '/admin') === 0
        || stripos($_SERVER['REQUEST_URI'], '/gii') === 0
        || stripos($_SERVER['REQUEST_URI'], '/debug') === 0
    );
    defined('HOST_INFO') || define('HOST_INFO',
        (
            (
                !empty($_SERVER['HTTPS'])
                and $_SERVER['HTTPS'] != 'off'
            )
            ||
            $_SERVER['SERVER_PORT'] == 443
        )
        == true ? 'https://' . HOST : 'http://' . HOST);
    defined('IS_SECURE') || define('IS_SECURE',
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || $_SERVER['SERVER_PORT'] == 443
    );
}
