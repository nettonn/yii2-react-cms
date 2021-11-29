<?php namespace app\assets;

use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\AssetBundle;

class BaseAsset extends AssetBundle
{
    public $appendTimestamps = [];

    public function publish($am)
    {
        parent::publish($am);

        if (isset($this->basePath, $this->baseUrl) && $this->appendTimestamps) {
            foreach ($this->js as $i => $js) {
                if (is_array($js)) {
                    $file = array_shift($js);
                    if (Url::isRelative($file)) {
                        $js = ArrayHelper::merge($this->jsOptions, $js);
                        array_unshift($js, $this->appendTimestamp($file, $this->basePath));
                        $this->js[$i] = $js;
                    }
                } elseif (Url::isRelative($js)) {
                    $this->js[$i] = $this->appendTimestamp($js, $this->basePath);
                }
            }
            foreach ($this->css as $i => $css) {
                if (is_array($css)) {
                    $file = array_shift($css);
                    if (Url::isRelative($file)) {
                        $css = ArrayHelper::merge($this->cssOptions, $css);
                        array_unshift($css, $this->appendTimestamp($file, $this->basePath));
                        $this->css[$i] = $css;
                    }
                } elseif (Url::isRelative($css)) {
                    $this->css[$i] = $this->appendTimestamp($css, $this->basePath);
                }
            }
        }
    }

    public function appendTimestamp($file, $basePath)
    {
        if(!in_array($file, $this->appendTimestamps))
            return $file;

        $filename = $basePath.DIRECTORY_SEPARATOR.$file;

        $time = filemtime($filename);

        $pathInfo = pathinfo($filename);

        $newFilename = $pathInfo['dirname'].DIRECTORY_SEPARATOR.$pathInfo['filename'].'-t'.$time.'.'.$pathInfo['extension'];

        $oldFiles = FileHelper::findFiles(dirname($filename), [
            'only' => [
                $pathInfo['filename'].'-t*'.$pathInfo['extension']
        ]]);

        foreach($oldFiles as $oldFile) {
            FileHelper::unlink($oldFile);
        }

        if(file_exists($newFilename))
            FileHelper::unlink($newFilename);

        if(!file_exists($newFilename)) {
            copy($filename, $newFilename);
        }

        return str_replace($basePath.DIRECTORY_SEPARATOR, '', $newFilename);
    }
}
