<?php namespace app\models\blocks;

use app\behaviors\FileBehavior;
use app\behaviors\TimestampBehavior;
use app\models\base\ActiveRecord;
use app\models\query\ActiveQuery;
use Yii;
use yii2tech\ar\dynattribute\DynamicAttributeBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "block_item".
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property int $block_id
 * @property string|null $data
 * @property int|null $sort
 * @property int $status
 * @property int $is_deleted
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Block $block
 */
class BlockItem extends ActiveRecord
{
    const TYPE = null;

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
        return '{{%block_item}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['name', 'status'], 'required'],
            [['sort', 'block_id'], 'integer'],
            [['status'], 'boolean'],
            [['name', 'type'], 'string', 'max' => 255],
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
            'block_id' => 'Блок',
            'data' => 'Data',
            'sort' => 'Сортировка',
            'status' => 'Статус',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        foreach($this->getFileAttributes() as $attribute => $params) {
            $fields[] = $params['attribute_id'] ?? $attribute.'_id';
        }

        foreach($this->getDynamicAttributes() as $name => $defaultValue) {
            $fields[] = $name;
        }

        return $fields;
    }

    /**
     * Gets query for [[Block]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlock()
    {
        return $this->hasOne(Block::class, ['id' => 'block_id']);
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

    public function getFileAttributes()
    {
        return [];
    }

    public function getDynamicAttributes()
    {
        return [];
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
            case SliderBlockItem::TYPE:
                return new SliderBlockItem();
            default:
                return new self;
        }
    }

    public static function types()
    {
        return [
            SliderBlockItem::TYPE => SliderBlockItem::class,
        ];
    }

    public static function getTypeLabels()
    {
        return [
            SliderBlockItem::TYPE => 'Слайдер',
        ];
    }

    public static function getTypeClass($type)
    {
        $types = self::types();
        return $types[$type] ?? BlockItem::class;
    }

    public static function getBlockClass()
    {
        return Block::class;
    }
}
