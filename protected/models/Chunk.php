<?php namespace app\models;

use app\behaviors\TimestampBehavior;
use app\models\base\ActiveRecord;
use nettonn\yii2filestorage\behaviors\ContentImagesBehavior;
use nettonn\yii2filestorage\behaviors\FileBehavior;
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

    public $typeOptions = array(
        self::TYPE_TEXT => 'Текст',
        self::TYPE_HTML => 'HTML',
    );

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%chunk}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
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
    public function attributeLabels()
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

    public function fields()
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
    public function behaviors()
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
