<?php namespace app\controllers;

use app\controllers\base\FrontAjaxController;
use Yii;
use yii\base\Exception;
use yii\web\UploadedFile;

class SiteAjaxController extends FrontAjaxController
{
    public function actionFileUpload($onlyImages = false)
    {
        sleep(1); // For timeweb

        $uploadedFile = UploadedFile::getInstanceByName('new-files');

        $token = Yii::$app->getRequest()->post('fileupload_token');

        return Yii::$app->fileUpload->upload($token, $uploadedFile, $onlyImages);
    }

    public function actionFileUploadImages()
    {
        return $this->actionFileUpload(true);
    }

    public function actionIndex()
    {
        return [
            'html' => '<p>Html with placeholders {{{rub}}}.</p>',
            'data' => ['some', 'data'],
        ];
    }
}
