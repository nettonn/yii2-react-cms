<?php namespace app\filters;

use yii\base\ActionFilter;

class FrontOutputFilter extends ActionFilter
{
    public $enabled = true;

    public $isAdminEdit = false;

    protected $adminLink;

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

    public function addAdminLink($link)
    {
        $this->adminLink = $link;
    }

    public function modifyOutput($content)
    {
        $content = $this->replaceRouble($content);
        if($this->isAdminEdit)
            $content = $this->addAdminButton($content);
        $content = $this->replacePlaceholders($content);
        $content = $this->replaceLazyImages($content);

        return $content;
    }

    protected function replaceRouble($content)
    {
        return preg_replace('~(#руб#|#rub#)~ui', '₽', $content);
        //    return preg_replace('~(#руб#|#rub#)~ui', '<span class="icon-rouble" title="рублей"><span class="icon-text">рублей</span></span>', $content);
    }

    protected function addAdminButton($content)
    {
        if($link = $this->adminLink) {
            $title = 'Редактировать';
        }
        else {
            $link = url(['/seo/seo-admin/create', 'url'=>get_request()->getUrl(), 'name'=>seo('h1')]);
            $title = 'Добавить SEO';
        }
        $editButton = '<a href="'.$link.'" style="position: fixed; top: 0; left: 0; display: block; z-index: 9999; color: #fff; background: #444">'.$title.'</a>';
        return preg_replace('~(<body[^>]*?>)~ui', '$1'.$editButton, $content);
    }

    protected function replacePlaceholders($content)
    {
        $placeholders = \Yii::$app->placeholders;
        $content = $placeholders->replaceAll($content);
        $content = $placeholders->replaceAll($content);
        $content = $placeholders->remove_empty($content);
        return $content;
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
