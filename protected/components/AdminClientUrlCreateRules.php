<?php namespace app\components;

use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
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
     *      ['path' => 'post-sections/<section_id>/posts', 'controller' => 'admin/post', 'params' => ['section_id']],
     * ]
     *
     * @var array
     */
    public $restControllers = [];

    public $patterns = [];

    public $prefix = ADMIN_URL_PREFIX;

    public function createUrl($manager, $route, $params)
    {
        $url = $this->createPath($manager, $route, $params);
        if($url && !empty($params) && ($query = http_build_query($params)) !== '') {
            $url .= '?' . $query;
        }
        return $url;
    }

    protected function createPath($manager, $route, &$params)
    {
        $prefix = trim($this->prefix, '/').'/';
        foreach($this->restControllers as $path => $controller) {
            if(is_array($controller)) {
                $path = $this->createPathFromArray($controller, $params);
                if(!$path)
                    continue;
                $controller = $controller['controller'];
            }
            if($route === $controller.'/index' || $route === $controller) {
                return $prefix.$path;
            }
            if($route === $controller.'/create') {
                return $prefix.$path.'/create';
            }

            if($route === $controller.'/update' && isset($params['id']) && $params['id']) {
                $url = $prefix.$path.'/'.$params['id'];
                unset($params['id']);
                return $url;
            }
        }

        foreach($this->patterns as $path => $controllerAction) {
            if($route === $controllerAction)
                return $prefix.$path;
        }

        return false;
    }

    protected function createPathFromArray($array, &$params)
    {
        if(!isset($array['path']) || !isset($array['controller']))
            return null;

        $path = $array['path'];

        if(isset($array['params']) && is_array($array['params'])) {
            foreach($array['params'] as $pathParam) {
                if(!isset($params[$pathParam]))
                    return null;
                $path = str_replace("<$pathParam>", $params[$pathParam], $path);
                unset($params[$pathParam]);
            }
        }
        return $path;
    }

    public function parseRequest($manager, $request)
    {
        return false;
    }
}
