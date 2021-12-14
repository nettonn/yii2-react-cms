<?php namespace app\errors;

class ErrorHandlerConsole extends \yii\console\ErrorHandler
{
    public function handleException($exception)
    {
        if(is_a($exception, 'yii\base\Exception')) {
            if(preg_match('~Has not waited the lock~', $exception->getMessage()))
                return;
            if(preg_match('~Can\'t create a new thread~', $exception->getMessage()))
                return;
        }

        parent::handleException($exception);
    }
}
