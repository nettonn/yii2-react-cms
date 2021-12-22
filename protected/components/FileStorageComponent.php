<?php namespace app\components;

use Imagick;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;
use app\models\FileModel;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\imagine\Image;
use Yii;
use yii\helpers\FileHelper;

class FileStorageComponent extends Component
{
    public $originalImageMaxWidth = 1920;
    public $originalImageMaxHeight = 1920;

    public $deleteNotAttachedFileModelsAfter = 3600;

    /**
     * If change exists files may be lost
     * @var string
     */
    protected $_webroot = '@webroot';

    /**
     * If change exists files may be lost
     * @var string public setter getter
     */
    protected $_privateStoragePath = '@app/storage/files';

    /**
     * If change exists files may be lost
     * @var string public setter getter
     */
    protected $_publicStoragePath = '@webroot/files';

    /**
     * If change exists files may be lost
     * @var int
     */
    public $directoryLevel = 1;

    /**
     * If change exists files may be lost
     * @var string
     */
    public $salt = 'kagkjgkjg-asgljkgsadg-sadgklhieutkbn';

    public $imageExt = ['jpg', 'jpeg', 'gif', 'bmp', 'png'];

    /**
     * Variants of generated image thumbs - width, height, quality, watermark, adaptive
     * variant name pattern \w+
     * watermark must be full filename or filename with pathAlias like @webroot/watermark.png
     * @var array
     */
    public $variants = [
        'thumb' => [
            'width' => 400,
            'height' => 300,
            'quality' => 85,
            'adaptive' => true,
        ],
        'normal' => [
            'width' => 1280,
            'height' => 1280,
            'quality' => 80,
        ],
        'original' => [
            'width' => 1920,
            'height' => 1920,
            'quality' => 80,
        ],
    ];

    public $defaultVariant = 'normal';

    public $defaultQuality = 90;

    public $useModelPathCache = true;

    public function init()
    {
        parent::init();

        if(!$this->variants) {
            throw new InvalidConfigException('Please specify variants for image thumbs');
        }

        $variants = $this->variants;
        foreach($variants as $variant => $options) {
            if(!$options['width'] || !$options['height']) {
                throw new InvalidConfigException('Please specify width and height for image variant');
            }

            $variants[$variant] = [
                'width' => $options['width'],
                'height' => $options['height'],
                'quality' => $options['quality'] ?? $this->defaultQuality,
                'adaptive' => $options['adaptive'] ?? false,
                'watermark' => isset($options['watermark']) && $options['watermark'] ? Yii::getAlias($options['watermark']) : null,
            ];
        }

        $this->defaultVariant = isset($variants[$this->defaultVariant])
            ? $this->defaultVariant : current(array_keys($variants));

        $this->variants = $variants;
    }

    public function generateImage($filename, $saveFilename, $toWidth, $toHeight, $adaptive = false, $quality = 80, $watermark = null)
    {
        if($adaptive) {
            $image = Image::thumbnail($filename, $toWidth, $toHeight, ManipulatorInterface::THUMBNAIL_OUTBOUND);
        } else {
            $image = Image::resize($filename, $toWidth, $toHeight);
        }
        if ($watermark) {
            $watermarkObj = Image::getImagine()->open(Yii::getAlias($watermark));
            $iSize = $image->getSize();
            $wSize = $watermarkObj->getSize();
            $image->paste($watermarkObj, new Point(($iSize->getWidth() - $wSize->getWidth())/2, ($iSize->getHeight() - $wSize->getHeight())/2));
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

                $imagick->setInterlaceScheme(Imagick::INTERLACE_JPEG);
                $imagick->setColorspace(Imagick::COLORSPACE_SRGB);
            }
            elseif (in_array($format, array('png'))) {
                $imagick->setimagecompressionquality(75);
                $imagick->setcompressionquality(75);
            }
        }

        return $image->save($saveFilename, ['jpeg_quality' => $quality]);
    }

    public function setWebroot($path)
    {
        $this->_webroot = $path;
        $this->_webrootCached = null;
    }

    private $_webrootCached = null;

    public function getWebroot()
    {
        if(null === $this->_webrootCached)
            $this->_webrootCached = rtrim(Yii::getAlias($this->_webroot), '/');
        return $this->_webrootCached;
    }

    public function setPrivateStoragePath($path)
    {
        $this->_privateStoragePath = $path;
        $this->_privateStoragePathCached = null;
    }

    private $_privateStoragePathCached = null;

    public function getPrivateStoragePath()
    {
        if(null === $this->_privateStoragePathCached)
            $this->_privateStoragePathCached = rtrim(Yii::getAlias($this->_privateStoragePath), '/');
        return $this->_privateStoragePathCached;
    }

    public function setPublicStoragePath($path)
    {
        $this->_publicStoragePath = $path;
        $this->_publicStoragePathCached = null;
    }

    private $_publicStoragePathCached = null;

    public function getPublicStoragePath()
    {
        if(null === $this->_publicStoragePathCached)
            $this->_publicStoragePathCached = rtrim(Yii::getAlias($this->_publicStoragePath), '/');
        return $this->_publicStoragePathCached;
    }

    public function getPrivateToPublicPath($path)
    {
        if(strpos($path, $this->getPrivateStoragePath()) !== 0)
            throw new InvalidConfigException('No such path in rules: '.$path);
        return $this->getPublicStoragePath().str_replace($this->getPrivateStoragePath(), '', $path);
    }

    public function getPublicToPrivatePath($path)
    {
        if(strpos($path, $this->getPublicStoragePath()) !== 0)
            throw new InvalidConfigException('No such path in rules: '.$path);
        return $this->getPrivateStoragePath().str_replace($this->getPublicStoragePath(), '', $path);
    }

    public function removePublicPath($privatePath)
    {
        $thumbPath = $this->getPrivateToPublicPath($privatePath);
        if($thumbPath !== $this->getPublicStoragePath()
            && file_exists($thumbPath)
            && is_dir($thumbPath))
            FileHelper::removeDirectory($thumbPath);
    }

    public function removeThumbs($basename, $privatePath)
    {
        $thumbPath = $this->getPrivateToPublicPath($privatePath);

        if($thumbPath !== $this->getPublicStoragePath()
            && file_exists($thumbPath)
            && is_dir($thumbPath)) {
            $nameWithoutExt = pathinfo($basename, PATHINFO_FILENAME);
            foreach(FileHelper::findFiles($thumbPath) as $one) {
                if(strpos(basename($one), $nameWithoutExt) === 0)
                    FileHelper::unlink($one);
            }
        }
    }

    /**
     * Get new public filename for private filename
     * @param $filename
     * @param string $variant
     * @param true $relative
     * @return string|string[]
     * @throws InvalidConfigException
     */
    public function getThumb($filename, $variant = null, $relative = true)
    {
        if(!file_exists($filename))
            throw new InvalidConfigException('File not exists '.$filename);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if(in_array($ext, $this->imageExt) && !isset($this->variants[$variant]))
            throw new InvalidConfigException('Wrong variant for image: '.$variant);

        $newFilename = $this->getPublicFilename($filename, $variant);
        if($relative) {
            return str_replace($this->getWebroot(), '', $newFilename);
        }

        return $newFilename;
    }

    protected function getPublicFilename($filename, $variant = null)
    {
        $pathParts = pathinfo($filename);
        $path = $pathParts['dirname'];
        $publicPath = $this->getPrivateToPublicPath($path);

        $ext = $pathParts['extension'];
        $name = $pathParts['filename'];

        if ($variant) {
            $hash = $this->generateHash($filename, $variant);

            return $publicPath.DIRECTORY_SEPARATOR.$name.'-'.$variant.'-'.$hash.'.'.$ext;
        }

        return $publicPath.DIRECTORY_SEPARATOR.$name.'.'.$ext;
    }

    protected function generateHash($filename, $variant)
    {
        $id = basename(dirname($filename));

        return substr(md5($id.$this->salt.basename($filename).$this->salt.$variant), 0, 5);
    }

    public function generateFromUrl($url)
    {
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        if(!$ext)
            throw new InvalidConfigException('Invalid url');
        $ext = strtolower($ext);
        if(in_array($ext, $this->imageExt)) {
            return $this->generateImageFromUrl($url);
        }
        return $this->generateFileFromUrl($url);
    }

    protected function generateImageFromUrl($url)
    {
        preg_match('~^(.*)-(\w+)-(\w+)\.(\w+)$~', $url, $m);
        if(!$m) return false;
        list($all, $pathPart, $variant, $hash, $ext) = $m;

        if(!isset($this->variants[$variant])) return false;

        $filePath = $pathPart.'.'.$ext;
        if($hash !== $this->generateHash($filePath, $variant))
            return false;

        $newFilename = $this->getWebroot().$url;
        $fromPath = $this->getPublicToPrivatePath($newFilename);
        $fromPath = pathinfo($fromPath, PATHINFO_DIRNAME);

        $filename = $fromPath.DIRECTORY_SEPARATOR.basename($filePath);

        if(!file_exists($filename))
            return false;

        $newPath = pathinfo($newFilename, PATHINFO_DIRNAME);
        FileHelper::createDirectory($newPath);

        $variantOptions = $this->variants[$variant];

        /**
         * @var number $width
         * @var number $height
         * @var number $quality
         * @var bool $adaptive
         * @var bool|string $watermark
         */
        extract($variantOptions);

        usleep(mt_rand(500, 3000)); // For hosting providers who not allow many generates at once

        $this->generateImage($filename, $newFilename, $width, $height, $adaptive, $quality, $watermark);
        return $newFilename;
    }

    protected function generateFileFromUrl($url)
    {
        preg_match('~^(.*)\.(\w+)$~', $url, $m);
        if(!$m) return false;
        list($all, $pathPart, $ext) = $m;

        $filePath = $pathPart.'.'.$ext;

        $newFilename = $this->getWebroot().$url;
        $fromPath = $this->getPublicToPrivatePath($newFilename);
        $fromPath = pathinfo($fromPath, PATHINFO_DIRNAME);

        $filename = $fromPath.DIRECTORY_SEPARATOR.basename($filePath);

        if(!file_exists($filename))
            return false;

        $newPath = pathinfo($newFilename, PATHINFO_DIRNAME);
        FileHelper::createDirectory($newPath);

        copy($filename, $newFilename);

        return $newFilename;

    }

    public function findOldNotAttachedFileModelsQuery()
    {
        return FileModel::find()
            ->andWhere(['or', ['link_class' => null], ['link_id' => null], ['link_attribute' => null]])
            ->andWhere(['<', 'updated_at', time()-$this->deleteNotAttachedFileModelsAfter]);
    }

    public function deleteOldNotAttachedFileModels()
    {
        foreach($this->findOldNotAttachedFileModelsQuery()->each() as $model) {
            $model->delete();
        }
    }
}
