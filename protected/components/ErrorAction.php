<?php namespace app\modules\main\components;

class ErrorAction extends \yii\web\ErrorAction
{
    public function init()
    {
        parent::init();
        if($this->getExceptionCode() == 404) {
//            handle_redirects();
        }
    }
}