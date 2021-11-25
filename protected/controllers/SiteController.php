<?php namespace app\controllers;

use app\controllers\base\FrontController;
use app\models\Page;
use Yii;
use yii\web\HttpException;

class SiteController extends FrontController
{
    public function actionIndex()
    {
        return $this->actionPage();
    }

    public function actionPage($path = null)
    {
        if($path === null) {
            $model = Page::find()->active()->where(['id'=>setting_get('main_page_id')])->one();
        } else {
            $model = Page::find()->active()->where(['path'=>$path])->one();
        }

        if($model === null)
            throw new HttpException(404, 'Страница не найдена');

        Yii::$app->admin->setAdminLink(url(['/admin/page/update', 'id' => $model->id]));

        $this->setLayout($model->layout);

        return $this->render('page', [
            'model'=>$model,
        ]);
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
