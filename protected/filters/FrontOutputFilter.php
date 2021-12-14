<?php namespace app\filters;

use Yii;
use yii\base\ActionFilter;
use yii\helpers\Url;

class FrontOutputFilter extends ActionFilter
{
    public $enabled = true;

    public $arrayResponseContentParams = [
        'html',
        'content',
        'text',
    ];

    /**
     * {@inheritdoc}
     */
    public function afterAction($action, $result)
    {
        if($this->enabled) {
            $result = $this->modifyOutput($result);
        }

        return parent::afterAction($action, $result);
    }

    public function modifyOutput($content)
    {
        if(is_array($content) && $this->arrayResponseContentParams) {
            foreach ($this->arrayResponseContentParams as $param) {
                if(!isset($content[$param]) || !is_string($content[$param]))
                    continue;
                $content[$param] = $this->modifyOutput($content[$param]);
            }
        } elseif(is_string($content)) {
            if(!Yii::$app->request->isAjax && Yii::$app->admin->isAdminEdit())
                $content = $this->addAdminButton($content);
            $content = $this->replacePlaceholders($content);
            $content = $this->replaceLazyImages($content);
        }

        return $content;
    }

    protected function addAdminButton($content)
    {
        if(!Yii::$app->response->isSuccessful )
            return $content;

        if($link = Yii::$app->admin->getAdminLink()) {
            $title = 'Редактировать';
        }
        else {
            $link = Url::to(['/admin/seo/create',
                'url'=> Yii::$app->seo->getCanonicalUri(),
                'name'=>Yii::$app->seo->getH1()]);
            $title = 'Добавить SEO';
        }
        $editButton = '<a href="'.$link.'" style="position: fixed; top: 0; left: 0; display: block; z-index: 9999; color: #fff; background: #444">'.$title.'</a>';
        return preg_replace('~(<body[^>]*?>)~ui', '$1'.$editButton, $content);
    }

    protected function replacePlaceholders($content)
    {
        return Yii::$app->placeholders->replaceAll($content);
    }

    protected function replaceLazyImages($content)
    {
        if(false === stripos($content, 'lazy-image-replace'))
            return $content;

        $images = [];
        preg_match_all("~<img[^>]*lazy-image-replace[^>]*>~i", $content, $images, PREG_PATTERN_ORDER);
        if(isset($images[0])) {
            foreach($images[0] as $image) {
                $newImage = preg_replace('~src=["\'](.*?)["\']~ui', 'src="/media/img/4x3-00000000.png" data-src="$1"', $image);
                $content = str_replace($image, $newImage, $content);
            }
        }
        return $content;
    }
}
