<?php namespace app\behaviors;

use app\components\FileStorageComponent;
use app\models\FileModel;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class FileBehavior extends BaseBehavior
{
    /**
     * Add validators to model [['images_id'], 'integer', 'allowArray' => true]
     *
     * @var array[]
     */
    public $attributes = [
        'images' => [
            'attribute_id' => 'images_id', // default {$attribute}_id
            'multiple' => true, // default true
            'is_image' => true, // default true
            'extensions' => ['jpg', 'jpeg', 'png'], // if image is true will use from component config
        ]
    ];

    protected $attributeIds = [];

    /**
     * default:
     *
     * function ($owner) {
     *     $owner->touch('updated_at');
     * };
     *
     * like yii\behaviors\TimestampBehavior touch method
     * @var string
     */
    public $touchCallback;

    public $deleteOldNotAttachedAfterSave = true;

    public $fileStorageComponent = 'fileStorage';

    protected $_related = [];

    protected $_values = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT  => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_UPDATE  => 'afterSave',
            BaseActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    /**
     * @param BaseActiveRecord $owner
     * @throws InvalidConfigException
     */
    public function attach($owner)
    {
        parent::attach($owner);
    }

    protected $_prepared = false;

    protected function prepare()
    {
        if($this->_prepared)
            return;

        $fileStorage = $this->getFileStorage();

        if(!$this->touchCallback)
            $this->touchCallback = function ($owner) {
                $owner->touch('updated_at');
            };

        $attributes = [];
        foreach ($this->attributes as $attribute => $options) {
            $options['attribute_id'] = $options['attribute_id'] ?? $attribute.'_id';
            $options['multiple'] = $options['multiple'] ?? false;
            $options['is_image'] = $options['is_image'] ?? true;
            if($options['is_image']) {
                $options['extensions'] = $options['extensions'] ?? $fileStorage->imageExt;
            } else {
                $options['extensions'] = $options['extensions'] ?? [];
            }

            $this->attributeIds[$options['attribute_id']] = $attribute;

            $attributes[$attribute] = $options;
        }
        $this->attributes = $attributes;

        $this->_prepared = true;
    }

    protected $_fileStorage;

    /**
     * @return FileStorageComponent
     * @throws InvalidConfigException
     */
    protected function getFileStorage()
    {
        if(null === $this->_fileStorage) {
            $this->_fileStorage = Instance::ensure($this->fileStorageComponent, FileStorageComponent::class);
        }
        return $this->_fileStorage;
    }

    /**
     * @param \yii\db\AfterSaveEvent $event
     */
    public function afterSave($event)
    {
        $this->prepare();

        $needTouch = false;
        foreach($this->attributes as $attribute => $options) {
            if(!isset($this->_values[$attribute]))
                continue;

            $newIds = [];

            if(is_array($this->_values[$attribute])) {
                $newIds = $this->_values[$attribute];
            } else {
                $newIds[] = $this->_values[$attribute];
            }

            if($options['multiple']) {
                $currentIds = $this->getRelation($attribute)->select($this->ownerPkAttribute)->column();
            } else {
                if(count($newIds) >1) {
                    $newIds = array_slice($newIds, 0, 1);
                }

                $currentIds = [$this->getRelation($attribute)->select($this->ownerPkAttribute)->scalar()];
            }

            if($currentIds !== $newIds) {
                $needTouch = true;
            }

            if($deleteIds = array_filter(array_diff($currentIds, $newIds))) {
                foreach(FileModel::find()->where(['in', $this->ownerPkAttribute, $deleteIds])->all() as $model) {
                    $model->delete();
                }
            }

            $extensions = $this->attributes[$attribute]['extensions'];

            $sort = 1;

            if($newIds) {
                $idsString = implode(',', $newIds);
                $query = FileModel::find()
                    ->where(['in', $this->ownerPkAttribute, $newIds])
                    ->orderBy(new Expression("FIELD (`{$this->ownerPkAttribute}`, $idsString)"));
                if($extensions) {
                    $query = $query->andWhere(['in', 'ext', $extensions]);
                }

                foreach($query->all() as $model) {
                    $model->link_class = $this->ownerClass;
                    $model->link_id = $this->owner->{$this->ownerPkAttribute};
                    $model->link_attribute = $attribute;
                    $model->sort = $sort++;
                    if(!$model->save()) {
                        Yii::error('Cant save FileModel with id '.$model->id.' '.VarDumper::dumpAsString($model->getFirstErrors()));
                    }
                }
            }

            unset($this->_related[$attribute]);
            unset($this->_values[$attribute]);
            unset($this->owner->{$attribute});
            if($options['multiple']) {
                $this->owner->populateRelation($attribute, $this->getRelation($attribute)->all());
            } else {
                $this->owner->populateRelation($attribute, $this->getRelation($attribute)->one());
            }
        }

        if($needTouch && is_callable($this->touchCallback)) {
            call_user_func($this->touchCallback, $this->owner);
        }

        if($this->deleteOldNotAttachedAfterSave) {
            $this->getFileStorage()->deleteOldNotAttachedFileModels();
        }
    }

    public function beforeDelete($event)
    {
        $this->prepare();

        $models = FileModel::find()
            ->where(['link_class' => $this->ownerClass, 'link_id' => $this->owner->{$this->ownerPkAttribute}])
            ->all();
        foreach($models as $model) {
            $model->delete();
        }
    }

    /**
     * @param $attribute
     * @return string[]
     * @throws \yii\base\Exception
     */
    public function filesGet($attribute)
    {
        $this->prepare();

        if(!$this->attributes[$attribute]['multiple']) {
            return [$this->fileGet($attribute)];
        }

        $useCache = $this->getFileStorage()->useModelPathCache;
        $filenames = [];
        foreach($this->owner->{$attribute} as $model) {
            $filename = $model->getFilenameWithCheckExists($useCache);
            if(!$filename)
                continue;
            $filenames[] = $filename;
        }

        return $filenames;
    }

    /**
     * @param $attribute
     * @return string|null
     * @throws \yii\base\Exception
     */
    public function fileGet($attribute)
    {
        $this->prepare();

        $model = $this->owner->{$attribute};
        $useCache = $this->getFileStorage()->useModelPathCache;
        if(!$model)
            return null;
        if(is_array($model)) {
            $first = reset($model);
            return $first ? $first->getFilenameWithCheckExists($useCache) : null;
        }
        return $model->getFilenameWithCheckExists($useCache);
    }

    /**
     * return for all files [[['variant1' => 'path/to/variant1'], ['variant2' => 'path/to/variant2']], [...], ...]
     */
    public function filesThumbsGet(string $attribute, array $variants = null, $relative = true)
    {
        $this->prepare();

        $fileStorage = $this->getFileStorage();
        $result = [];
        if(!$variants) {
            $variants = array_keys($fileStorage->variants);
        }
        foreach($this->filesGet($attribute) as $filename) {
            $thumbs = [];
            foreach($variants as $variant) {
                $thumbs[$variant] = $fileStorage->getThumb($filename, $variant, $relative);
            }
            $result[] = $thumbs;
        }
        return $result;
    }

    /**
     * return for all files [['path/to/variant1', 'path/to/variant2'], [...], ...]
     */
    public function filesThumbGet(string $attribute, string $variant = null, $relative = true)
    {
        $this->prepare();

        $fileStorage = $this->getFileStorage();
        $result = [];
        if(!$variant) {
            $variant = $fileStorage->defaultVariant;
        }
        foreach($this->filesGet($attribute) as $filename) {
            $result[] = $fileStorage->getThumb($filename, $variant, $relative);
        }
        return $result;
    }

    /**
     * return for one first file [['variant1' => 'path/to/variant1'], ['variant2' => 'path/to/variant2']]
     */
    public function fileThumbsGet(string $attribute, array $variants = null, $relative = true)
    {
        $this->prepare();

        $filename = $this->fileGet($attribute);
        if(!$filename)
            return null;
        $fileStorage = $this->getFileStorage();
        $thumbs = [];
        foreach($variants as $variant) {
            $thumbs[$variant] = $fileStorage->getThumb($filename, $variant, $relative);
        }
        return $thumbs;
    }

    /**
     * return for one first file 'path/to/variant'
     */
    public function fileThumbGet(string $attribute, array $variant = null, $relative = true)
    {
        $this->prepare();

        $filename = $this->fileGet($attribute);
        if(!$filename)
            return null;
        return $this->getFileStorage()->getThumb($filename, $variant, $relative);
    }

    protected function getRelation($attribute)
    {
        if(!isset($this->attributes[$attribute]))
            return false;

        if(!isset($this->_related[$attribute])) {
            $extensions = $this->attributes[$attribute]['extensions'];

            if($this->attributes[$attribute]['multiple']) {
                $query = $this->owner
                    ->hasMany(FileModel::class, ['link_id' => $this->ownerPkAttribute])
                    ->andWhere(['link_attribute' => $attribute])
                    ->andWhere(['link_class' => $this->ownerClass])
                    ->andWhere(['is_file_exists' => FileModel::FILE_EXISTS_YES])
                    ->orderBy('sort ASC');
                if($extensions) {
                    $query = $query->andWhere(['in', 'ext', $extensions]);
                }
                $this->_related[$attribute] = $query;
            } else {
                $query = $this->owner
                    ->hasOne(FileModel::class, ['link_id' => $this->ownerPkAttribute])
                    ->andWhere(['link_attribute' => $attribute])
                    ->andWhere(['link_class' => $this->ownerClass])
                    ->andWhere(['is_file_exists' => FileModel::FILE_EXISTS_YES])
                    ->orderBy('sort ASC');
                if($extensions) {
                    $query = $query->andWhere(['in', 'ext', $extensions]);
                }
                $this->_related[$attribute] = $query;
            }
        }
        return $this->_related[$attribute];
    }

    public function canGetProperty($name, $checkVars = true)
    {
        $this->prepare();

        if(isset($this->attributes[$name])) {
            return true;
        }

        $attribute = $this->attributeIds[$name] ?? false;
        if($attribute && isset($this->attributes[$attribute]))
            return true;

        return parent::canGetProperty($name, $checkVars);
    }

    public function canSetProperty($name, $checkVars = true)
    {
        $this->prepare();

        $attribute = $this->attributeIds[$name] ?? false;
        if($attribute && isset($this->attributes[$attribute])) {
            return true;
        }

        return parent::canSetProperty($name, $checkVars);
    }

    public function __set($name, $value)
    {
        $this->prepare();

        $attribute = $this->attributeIds[$name] ?? false;
        if($attribute && isset($this->attributes[$attribute])) {
            $this->_values[$attribute] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    public function __get($name)
    {
        $this->prepare();

        $relation = $this->getRelation($name);
        if($relation) {
            return $relation->findFor($name, $this->owner);
        }

        $attribute = $this->attributeIds[$name] ?? false;
        if($attribute && isset($this->attributes[$attribute])) {
            if ($this->attributes[$attribute]['multiple']) {
                $attributeValue = $this->owner->{$attribute};
                return ArrayHelper::getColumn($attributeValue, 'id');
            } else {
                $attributeValue = $this->owner->{$attribute};
                return $attributeValue['id'] ?? null;
            }
        }
        return parent::__get($name);
    }

    public function __call($name, $params)
    {
        $this->prepare();

        if(strlen($name) > 3 && 0 === strpos(strtolower($name), 'get')) {
            $attribute = lcfirst(substr($name, 3));
            $relation = $this->getRelation($attribute);
            if($relation) {
                return $relation;
            }
        }
        parent::__call($name, $params);
    }

    public function hasMethod($name)
    {
        $this->prepare();

        if(strlen($name) > 3 && 0 === strpos(strtolower($name), 'get')) {
            $attribute = lcfirst(substr($name, 3));
            if(isset($this->attributes[$attribute]))
                return true;
        }
        return parent::hasMethod($name);
    }

    public function fileAttachByFilename($attribute,  $fileData, $replace = false)
    {
        $this->prepare();

        if(!isset($this->attributes[$attribute]))
            throw new InvalidArgumentException('No such attribute: '.$attribute);

        $filenames = is_array($fileData) ? $fileData : [$fileData];

        foreach($filenames as $filename) {
            if(!file_exists($filename) || !is_file($filename))
                throw new InvalidArgumentException('File not exists: '.$filename);
        }

        $fileIds = $this->_values[$attribute] ?? [];
        if(!$replace)
            $fileIds = $this->getRelation($attribute)->select($this->ownerPkAttribute)->column();

        foreach($filenames as $filename) {
            $fileModel = FileModel::createByFilename($filename);
            $fileIds[] = $fileModel->id;
        }

        $this->_values[$attribute] = $fileIds;
    }
}
