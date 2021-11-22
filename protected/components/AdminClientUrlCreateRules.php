<?php namespace app\components;

use yii\base\BaseObject;
use yii\web\UrlRuleInterface;

class AdminClientUrlCreateRules extends BaseObject implements UrlRuleInterface
{
    /**
     * [
     *      'posts' => 'admin/post',
     *      'pages' => 'admin/page',
     *      'users' => 'admin/user',
     *      'chunks' => 'admin/chunk',
     *      'redirects' => 'admin/redirect',
     *      'settings' => 'admin/setting',
     * ]
     *
     * @var array
     */
    public $restControllers = [];

    public $patterns = [];

    public $prefix = 'admin';

    public function createUrl($manager, $route, $params)
    {
        $prefix = ltrim($this->prefix, '/');
        foreach($this->restControllers as $path => $controller) {
            if($route === $controller.'/index' || $route === $controller)
                return $prefix.'/'.$path;

            if($route === $controller.'/create')
                return $prefix.'/'.$path.'/create';

            if($route === $controller.'/update' && isset($params['id']) && $params['id'])
                return $prefix.'/'.$path.'/'.$params['id'];
        }

        foreach($this->patterns as $path => $controllerAction) {
            if($route === $controllerAction)
                return $prefix.'/'.$path;
        }

        return false;
    }

    public function parseRequest($manager, $request)
    {
        return false;
    }
}
