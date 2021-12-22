<?php namespace app\models;

use app\behaviors\ContentImagesBehavior;
use app\behaviors\FileBehavior;
use app\behaviors\TimestampBehavior;
use app\behaviors\VersionBehavior;
use app\models\base\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "chunk".
 *
 * @property integer $id
 * @property string $name
 * @property string $key
 * @property integer $type
 * @property string $content
 * @property integer $is_deleted
 * @property integer $created_at
 * @property integer $updated_at
 */
class Chunk extends ActiveRecord
{
    const TYPE_TEXT = 1;
    const TYPE_HTML = 2;

    public $typeOptions = [
        self::TYPE_TEXT => 'Текст',
        self::TYPE_HTML => 'HTML',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%chunk}}';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'type'], 'required'],
            [['content'], 'string'],
            [['type'], 'integer'],
            [['name', 'key'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'key' => 'Ключ',
            'type' => 'Тип',
            'content' => 'Содержимое',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Чанки';
    }

    public function fields(): array
    {
        $fields = parent::fields();
        $fields['type_text'] = function ($model) {
            return $model->typeOptions[$model->type];
        };
        return $fields;
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
            'ContentImagesBehavior' => [
                'class' => ContentImagesBehavior::class,
                'imagesAttribute' => 'content_images',
                'contentAttributes' => ['content'],
            ],
            'FileBehavior' => [
                'class' => FileBehavior::class,
                'attributes' => [
                    'content_images' => [
                        'multiple' => true,
                    ],
                ]
            ],
            'VersionBehavior' => [
                'class' => VersionBehavior::class,
                'attributes' => [
                    'name', 'key', 'type', 'content',
                ]
            ],
            'SoftDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],
        ];
    }

    public function init()
    {
        if($this->getIsNewRecord())
        {
            $this->type = self::TYPE_TEXT;
        }

        parent::init();
    }

}
