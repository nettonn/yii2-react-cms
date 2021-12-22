<?php namespace app\behaviors;

use app\models\FileModel;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use yii\validators\Validator;
use yii\web\UploadedFile;

class FileBehavior extends Behavior
{
    /**
     * @var BaseActiveRecord
     */
    public $owner;

    public $attributes = [
        'images' => [
            'attribute_id' => 'images_id', // default {$attribute}_id
            'multiple' => true, // default true
            'is_image' => true, // default true
            'extensions' => ['jpg', 'jpeg', 'png'], // if image is true will use from component config
        ]
    ];


    protected $ownerClass;

    protected $pkAttribute;

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

    /**
     * if false add validators in attached model
     * [['images_id', 'photo_id'], 'each', 'rule' => ['integer']]
     * @var bool
     */
    public $addValidatorsOnAttach = false;

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

        $ownerClass = get_class($this->owner);
        if(!is_subclass_of($ownerClass, ActiveRecord::class)) {
            throw new InvalidConfigException('Attach allowed only for children of ActiveRecord');
        }

        $primaryKey = $ownerClass::primaryKey();

        if(count($primaryKey) > 1) {
            throw new InvalidConfigException('Composite primary keys not allowed');
        }
        $this->ownerClass = $ownerClass;
        $this->pkAttribute = current($primaryKey);

        $fileStorage = Yii::$app->fileStorage;

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

            if($this->addValidatorsOnAttach) {
                $owner->getValidators()->append(Validator::createValidator('safe', $owner, $options['attribute_id']));
            }

            $attributes[$attribute] = $options;
        }
        $this->attributes = $attributes;
    }

    /**
     * @param \yii\db\AfterSaveEvent $event
     */
    public function afterSave($event)
    {
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
                $currentIds = $this->getRelation($attribute)->select($this->pkAttribute)->column();
            } else {
                if(count($newIds) >1) {
                    $newIds = array_slice($newIds, 0, 1);
                }

                $currentIds = [$this->getRelation($attribute)->select($this->pkAttribute)->scalar()];
            }

            if($currentIds !== $newIds) {
                $needTouch = true;
            }

            if($deleteIds = array_filter(array_diff($currentIds, $newIds))) {
                foreach(FileModel::find()->where(['in', $this->pkAttribute, $deleteIds])->all() as $model) {
                    $model->delete();
                }
            }

            $extensions = $this->attributes[$attribute]['extensions'];

            $sort = 1;

            if($newIds) {
                $idsString = implode(',', $newIds);
                $query = FileModel::find()
                    ->where(['in', $this->pkAttribute, $newIds])
                    ->orderBy(new Expression("FIELD (`{$this->pkAttribute}`, $idsString)"));
                if($extensions) {
                    $query = $query->andWhere(['in', 'ext', $extensions]);
                }

                foreach($query->all() as $model) {
                    $model->link_class = $this->ownerClass;
                    $model->link_id = $this->owner->{$this->pkAttribute};
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
            Yii::$app->fileStorage->deleteOldNotAttachedFileModels();
        }
    }

    public function beforeDelete($event)
    {
        $models = FileModel::find()
            ->where(['link_class' => $this->ownerClass, 'link_id' => $this->owner->{$this->pkAttribute}])
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
        if(!$this->attributes[$attribute]['multiple']) {
            return [$this->fileGet($attribute)];
        }

        $useCache = Yii::$app->fileStorage->useModelPathCache;
        $filenames = [];
        foreach($this->owner->{$attribute} as $model) {
            $filenames[] = $model->getFilename($useCache);
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
        $model = $this->owner->{$attribute};
        $useCache = Yii::$app->fileStorage->useModelPathCache;
        if(!$model)
            return null;
        if(is_array($model)) {
            $first = reset($model);
            return $first ? $first->getFilename($useCache) : null;
        }
        return $model->getFilename($useCache);
    }

    /**
     * return for all files [[['variant1' => 'path/to/variant1'], ['variant2' => 'path/to/variant2']], [...], ...]
     */
    public function filesThumbsGet(String $attribute, $variants = false, $relative = true)
    {
        $fileStorage = Yii::$app->fileStorage;
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
    public function filesThumbGet(String $attribute, String $variant = null, $relative = true)
    {
        $fileStorage = Yii::$app->fileStorage;
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
    public function fileThumbsGet(String $attribute, $variants = false, $relative = true)
    {
        $filename = $this->fileGet($attribute);
        if(!$filename)
            return null;
        $fileStorage = Yii::$app->fileStorage;
        $thumbs = [];
        foreach($variants as $variant) {
            $thumbs[$variant] = $fileStorage->getThumb($filename, $variant, $relative);
        }
        return $thumbs;
    }

    /**
     * return for one first file 'path/to/variant'
     */
    public function fileThumbGet(String $attribute, $variant = false, $relative = true)
    {
        $filename = $this->fileGet($attribute);
        if(!$filename)
            return null;
        return Yii::$app->fileStorage->getThumb($filename, $variant, $relative);
    }

    protected function getRelation($attribute)
    {
        if(!isset($this->attributes[$attribute]))
            return false;

        if(!isset($this->_related[$attribute])) {
            $extensions = $this->attributes[$attribute]['extensions'];

            if($this->attributes[$attribute]['multiple']) {
                $query = $this->owner
                    ->hasMany(FileModel::class, ['link_id' => $this->pkAttribute])
                    ->andWhere(['link_attribute' => $attribute])
                    ->andWhere(['link_class' => $this->ownerClass])
                    ->orderBy('sort ASC');
                if($extensions) {
                    $query = $query->andWhere(['in', 'ext', $extensions]);
                }
                $this->_related[$attribute] = $query;
            } else {
                $query = $this->owner
                    ->hasOne(FileModel::class, ['link_id' => $this->pkAttribute])
                    ->andWhere(['link_attribute' => $attribute])
                    ->andWhere(['link_class' => $this->ownerClass])
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
        $attribute = $this->attributeIds[$name] ?? false;
        if($attribute && isset($this->attributes[$attribute])) {
            return true;
        }

        return parent::canSetProperty($name, $checkVars);
    }

    public function __set($name, $value)
    {
        $attribute = $this->attributeIds[$name] ?? false;
        if($attribute && isset($this->attributes[$attribute])) {
            $this->_values[$attribute] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    public function __get($name)
    {
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
        if(strlen($name) > 3 && 0 === strpos(strtolower($name), 'get')) {
            $attribute = lcfirst(substr($name, 3));
            if(isset($this->attributes[$attribute]))
                return true;
        }
        return parent::hasMethod($name);
    }

    public function fileAttachByFilename($attribute,  $fileData, $replace = false)
    {
        if(!isset($this->attributes[$attribute]))
            throw new InvalidArgumentException('No such attribute: '.$attribute);

        $filenames = is_array($fileData) ? $fileData : [$fileData];

        foreach($filenames as $filename) {
            if(!file_exists($filename) || !is_file($filename))
                throw new InvalidArgumentException('File not exists: '.$filename);
        }

        $fileIds = $this->_values[$attribute] ?? [];
        if(!$replace)
            $fileIds = $this->getRelation($attribute)->select($this->pkAttribute)->column();

        foreach($filenames as $filename) {
            $fileModel = FileModel::createByFilename($filename);
            $fileIds[] = $fileModel->id;
        }

        $this->_values[$attribute] = $fileIds;
    }
}
