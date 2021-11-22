<?php namespace app\modules\main\components;

use app\models\Redirect;
use yii\web\Response;

class ErrorHandler extends \yii\web\ErrorHandler
{
    public function handleException($exception)
    {
        if(is_a($exception, 'yii\web\HttpException') && $exception->statusCode === 404) {
            Redirect::handleRedirects();
//            NotFound::log();
        }

        get_response()->on(Response::EVENT_AFTER_PREPARE, function($event) {
            $event->sender->content = modify_output($event->sender->content);
        });

//        $url = get_request()->absoluteUrl;;
//        $newUrl = $url.'.html';
//        if(!strpos($url, '.html') && !is_404($newUrl)) {
//            \redirect($newUrl);
//        }

        parent::handleException($exception);
    }
}
