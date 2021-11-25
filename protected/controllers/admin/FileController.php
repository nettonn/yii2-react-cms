<?php namespace app\controllers\admin;

use app\controllers\base\BaseApiController;
use nettonn\yii2filestorage\actions\CreateAction;
use nettonn\yii2filestorage\actions\IndexAction;
use Yii;

class FileController extends BaseApiController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::class,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => CreateAction::class,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create-image' => [
                'class' => CreateAction::class,
                'checkAccess' => [$this, 'checkAccess'],
                'onlyImage' => true,
            ],
        ];
    }

    protected function authExcept()
    {
        return [
            'options',
        ];
    }

    public function verbs()
    {
        return [
            'index'  => ['GET', 'HEAD'],
            'create'  => ['POST'],
            'create-image'  => ['POST'],
            'options' => ['OPTIONS'],
        ];
    }

    public function actionOptions($action)
    {
        $verbs = $this->verbs();
        if (Yii::$app->getRequest()->getMethod() !== 'OPTIONS' || !isset($verbs[$action])) {
            Yii::$app->getResponse()->setStatusCode(405);
        }
        $headers = Yii::$app->getResponse()->getHeaders();


        $headers->set('Allow', implode(', ', $verbs[$action]));
        $headers->set('Access-Control-Allow-Methods', implode(', ', $verbs[$action]));
    }
}
