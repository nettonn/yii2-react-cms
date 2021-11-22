<?php

namespace app\models;

use app\behaviors\TimestampBehavior;
use app\models\base\ActiveRecord;
use nettonn\yii2filestorage\behaviors\FileBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property string|null $introtext
 * @property string|null $content
 * @property int|null $user_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property bool $status
 */
class Post extends ActiveRecord
{
    public $options;
    public $option;

    const STATUS_NOT_ACTIVE = false;
    const STATUS_ACTIVE = true;

    public $statusOptions = [
        self::STATUS_NOT_ACTIVE => 'Не активно',
        self::STATUS_ACTIVE => 'Активно',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%post}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'alias'], 'required'],
            [['content'], 'string'],
            [['status'], 'boolean'],
            [['name', 'alias', 'introtext'], 'string', 'max' => 255],
//            [['options', 'option'], 'string'],
//            ['images', 'string']
            ['alias', 'filter', 'filter' => 'generate_alias'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'alias' => 'Alias',
            'introtext' => 'Introtext',
            'content' => 'Content',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        if($this->isRelationPopulated('user'))
            $fields[] = 'user';

        if($this->isRelationPopulated('images')) {
            $fields[] = 'images';
            $fields[] = 'images_id';
        }

        if($this->isRelationPopulated('files')) {
            $fields[] = 'files';
            $fields[] = 'files_id';
        }

        if($this->isRelationPopulated('picture')) {
            $fields[] = 'picture';
            $fields[] = 'picture_id';
        }

        return $fields;
    }

    public function extraFields()
    {
        $fields = [];

        return $fields;
    }


    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function behaviors()
    {
        return [
            'TimestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
            'FileBehavior' => [
                'class' => FileBehavior::class,
                'attributes' => [
                    'images' => [
                        'multiple' => true,
                    ],
                    'files' => [
                        'multiple' => true,
                        'image' => false,
                        'extensions' => ['txt', 'pdf'],
                    ],
                    'picture' => [
                        'multiple' => false,
                        'image' => true,
                    ],
                ]
            ],
            'softDeleteBehavior' => [
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
            $this->status = self::STATUS_NOT_ACTIVE;
        }
        parent::init();
    }

    public function beforeSave($insert)
    {
        if (!$this->user_id) {
            $this->user_id = app()->user->id;
        }
        return parent::beforeSave($insert);
    }
}
