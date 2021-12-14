<?php namespace app\actions\rest;

use Yii;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

class DeleteAction extends \yii\rest\DeleteAction
{
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        /** @var ActiveRecord $model */
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }
        $method = $model->hasMethod('softDelete') ? 'softDelete' : 'delete';
        if($model->$method() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}
