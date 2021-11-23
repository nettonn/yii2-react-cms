<?php namespace app\components;

use Yii;
use yii\base\Component;
use app\models\Seo;

class SeoComponent extends Component
{
    protected $currentUrl;

    public $title;
    public $h1;
    public $description;
    public $keywords;

    public $model;

    public $seoModel;

    public $paginationPage;

    public $canonicalUri;
    public $canonicalUrl;

    public $noindex = false;
    public $noindexGoogle = false;

    public $queryParams = [
        'page',
    ];

    public function init()
    {
        $this->currentUrl = urldecode(preg_replace('~\?.*?$~i', '', Yii::$app->getRequest()->getUrl()));
//        $this->seoModel = $this->loadSeoModel();
    }

    protected function loadSeoModel()
    {
        return Seo::findOne(['url'=>$this->currentUrl]);
    }

    public function getCanonicalUri()
    {
        if($this->canonicalUri === null) {
            $this->canonicalUri = preg_replace('~\?.*?$~i', '', Yii::$app->getRequest()->getUrl());
            $params = [];
            if($this->paginationPage && is_int($this->paginationPage) && $this->paginationPage > 1)
                $params['page'] = $this->paginationPage;

            if($params)
                $this->canonicalUri .= '?'.http_build_query($params);
        }
        return $this->canonicalUri;
    }

    public function getCanonicalUrl()
    {
        if($this->canonicalUrl === null) {
            $request = Yii::$app->getRequest();
            $this->canonicalUrl = $request->hostInfo .  $request->baseUrl . $this->getCanonicalUri();
        }

        return $this->canonicalUrl;
    }

    public function getTitle()
    {
        $title = $this->_getTitleInternal();
        if($this->paginationPage && $this->paginationPage > 1 && $title) {
            $title = 'Стр. '.$this->paginationPage.' - '.$title;
        }
        return $title;
    }

    protected function _getTitleInternal()
    {
        if($this->seoModel === null || !$this->seoModel->title) {
            if(isset($this->model)) {
                if($this->model->seo_title) {
                    return $this->model->seo_title;
                }
                if($this->model->seo_h1) {
                    return $this->model->seo_h1;
                }
                return $this->getDefaultName();
            }
            return $this->title;
        }
        return $this->seoModel->title;
    }

    public function getH1()
    {
        $h1 = $this->_getH1Internal();
        if($this->paginationPage && $this->paginationPage > 1 && $h1) {
            $h1 = $h1 . ' - стр. '.$this->paginationPage;
        }
        return $h1;
    }

    protected function _getH1Internal()
    {
        if($this->seoModel === null || !$this->seoModel->h1) {
            if(isset($this->model)) {
                if($this->model->seo_h1) {
                    return $this->model->seo_h1;
                }
                return $this->getDefaultName();
            }
            return $this->h1;
        }
        return $this->seoModel->h1;
    }

    public function getDescription()
    {
        $description = $this->_getDescriptionInternal();
        if($this->paginationPage && $this->paginationPage > 1 && $description) {
            $description = 'Страница '.$this->paginationPage.' - '.$description;
        }
        return $description;
    }

    protected function _getDescriptionInternal()
    {
        if($this->seoModel === null || !$this->seoModel->description) {
            if(isset($this->model)) {
                return $this->model->seo_description;
            }
            return $this->description;
        }
        return $this->seoModel->description;
    }

    public function getKeywords()
    {
        return $this->_getKeywordsInternal();
    }

    protected function _getKeywordsInternal()
    {
        if($this->seoModel === null || !$this->seoModel->keywords) {
            if(isset($this->model)) {
                return $this->model->seo_keywords;
            }
            return $this->keywords;
        }
        return $this->seoModel->keywords;
    }

    public function setPlaceholders()
    {

    }

    public function getDefaultName()
    {
        if($this->model->hasAttribute('name_lang') && $this->model->name_lang) {
            return $this->model->name_lang;
        }
        return $this->model->name;
    }

}
