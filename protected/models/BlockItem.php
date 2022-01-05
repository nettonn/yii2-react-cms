<?php namespace app\models;

use app\behaviors\FileBehavior;
use app\behaviors\TimestampBehavior;
use app\models\base\ActiveRecord;
use app\traits\ModelType;
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
    use ModelType;

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
            'block_id' => 'Блок',
            'data' => 'Data',
            'sort' => 'Сортировка',
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

        foreach($this->getTypeFileAttributes() as $attribute => $params) {
            $fields[] = $params['attribute_id'] ?? $attribute.'_id';
        }

        foreach($this->getTypeDynamicAttributes() as $name => $defaultValue) {
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

    protected function configureTypes()
    {
        return [
            Block::TYPE_SLIDER => [
                'rules' => [
                    ['title', 'string', 'max' => 255],
                    ['description', 'string', 'max' => 1000],
                ],
                'attributeLabels' => [
                    'title' => 'Заголовок',
                    'description' => 'Описание',
                ],
                'fileAttributes' => [
                    'image' => [
                        'multiple' => false,
                        'is_image' => true,
                    ],
                ],
                'dynamicAttributes' => [
                    'title' => '',
                    'description' => '',
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

    public function beforeSave($insert)
    {
        $this->type = $this->block->type;
        return parent::beforeSave($insert);
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
