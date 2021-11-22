<?php namespace app\services;

use app\models\User;

class FilterService
{
    public static function corsFilter()
    {
        return [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['http://localhost:3000'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
//                'Access-Control-Request-Method' => ['*'],
//                'Access-Control-Request-Headers' => ['Authorization', 'Content-Type', 'Accept', 'X-Requested-With', 'Origin'],
                'Access-Control-Request-Headers' => ['*'],
//                'Access-Control-Request-Headers' => ['Origin', 'X-Requested-With', 'Content-Type', 'Accept'],
//                'Access-Control-Allow-Headers' => ['*'],
                'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type', 'Accept', 'X-Requested-With', 'Origin'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 3600,
                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page', 'X-pagination-total-count', 'X-pagination-per-page', 'X-pagination-page-count', 'X-model-options-last-modified'],
//                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
            ],
//            'cors' => [
//                // restrict access to
//                'Origin' => ['http://localhost:3000'],
//                // Allow only POST and PUT methods
//                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
//                // Allow only headers 'X-Wsse'
//                'Access-Control-Request-Headers' => ['*'],
//                // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
//                'Access-Control-Allow-Credentials' => true,
//                // Allow OPTIONS caching
//                'Access-Control-Max-Age' => 3600,
//                // Allow the X-Pagination-Current-Page header to be exposed to the browser.
//                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
//            ]
        ];
    }

    public static function authenticator($except)
    {
        return [
            'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
            'except' => $except,
            'auth' => function ($token, $authMethod) {
                $user = User::findOne($token->getClaim('uid'));
                return $user && app()->user->login($user);
            }
        ];
    }
}
