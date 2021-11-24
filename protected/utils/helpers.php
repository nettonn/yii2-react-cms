<?php

function app() {
    return Yii::$app;
}

function controller() {
    return Yii::$app->controller;
}

function db() {
    return Yii::$app->getDb();
}

function formatter() {
    return Yii::$app->getFormatter();
}

function db_cmd($sql = false, $params = false) {
    if($sql)
        return Yii::$app->getDb()->createCommand($sql, $params);
    return Yii::$app->getDb()->createCommand();
}

function db_query() {
    return new \yii\db\Query();
}

function cookie($name = null, $value = null, $time = 86400, $domain = '', $path = '/') {
    if ($value === null)
        return Yii::$app->getRequest()->getCookies()->getValue($name);

    Yii::$app->getResponse()->getCookies()->add(new \yii\web\Cookie([
        'name' => $name,
        'value' => $value,
        'expire' => time() + $time,
        'domain' => $domain,
        'path' => $path,
    ]));
}


function dd($var, $depth = 10, $highlight = true) {
    yii\helpers\VarDumper::dump($var, $depth, $highlight);
}

function dd_str($var, $depth = 10, $highlight = false) {
    return yii\helpers\VarDumper::dumpAsString($var, $depth, $highlight);
}

function dd_plain($var) {
    header('Content-Type: text/plain;');
    print_r($var);
}

function path_alias($alias, $path = false) {
    if($path)
        Yii::setAlias($alias, $path);
    else
        return Yii::getAlias($alias);
}

function url($to, $scheme = false) {
    return yii\helpers\Url::to($to, $scheme);
}

function url_current(array $params = [], $scheme = false) {
    return yii\helpers\Url::current($params, $scheme);
}

function url_abs($url) {
    $hostInfo = Yii::$app->getUrlManager()->getHostInfo();
    if (strncmp($url, '//', 2) === 0) {
        $url = substr($hostInfo, 0, strpos($hostInfo, '://')) . ':' . $url;
    } else {
        $url = $hostInfo . $url;
    }
    return $url;
}

function e($var, $doubleEncode = true) {
    return yii\helpers\Html::encode($var, $doubleEncode);
}

function cache($key = false, $value = false, $duration = 0, $dependency = null) {
    if($key === false)
        return Yii::$app->getCache();
    if($value === false)
        return Yii::$app->getCache()->get($key);
    return Yii::$app->getCache()->add($key, $value, $duration, $dependency);
}


function get_request() {
    return Yii::$app->getRequest();
}

function get_response() {
    return Yii::$app->getResponse();
}

function is_ajax() {
    return Yii::$app->getRequest()->getIsAjax();
}

function get_get($name = null, $defaultValue = null) {
    return Yii::$app->getRequest()->get($name, $defaultValue);
}

function get_post($name = null, $defaultValue = null) {
    return Yii::$app->getRequest()->post($name, $defaultValue);
}

function get_param($name) {
    return Yii::$app->params[$name];
}

function prepare_value_text_options($array, $valueParam = 'value', $textParam = 'text'): array {
    $result = [];
    foreach($array as $value => $text) {
        $result[] = [$valueParam => $value, $textParam => $text];
    }
    return $result;
}

function prepare_options_from_models($array, $valueParam = 'id', $titleParam = 'name', $childrenRelation = 'children'): array {
    $result = [];

    foreach($array as $data) {
        $one = [
            'key' => $data[$valueParam],
            'title' => $data[$titleParam],
            'value' => $data[$valueParam],
        ];
        if($data->$childrenRelation) {
            $one['children'] = prepare_options_from_models($data[$childrenRelation], $valueParam, $titleParam, $childrenRelation);
        }
        $result[] = $one;
    }

    return $result;
}

function class_basename($class) {
    $class = is_object($class) ? get_class($class) : $class;

    return basename(str_replace('\\', '/', $class));
}

function generate_alias($str) {
    return \app\utils\UrlHelper::generateAlias($str);
}

function generate_image($filename, $saveFilename, $toWidth, $toHeight, $adaptive = false, $quality = 80, $watermark = false) {
    if($adaptive) {
        $image = \yii\imagine\Image::thumbnail($filename, $toWidth, $toHeight, ManipulatorInterface::THUMBNAIL_OUTBOUND);
    } else {
        $image = \yii\imagine\Image::resize($filename, $toWidth, $toHeight);
    }
    if ($watermark) {
        $watermarkObj = \yii\imagine\Image::getImagine()->open(Yii::getAlias($watermark));
        $iSize = $image->getSize();
        $wSize = $watermarkObj->getSize();
        $image->paste($watermarkObj, new \Imagine\Image\Point(($iSize->getWidth() - $wSize->getWidth())/2, ($iSize->getHeight() - $wSize->getHeight())/2));
    }

    if(class_exists('Imagick', false)) {
        $imagick = $image->getImagick();
        $imagick->stripImage();
        $imagick->setImageCompressionQuality($quality);
        $format = pathinfo($filename, \PATHINFO_EXTENSION);

        if (in_array($format, array('jpeg', 'jpg', 'pjpeg')))
        {
            $imagick->setSamplingFactors(array('2x2', '1x1', '1x1'));
            $profiles = $imagick->getImageProfiles("icc", true);
            $imagick->stripImage();

            if(!empty($profiles)) {
                $imagick->profileImage('icc', $profiles['icc']);
            }

            $imagick->setInterlaceScheme(\Imagick::INTERLACE_JPEG);
            $imagick->setColorspace(\Imagick::COLORSPACE_SRGB);
        }
        elseif (in_array($format, array('png'))) {
            $imagick->setimagecompressionquality(75);
            $imagick->setcompressionquality(75);
        }
    }

    $image->save($saveFilename, ['jpeg_quality' => $quality]);
}

function chunk_get($key) {
    return Yii::$app->chunks->get($key);
}

function setting_get($key) {
    return Yii::$app->settings->get($key);
}

function redirect($url, $statusCode = 301, $checkAjax = true) {
    Yii::$app->getResponse()->redirect($url, $statusCode, $checkAjax)->send();
    die();
}

function seo($type = false) {
    $seo = Yii::$app->seo;

    switch($type) {
        case 'title':
            return trim($seo->getTitle(), '. ');
        case 'h1':
            return trim($seo->getH1(), '. ');
        case 'description':
        case 'desc':
            return $seo->getDescription();
        case 'key':
        case 'keywords':
            return $seo->getKeywords();
        default:
            return $seo;
    }
}

function placeholders($name = null, $value = null) {
    $placeholders = Yii::$app->placeholders;

    if(null !== $value && null !== $name) {
        return $placeholders->set($name, $value);
    }
    if(null !== $name) {
        return $placeholders->get($name);
    }
    return $placeholders;
}

function remove_nbsp($value) {
    return str_ireplace('&nbsp;', ' ', $value);
}

function asset_with_timestamp($filename, $relativePath = true) {
    if(!file_exists($filename))
        return false;

    $fileDir = dirname($filename);
    $dirRelative = str_replace(DOCROOT, '', $fileDir);
    if($fileDir == $dirRelative)
        return false;

    $assetsDir = Yii::$app->assetManager->basePath.$dirRelative;

    if(!is_dir($assetsDir)) {
        \yii\helpers\FileHelper::createDirectory($assetsDir, 0775, true);
    }

    $time = filemtime($filename);

    $pathInfo = pathinfo($filename);

    $newFilename = $assetsDir.DS.$pathInfo['filename'].'-t'.$time.'.'.$pathInfo['extension'];

    if(!file_exists($newFilename)) {
        copy($filename, $newFilename);
    }

    if($relativePath) {
        return str_replace(DOCROOT, '', $newFilename);
    }
    return $newFilename;
}
