<?php namespace app\actions\file;

use app\models\FileModel;
use yii\base\Action;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

class CreateAction extends Action
{
    public $checkAccess;

    public $onlyImage = false;

    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        $model = new FileModel();

        $model->file = UploadedFile::getInstanceByName('file');

        if(!$model->file) {
            throw new BadRequestHttpException('No file to upload');
        }

        if(!$model->validate()) {
            throw new BadRequestHttpException('File is not valid: '.VarDumper::dumpAsString($model->getFirstErrors()));
        }

        if(!$model->save(false)) {
            throw new ServerErrorHttpException('Error saving model');
        }

        if($this->onlyImage) {
            if(!$model->is_image) {
                $model->delete();
                throw new BadRequestHttpException('File is not image');
            }
        }

        return $model;
    }
}
