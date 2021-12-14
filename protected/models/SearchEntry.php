<?php

namespace app\models;

use app\models\base\ActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "search_entry".
 *
 * @property int $id
 * @property string $name
 * @property string $link_class
 * @property int $link_id
 * @property string|null $description
 * @property string|null $content
 * @property int|null $value
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class SearchEntry extends ActiveRecord
{
    public $flushCache = false;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%search_entry}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'link_class', 'link_id'], 'required'],
            [['link_id', 'value'], 'integer'],
            [['content'], 'string'],
            [['name', 'description'], 'string', 'max' => 255],
            [['link_class'], 'string', 'max' => 128],
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
            'link_class' => 'Link Class',
            'link_id' => 'Link ID',
            'description' => 'Description',
            'content' => 'Content',
            'value' => 'Value',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
        ];
    }
}
