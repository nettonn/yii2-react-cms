<?php namespace app\components;

class View extends \yii\web\View
{
    public $breadcrumbs;

    public $isMainPage = false;

    public $showH1 = true;

    public $bodyClass = '';

    public function getParam($name)
    {
        return $this->params[$name] ?? null;
    }

    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }
}
