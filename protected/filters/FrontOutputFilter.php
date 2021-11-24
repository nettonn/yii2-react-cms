<?php namespace app\filters;

use Yii;
use yii\base\ActionFilter;

class FrontOutputFilter extends ActionFilter
{
    public $enabled = true;

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
        if(Yii::$app->admin->isAdminEdit())
            $content = $this->addAdminButton($content);
        $content = $this->replaceInlineWidgets($content);
        $content = $this->replacePlaceholders($content);
        $content = $this->replaceRouble($content);
        $content = $this->replaceLazyImages($content);

        return $content;
    }

    protected function addAdminButton($content)
    {
        if(!Yii::$app->response->isSuccessful)
            return $content;

        if($link = Yii::$app->admin->getAdminLink()) {
            $title = 'Редактировать';
        }
        else {
            $link = url(['/admin/seo/create', 'url'=> Yii::$app->request->getUrl(), 'name'=>seo('h1')]);
            $title = 'Добавить SEO';
        }
        $editButton = '<a href="'.$link.'" style="position: fixed; top: 0; left: 0; display: block; z-index: 9999; color: #fff; background: #444">'.$title.'</a>';
        return preg_replace('~(<body[^>]*?>)~ui', '$1'.$editButton, $content);
    }

    protected function replaceInlineWidgets($content)
    {
        $content = Yii::$app->inlineWidgets->decodeWidgets($content);

        return $content;
    }

    protected function replacePlaceholders($content)
    {
        $placeholders = Yii::$app->placeholders;
        $content = $placeholders->replaceAll($content);
        $content = $placeholders->replaceAll($content);
        $content = $placeholders->remove_empty($content);
        return $content;
    }

    protected function replaceRouble($content)
    {
        return preg_replace('~(#руб#|#rub#)~ui', '₽', $content);
        //    return preg_replace('~(#руб#|#rub#)~ui', '<span class="icon-rouble" title="рублей"><span class="icon-text">рублей</span></span>', $content);
    }

    protected function replaceLazyImages($content)
    {
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
