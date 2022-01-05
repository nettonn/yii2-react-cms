<?php namespace app\models;

use app\behaviors\FileBehavior;
use app\behaviors\TimestampBehavior;
use app\models\base\ActiveRecord;
use app\traits\ModelType;
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
    use ModelType;

    const TYPE_SLIDER = 'slider';
    const TYPE_SIMPLE_GALLERY = 'simple_gallery';

    public $typeOptions = [
        self::TYPE_SLIDER => 'Слайдер',
        self::TYPE_SIMPLE_GALLERY => 'Простая галерея',
    ];

    public $typeWithItems = [
        self::TYPE_SLIDER,
    ];

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

        return array_merge($rules, $this->getTypeRules());
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $labels = [
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

        return array_merge($labels, $this->getTypeAttributeLabels());
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['has_items'] = function (Block $model) {
            return in_array($model->getCurrentType(), $model->typeWithItems);
        };

        foreach($this->getTypeFileAttributes() as $attribute => $params) {
            $fields[] = $params['attribute_id'] ?? $attribute.'_id';
        }

        foreach($this->getTypeDynamicAttributes() as $name => $defaultValue) {
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
        return $this->hasMany(BlockItem::class, ['block_id' => 'id']);
    }

    protected function configureTypes()
    {
        return [
            self::TYPE_SLIDER => [
                'rules' => [
                    ['title', 'string', 'max' => 255],
                ],
                'attributeLabels' => [
                    'title' => 'Заголовок',
                ],
                'dynamicAttributes' => [
                    'title' => '',
                ],
            ],
            self::TYPE_SIMPLE_GALLERY => [
                'rules' => [
                    ['title', 'string', 'max' => 255],
                ],
                'attributeLabels' => [
                    'title' => 'Заголовок',
                ],
                'fileAttributes' => [
                    'images' => [
                        'multiple' => true,
                        'is_image' => true,
                    ],
                ],
                'dynamicAttributes' => [
                    'title' => '',
                ],
            ],
        ];
    }

    public function init()
    {
        if($this->getIsNewRecord())
        {
            $this->status = self::STATUS_NOT_ACTIVE;
        }
        parent::init();
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

        if($dynamicAttributes = $this->getTypeDynamicAttributes()) {
            $behaviors['DynamicAttribute'] = [
                'class' => DynamicAttributeBehavior::class,
                'storageAttribute' => 'data', // field to store serialized attributes
                'dynamicAttributeDefaults' => $dynamicAttributes, // default values for the dynamic attributes
            ];
        }

        if($fileAttributes = $this->getTypeFileAttributes()) {
            $behaviors['FileBehavior'] = [
                'class' => FileBehavior::class,
                'attributes' => $fileAttributes,
            ];
        }
        return $behaviors;
    }
}
