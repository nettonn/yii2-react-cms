<?php namespace app\controllers;

use app\controllers\base\FrontController;
use app\models\Page;
use app\models\Post;
use app\models\PostSection;
use app\models\query\ActiveQuery;
use Yii;
use yii\helpers\Url;
use yii\web\HttpException;

class SiteController extends FrontController
{
    public function actionIndex()
    {
        return $this->actionPage();
    }

    public function actionPage($path = null)
    {
        if(null === $path) {
            $mainPageId = Yii::$app->settings->get('main_page_id');
            $page = $this->findModel(Page::find()->where(['id'=>$mainPageId]));
        } else {
            $page = $this->findModel(Page::find()->where(['path'=>$path]));
        }

        Yii::$app->admin->setAdminLink($page->getAdminUpdateUrl());

        $this->setLayout($page->getLayout());

        return $this->render('page', [
            'page'=>$page,
        ]);
    }

    public function actionPostSection($alias)
    {
        $postSection = $this->findModel(PostSection::find()->where(['alias' => $alias]));

        Yii::$app->admin->setAdminLink($postSection->getAdminUpdateUrl());

        $this->setLayout($postSection->getLayout());

        return $this->render('post-section', [
            'postSection' => $postSection,
        ]);
    }

    public function actionPost($path)
    {
        $post = $this->findModel(Post::find()->where(['path' => $path]));

        $postSection = $this->findModel($post->getSection());

        Yii::$app->admin->setAdminLink($post->getAdminUpdateUrl());

        $this->setLayout($post->getLayout());

        return $this->render('post', [
            'post' => $post,
            'postSection' => $postSection,
        ]);
    }

    protected function findModel(ActiveQuery $query, $active = true, $cache = true)
    {
        if($active)
            $query = $query->active();

        if($cache)
            $query = $query->cache();

        $model = $query->one();

        if(null === $model)
            throw new HttpException(404, 'Страница не найдена');

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}
