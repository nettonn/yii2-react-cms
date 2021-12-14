<?php namespace app\controllers\base;

use app\filters\FrontOutputFilter;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

abstract class FrontController extends Controller
{
    public $layout = '//common';

    public function afterAction($action, $result)
    {
        Yii::$app->seo->setPlaceholders();

        if(!Yii::$app->admin->hasAdminLink() && $seoModel = Yii::$app->seo->seoModel) {
            Yii::$app->admin->setAdminLink(Url::to(['/admin/seo/update', 'id' => $seoModel->id]));
        }

        return parent::afterAction($action, $result);
    }

    public function setLayout($layout = false)
    {
        $layout = $layout ? $layout : 'common';
        $this->layout = '//'.$layout;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'frontOutput' => [
                'class' => FrontOutputFilter::class,
            ],
        ];
    }

}
