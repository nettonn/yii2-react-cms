<?php namespace app\controllers\base;

use app\filters\FrontOutputFilter;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

abstract class FrontAjaxController extends Controller
{
    public function afterAction($action, $result)
    {
        Yii::$app->seo->setPlaceholders();

        return parent::afterAction($action, $result);
    }

    protected function verbs()
    {
        return [
            '*'  => ['post'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'frontOutput' => [
                'class' => FrontOutputFilter::class,
            ],
        ];
    }

}
