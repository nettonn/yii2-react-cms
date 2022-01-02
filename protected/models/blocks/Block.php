<?php namespace app\models\blocks;

use app\behaviors\FileBehavior;
use app\behaviors\TimestampBehavior;
use app\models\base\ActiveRecord;
use app\models\query\ActiveQuery;
use Yii;
use yii\helpers\Inflector;
use yii2tech\ar\dynattribute\DynamicAttributeBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "block".
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string $type
 * @property string|null $data
 * @property int $status
 * @property int $is_deleted
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property BlockItem[] $blockItems
 */
class Block extends ActiveRecord
{
    const TYPE = null;

    public $has_items = false;

    const STATUS_ACTIVE = true;
    const STATUS_NOT_ACTIVE = false;

    public $statusOptions = [
        self::STATUS_ACTIVE => 'Активно',
        self::STATUS_NOT_ACTIVE => 'Не активно',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%block}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['name', 'key', 'type', 'status'], 'required'],
            [['status'], 'boolean'],
            [['name', 'key', 'type'], 'string', 'max' => 255],
            [['key'], 'filter', 'filter'=>[Inflector::class, 'slug']],
        ];

        foreach($this->getFileAttributes() as $attribute => $params) {
            $attributeId = $params['attribute_id'] ?? $attribute.'_id';
            $rules[] = [$attributeId, 'integer', 'allowArray' => true];
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'key' => 'Ключ',
            'type' => 'Тип',
            'data' => 'Data',
            'status' => 'Статус',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['has_items'] = function ($model) {
            return $model->has_items;
        };

        foreach($this->getFileAttributes() as $attribute => $params) {
            $fields[] = $params['attribute_id'] ?? $attribute.'_id';
        }

        foreach($this->getDynamicAttributes() as $name => $defaultValue) {
            $fields[] = $name;
        }

        return $fields;
    }


    /**
     * Gets query for [[BlockItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlockItems()
    {
        return $this->hasMany($this->getBlockItemClass(), ['block_id' => 'id']);
    }

    public function getFileAttributes()
    {
        return [];
    }

    public function getDynamicAttributes()
    {
        return [];
    }

    public function init()
    {
        if($this->getIsNewRecord())
        {
            $this->status = self::STATUS_NOT_ACTIVE;
        }
        $this->type = static::TYPE;
        parent::init();
    }

    public static function find(): ActiveQuery
    {
        return new BlockQuery(get_called_class(), ['type' => static::TYPE]);
    }

    public function beforeSave($insert)
    {
        $this->type = static::TYPE;
        return parent::beforeSave($insert);
    }

    public function afterDelete()
    {
        foreach($this->blockItems as $blockItem) {
            $blockItem->softDelete();
        }
        parent::afterDelete();
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = [
            'TimestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
            'SoftDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],
        ];

        if($dynamicAttributes = $this->getDynamicAttributes()) {
            $behaviors['DynamicAttribute'] = [
                'class' => DynamicAttributeBehavior::class,
                'storageAttribute' => 'data', // field to store serialized attributes
                'dynamicAttributeDefaults' => $dynamicAttributes, // default values for the dynamic attributes
            ];
        }

        if($fileAttributes = $this->getFileAttributes()) {
            $behaviors['FileBehavior'] = [
                'class' => FileBehavior::class,
                'attributes' => $fileAttributes,
            ];
        }
        return $behaviors;
    }

    public static function instantiate($row)
    {
        switch ($row['type']) {
            case SliderBlock::TYPE:
                return new SliderBlock();
            case GallerySimpleBlock::TYPE:
                return new GallerySimpleBlock();
            default:
                return new self;
        }
    }

    public static function types()
    {
        return [
            SliderBlock::TYPE => SliderBlock::class,
            GallerySimpleBlock::TYPE => GallerySimpleBlock::class,
        ];
    }

    public static function getTypeLabels()
    {
        return [
            SliderBlock::TYPE => 'Слайдер',
            GallerySimpleBlock::TYPE => 'Простая галерея',
        ];
    }

    public static function getTypeClass($type)
    {
        $types = self::types();
        return $types[$type] ?? Block::class;
    }

    public static function getBlockItemClass()
    {
        return BlockItem::class;
    }
}
