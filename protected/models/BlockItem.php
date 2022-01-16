<?php namespace app\models;

use app\behaviors\FileBehavior;
use app\behaviors\TimestampBehavior;
use app\models\base\ActiveRecord;
use Yii;
use yii\base\InvalidArgumentException;
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
    const STATUS_ACTIVE = true;
    const STATUS_NOT_ACTIVE = false;

    public $statusOptions = [
        self::STATUS_ACTIVE => 'Активно',
        self::STATUS_NOT_ACTIVE => 'Не активно',
    ];

    protected $adminUrlParams = ['block_id'];

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
        return [
            [['name', 'status'], 'required'],
            [['sort', 'block_id'], 'integer'],
            [['status'], 'boolean'],
            [['name', 'type'], 'string', 'max' => 255],

            ['title', 'string', 'max' => 255],
            ['description', 'string', 'max' => 1000],

            [['image_id'], 'integer', 'allowArray' => true],
        ];
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
            'title' => 'Заголовок',
            'description' => 'Описание',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        if($this->isRelationPopulated('image')) {
            $fields[] = 'image_id';
        }

        foreach($this->getDynamicAttributes() as $name => $value) {
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
        return [
            'TimestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
            'SoftDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],
            'FileBehavior' => [
                'class' => FileBehavior::class,
                'attributes' => [
                    'image' => [
                        'multiple' => false,
                        'is_image' => true,
                    ],
                ],
            ],
            'DynamicAttribute' => [
                'class' => DynamicAttributeBehavior::class,
                'storageAttribute' => 'data', // field to store serialized attributes
                'dynamicAttributeDefaults' => [
                    'title' => '',
                    'description' => '',
                ], // default values for the dynamic attributes
            ],
        ];
    }

}
