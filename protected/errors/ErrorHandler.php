<?php namespace app\errors;

use app\models\Redirect;
use Yii;

class ErrorHandler extends \yii\web\ErrorHandler
{
    public function handleException($exception)
    {
        if(is_a($exception, 'yii\web\HttpException')) {
            if($exception->statusCode === 401 && Yii::$app->getLog()->targets) {
                // not log requests with auth header it may be refresh token request
                $authHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');
                if($authHeader) {
                    foreach(Yii::$app->getLog()->targets as $target) {
                        $target->setEnabled(false);
                    }
                }
            }
            if($exception->statusCode === 404) {
                Redirect::handleRedirects();
            }
        }

        parent::handleException($exception);
    }
}
