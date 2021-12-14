<?php namespace app\controllers;

use app\controllers\base\FrontAjaxController;

class SiteAjaxController extends FrontAjaxController
{
    public function actionIndex()
    {
        return [
            'html' => '<p>Html with placeholders {{{rub}}}.</p>',
            'data' => ['some', 'data'],
        ];
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
