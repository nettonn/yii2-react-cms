<?php

namespace app\models;

use app\behaviors\TimestampBehavior;
use app\models\base\ActiveRecord;
use Yii;

/**
 * This is the model class for table "user_refresh_token".
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $ip
 * @property string $user_agent
 * @property int $created_at
 * @property int $updated_at
 */
class UserRefreshToken extends ActiveRecord
{
    public $flushCache = false;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_refresh_token}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'token', 'ip', 'user_agent'], 'required'],
            [['user_id'], 'integer'],
            [['token', 'user_agent'], 'string', 'max' => 1000],
            [['ip'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'token' => 'Token',
            'ip' => 'Ip',
            'user_agent' => 'User Agent',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            'TimestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
        ];
    }
}
