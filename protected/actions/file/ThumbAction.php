<?php namespace app\actions\file;

use Yii;
use yii\base\Action;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;

class ThumbAction extends Action
{
    public function run()
    {
        try {
            $filename = Yii::$app->fileStorage->generateFromUrl(Yii::$app->getRequest()->getUrl());
        } catch(\Exception $e) {
            sleep(1); // for host providers
            return $this->redirect(Yii::$app->getRequest()->getUrl());
        }

        if(!$filename)
            throw new NotFoundHttpException();

        return Yii::$app->getResponse()->sendFile($filename, null, [
            'mimeType'=> FileHelper::getMimeType($filename),
            'inline'=>true,
        ]);
    }
}
