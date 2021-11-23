<?php namespace app\controllers;

use app\filters\FrontOutputFilter;
use Yii;
use yii\web\Controller;

class FrontController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'frontOutput' => [
                'class' => FrontOutputFilter::class,
                'isAdminEdit' => Yii::$app->admin->isAdminEdit(),
            ]
        ];
    }

}
