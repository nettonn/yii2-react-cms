<?php namespace app\errors;

use app\models\Redirect;

class ErrorHandler extends \yii\web\ErrorHandler
{
    public function handleException($exception)
    {
        if(is_a($exception, 'yii\web\HttpException') && $exception->statusCode === 404) {
            Redirect::handleRedirects();
//            NotFound::log();
        }

        parent::handleException($exception);
    }
}
