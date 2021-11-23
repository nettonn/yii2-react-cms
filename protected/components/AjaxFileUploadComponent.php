<?php namespace app\components;

use app\utils\UrlHelper;
use Yii;
use yii\base\Component;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class AjaxFileUploadComponent extends Component
{
    public $imagesExt = [
        'jpg', 'jpeg', 'bmp', 'gif', 'png'
    ];

    public $restrictExt = [
        'exe', 'bat',
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
        $path = $this->uploadPath.DS.$token;
        FileHelper::createDirectory($path);

        return $path;
    }

    protected function getThumbPath($token)
    {
        $path = $this->getCurrentPath($token).DS.'thumb';
        FileHelper::createDirectory($path);
        return $path;
    }

    public function upload($token, UploadedFile $uploadedFile, $onlyImages = false)
    {
        if($uploadedFile->size > $this->fileMaxSize)
            return ['success'=>false];

        $token = basename($token);

        $currentPath = $this->getCurrentPath($token);
        $thumbPath = $this->getThumbPath($token);

        $name = UrlHelper::transliterate($uploadedFile->baseName);
        $ext = strtolower($uploadedFile->extension);
        $nameExt = $name.'.'.$ext;

        if(in_array($ext, $this->imagesExt)) {
            $isImage = true;

            $filename = $currentPath.DS.$nameExt;
            $thumbName = $thumbPath.DS.$nameExt;
            generate_image($uploadedFile->tempName, $filename, 1280, 1280);
            generate_image($uploadedFile->tempName, $thumbName, 100, 100, true);
        } elseif(!$onlyImages) {
            $isImage = false;
            $filename = $currentPath.DS.$nameExt;
            move_uploaded_file($uploadedFile->tempName, $filename);
        } else {
            return ['success'=>false];
        }

        $this->deleteOldFiles();

        return [
            'success'=>true,
            'isImage'=>$isImage,
            'name'=>$name,
            'nameExt'=>$nameExt,
            'ext'=>$ext,
            'fileurl'=>str_replace(DOCROOT, '', $filename),
            'thumburl'=>isset($thumbName) ? str_replace(DOCROOT, '', $thumbName) : '',
        ];

    }

    public function getFiles($token, $names = [])
    {
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

    protected function deleteOldFiles()
    {
        $dir = $this->uploadPath;
        $handler = opendir($dir);
        while($file = readdir($handler)) {
            if ($file == '.' || $file == '..')
                continue;

            $dirname = $dir.DS.$file;
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
