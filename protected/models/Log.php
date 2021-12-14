<?php

namespace app\models;

use app\models\base\ActiveRecord;
use Yii;

/**
 * This is the model class for table "log".
 *
 * @property int $id
 * @property string $name
 * @property string|null $url
 * @property string|null $messages
 * @property int|null $created_at
 */
class Log extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['messages'], 'string'],
            [['created_at'], 'integer'],
            [['name', 'url'], 'string', 'max' => 255],
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
            'url' => 'Url',
            'messages' => 'Сообщения',
            'created_at' => 'Создано',
        ];
    }
}
