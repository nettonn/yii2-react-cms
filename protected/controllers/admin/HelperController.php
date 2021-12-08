<?php namespace app\controllers\admin;

use app\controllers\base\BaseApiController;
use Yii;
use yii\helpers\Inflector;
use yii\web\BadRequestHttpException;

class HelperController extends BaseApiController
{
    public function actionGenerateAlias($value)
    {
        if(!$value)
            throw new BadRequestHttpException('Value must be set');

        return Inflector::slug($value);
    }

    protected function authExcept(): array
    {
        return [
            'options',
        ];
    }

    public function verbs()
    {
        return [
            'generate-alias'  => ['GET', 'POST'],
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
