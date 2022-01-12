<?php namespace app\components;

use app\models\Page;
use app\models\Post;
use app\models\PostSection;
use Yii;
use yii\base\BaseObject;
use yii\helpers\StringHelper;
use yii\web\UrlRuleInterface;

class SiteUrlRule extends BaseObject implements UrlRuleInterface
{
    public function createUrl($manager, $route, $params)
    {
        $url = false;

        if($route === 'site/index') {
            return '';
        }

        if($route === 'site/page' && isset($params['path'])) {
            $url = urlencode($params['path']);
            unset($params['path']);
            return $url;
        }

        if($route === 'site/post-section' && isset($params['alias'])) {
            $url = urlencode($params['alias']);
            unset($params['alias']);
            return $url;
        }

        if($route === 'site/post' && isset($params['path'])) {
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

        if(!$pathInfo) {
            return ['site/index', $params];
        }

        $cache = Yii::$app->getCache();

        $pagesKey = 'site.url.pages';
        if(false === $pages = $cache->get($pagesKey)) {
            $pages = Page::find()
                ->select('path')
                ->active()
                ->column();

            $cache->set($pagesKey, $pages);
        }

        if(in_array($pathInfo, $pages)) {
            $params['path'] = $pathInfo;
            return ['site/page', $params];
        }

        $pathInfoParts = StringHelper::explode($pathInfo, '/'); // TODO skip empty?

        $postSectionsKey = 'site.url.post-sections';
        if(false === $postSections = $cache->get($postSectionsKey)) {
            $postSections = PostSection::find()
                ->select('alias')
                ->active()
                ->column();

            $cache->set($postSectionsKey, $postSections);
        }

        if(count($pathInfoParts) === 1) {
            if(in_array($pathInfoParts[0], $postSections)) {
                $params['alias'] = $pathInfoParts[0];
                return ['site/post-section', $params];
            }
        }

        if(count($pathInfoParts) === 2) {
            if(in_array($pathInfoParts[0], $postSections)) {
                $postsKey = 'site.url.posts';
                if(false === $posts = $cache->get($postsKey)) {
                    $posts = Post::find()
                        ->select('path')
                        ->active()
                        ->column();

                    $cache->set($postsKey, $posts);
                }

                if(in_array($pathInfo, $posts)) {
                    $params['path'] = $pathInfo;
                    return ['site/post', $params];
                }
            }
        }

        return false;
    }
}
