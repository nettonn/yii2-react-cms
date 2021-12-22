<?php namespace app\components;

use nettonn\yii2filestorage\Module;
use Yii;
use yii\base\Component;
use yii\helpers\FileHelper;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;
use yii\helpers\Inflector;

class FileUploadComponent extends Component
{
    public $imagesExt = [
        'jpg', 'jpeg', 'bmp', 'gif', 'png'
    ];

    public $restrictExt = [
        'exe', 'bat', 'dmg',
    ];

    public $fileMaxSize = 52428800;

    public $uploadPath = '@webroot/temp/fileupload';

    public $maxFiles = 12;

    public function init()
    {
        $this->uploadPath = Yii::getAlias($this->uploadPath);
    }

    protected function getCurrentPath($token)
    {
        $path = $this->uploadPath.DIRECTORY_SEPARATOR.$token;
        FileHelper::createDirectory($path);

        return $path;
    }

    protected function getThumbPath($token)
    {
        $path = $this->getCurrentPath($token).DIRECTORY_SEPARATOR.'thumb';
        FileHelper::createDirectory($path);
        return $path;
    }

    public function upload($token, UploadedFile $uploadedFile, $onlyImages = false)
    {
        if(!$uploadedFile)
            throw new BadRequestHttpException('Файл не приложен');

        if($uploadedFile->size > $this->fileMaxSize)
            throw new BadRequestHttpException('Файл слишком большой');

        $token = $this->prepareToken($token);

        $currentPath = $this->getCurrentPath($token);
        $thumbPath = $this->getThumbPath($token);

        $name = Inflector::slug($uploadedFile->baseName);
        $ext = strtolower($uploadedFile->extension);
        $nameExt = $name.'.'.$ext;

        if(in_array($ext, $this->imagesExt)) {
            $isImage = true;

            $filename = $currentPath.DIRECTORY_SEPARATOR.$nameExt;
            $thumbName = $thumbPath.DIRECTORY_SEPARATOR.$nameExt;
            $fileStorage = Yii::$app->getModule('file-storage');
            $fileStorage->generateImage($uploadedFile->tempName, $filename, 1280, 1280);
            $fileStorage->generateImage($uploadedFile->tempName, $thumbName, 100, 100, true);
        } elseif(!$onlyImages) {
            $isImage = false;
            $filename = $currentPath.DIRECTORY_SEPARATOR.$nameExt;
            move_uploaded_file($uploadedFile->tempName, $filename);
        } else {
            throw new BadRequestHttpException('Файл не является изображением');
        }

        $this->deleteOldFiles();

        $webroot = Yii::getAlias('@webroot');

        return [
            'success'=>true,
            'isImage'=>$isImage,
            'name'=>$name,
            'nameExt'=>$nameExt,
            'ext'=>$ext,
            'fileUrl'=>str_replace($webroot, '', $filename),
            'thumbUrl'=>isset($thumbName) ? str_replace($webroot, '', $thumbName) : '',
        ];

    }

    public function getFiles($token, $names = [])
    {
        $token = $this->prepareToken($token);
        $path = $this->getCurrentPath($token);

        $files = FileHelper::findFiles($path, [
            'recursive'=>false,
        ]);
        if($names) {
            foreach($files as $key => $file) {
                $file = basename($file);
                if(!in_array($file, $names))
                    unset($files[$key]);
            }
        }

        if(count($files) > $this->maxFiles) {
            $files = array_slice($files, 0, $this->maxFiles);
        }

        return $files;
    }

    protected function prepareToken($token)
    {
        $token = basename($token);
        $token = preg_replace('~[^\w]~ui', '', $token);

        if(strlen($token) > 20)
            throw new BadRequestHttpException('Неверный токен');

        return $token;
    }

    protected function deleteOldFiles()
    {
        $dir = $this->uploadPath;
        $handler = opendir($dir);
        while($file = readdir($handler)) {
            if ($file == '.' || $file == '..')
                continue;

            $dirname = $dir.DIRECTORY_SEPARATOR.$file;
            if(!is_dir($dirname))
                continue;

            $innerFiles = FileHelper::findFiles($dirname,
                [
                    'recursive'=>false,
                    'filter'=>function($path) {
                        if(is_dir($path))
                            return false;
                        if(filectime($path) < time()-3600*24*7)
                            return false;
                        return true;
                    }
                ]);
            if(!$innerFiles) {
                FileHelper::removeDirectory($dirname);
            }
        }
    }
}
