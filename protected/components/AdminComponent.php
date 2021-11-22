<?php namespace app\components;

use yii\base\Component;

class AdminComponent extends Component
{
    public $adminCookieName = 'admin-edit';

    public function isAdminEdit(): bool
    {
        return boolval(\Yii::$app->getRequest()->getCookies()->getValue($this->adminCookieName));
    }

    public function setIsAdminEdit($value = true)
    {
        \Yii::$app->getResponse()->getCookies()->add(new \yii\web\Cookie([
            'name' => $this->adminCookieName,
            'value' => intval($value),
            'expire' => time() + 3600*24,
            'domain' => '',
            'httpOnly' => true,
            'path' => '/',
        ]));
    }
}
