<?php namespace app\components;

class UrlManager extends \yii\web\UrlManager
{
    /**
     * {@inheritdoc}
     */
    public function createUrl($params)
    {
        return $this->fixPathSlashes(parent::createUrl($params));
    }

    protected function fixPathSlashes($url)
    {
        return preg_replace('|%2F|i', '/', $url);
    }
}
