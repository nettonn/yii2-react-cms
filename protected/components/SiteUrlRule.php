<?php namespace app\components;

use app\models\Page;
use Yii;
use yii\base\BaseObject;
use yii\web\UrlRuleInterface;

class SiteUrlRule extends BaseObject implements UrlRuleInterface
{
    public function createUrl($manager, $route, $params)
    {
        $url = false;

        if($route === 'site/index') {
            return '';
        }

        if($route === 'site/page'&& isset($params['path'])) {
            $url = urlencode($params['path']);
            unset($params['path']);
            return $url;
        }

        return $url;
    }


    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();

        $params = [];

        if($pathInfo === '') {
            return ['site/index', $params];
        }

        if(false === $pages = Yii::$app->getCache()->get('site.url.pages')) {

            $pages = Page::find()
                ->select('path')
                ->active()
                ->column();

            Yii::$app->getCache()->set('site.url.pages', $pages);
        }

        if(in_array($pathInfo, $pages)) {
            $params['path'] = $pathInfo;
            return ['site/page', $params];
        }

        return false;
    }
}
