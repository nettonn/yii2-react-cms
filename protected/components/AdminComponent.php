<?php namespace app\components;

use Yii;
use yii\base\Component;
use yii\web\Cookie;

class AdminComponent extends Component
{
    public $adminCookieName = 'admin-edit';

    protected $adminLink;

    public function setAdminLink($link)
    {
        $this->adminLink = $link;
    }

    public function hasAdminLink()
    {
        return !!$this->adminLink;
    }

    public function getAdminLink()
    {
        return $this->adminLink;
    }

    public function isAdminEdit(): bool
    {
        return boolval(Yii::$app->getRequest()->getCookies()->getValue($this->adminCookieName));
    }

    public function setIsAdminEdit($value = true)
    {
        Yii::$app->getResponse()->getCookies()->add(new \yii\web\Cookie([
            'name' => $this->adminCookieName,
            'value' => intval($value),
            'expire' => time() + 3600 * 24,
            'domain' => '',
            'httpOnly' => true,
            'secure' => IS_SECURE,
            'sameSite' => IS_SECURE && !DEV ? Cookie::SAME_SITE_LAX : Cookie::SAME_SITE_NONE,
            'path' => '/',
        ]));
    }
}
