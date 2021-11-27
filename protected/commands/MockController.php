<?php namespace app\commands;

use app\models\Post;
use app\models\User;
use app\models\UserMock;
use app\models\Page;
use nettonn\yii2filestorage\models\FileModel;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class MockController extends Controller
{
    public function actionUser()
    {
        $json = file_get_contents('https://my.api.mockaroo.com/users.json?key=91f50010');
        $data = \yii\helpers\Json::decode($json);

        UserMock::deleteAll();

        foreach($data as $row) {
            $userMock = new UserMock();
            $userMock->attributes = $row;
            $userMock->save();
        }

        echo 'Imported: '.UserMock::find()->count() . "\n";

        return ExitCode::OK;
    }

    public function actionPost()
    {
        $json = file_get_contents('https://my.api.mockaroo.com/posts.json?key=91f50010');
        $data = \yii\helpers\Json::decode($json);

        foreach(Post::find()->all() as $post) {
            $post->delete();
        }

//        foreach(FileModel::find()->all() as $fileModel) {
//            $fileModel->delete();
//        }

        $images = [];
        if(is_dir(Yii::getAlias('@app/temp/photos'))) {
            foreach(FileHelper::findFiles(Yii::getAlias('@app/temp/photos')) as $filename) {
                $image = new UploadedFile();
                $image->name = basename($filename);
                $image->tempName = $filename;
                $image->type = FileHelper::getMimeType($filename);
                $image->size = filesize($filename);
                $images[] = $image;
            }

        }

        $userIds = User::find()->select('id')->column();

        foreach($data as $row) {
            $model = new Post();
            $model->detachBehavior('TimestampBehavior');
            $model->attributes = $row;
            $model->user_id = $userIds[array_rand($userIds)];
            $model->created_at = $row['created_at'] / 1000;
            $model->updated_at = $row['updated_at'] / 1000;

//            $imageKey = array_rand($images);
//            $fileModel = new FileModel();
//            $fileModel->file = $images[$imageKey];
//            $fileModel->save();
//
//            $model->picture_id = $fileModel->id;

            $images_id = [];
            if($images) {
                foreach((array) array_rand($images, rand(1, count($images))) as $imageKey) {
                    $fileModel = new FileModel();
                    $fileModel->file = $images[$imageKey];
                    $fileModel->save();

                    $images_id[] = $fileModel->id;
                }
                $model->images_id = $images_id;
            }


            $model->save();
        }

        echo 'Imported: '.Post::find()->count() . "\n";

        return ExitCode::OK;
    }

    public function actionPage()
    {
        $json = file_get_contents('https://my.api.mockaroo.com/pages.json?key=91f50010');
        $data = \yii\helpers\Json::decode($json);

        foreach(Page::find()->all() as $post) {
            $post->delete();
        }

//        foreach(FileModel::find()->all() as $fileModel) {
//            $fileModel->delete();
//        }

//        $images = [];
//        foreach(FileHelper::findFiles(path_alias('@app/temp/photos')) as $filename) {
//            $image = new UploadedFile();
//            $image->name = basename($filename);
//            $image->tempName = $filename;
//            $image->type = FileHelper::getMimeType($filename);
//            $image->size = filesize($filename);
//            $images[] = $image;
//        }

        echo 'Data count: '.count($data).PHP_EOL;

        $parents = [];
        foreach($data as $row) {
            $model = new Page();
            $model->detachBehavior('TimestampBehavior');
            $model->attributes = $row;
            $model->created_at = $row['created_at'] / 1000;
            $model->updated_at = $row['updated_at'] / 1000;

            while(!empty($parents)) {
                $lastParent = end($parents);
                if(mt_rand(0, 1)) {
                    $model->parent_id = $lastParent;
                    break;
                } else {
                    array_pop($parents);
                }
            }


//            $images_id = [];
//            foreach((array) array_rand($images, rand(1, count($images))) as $imageKey) {
//                $fileModel = new FileModel();
//                $fileModel->file = $images[$imageKey];
//                $fileModel->save();
//
//                $images_id[] = $fileModel->id;
//            }
//            $model->images_id = $images_id;

            if($model->save()) {
                $parents[] = $model->id;
            } else {
                echo dd_str($model->getFirstErrors());
            }
        }

        echo 'Imported: '.Page::find()->count() . "\n";

        return ExitCode::OK;
    }
}
