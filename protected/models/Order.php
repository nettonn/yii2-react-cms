<?php

namespace app\models;

use app\behaviors\TimestampBehavior;
use app\models\base\ActiveRecord;
use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property string $subject
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $message
 * @property string|null $info
 * @property string|null $url
 * @property string|null $referrer
 * @property string|null $entrance_page
 * @property string|null $ip
 * @property string|null $user_agent
 * @property int $is_deleted
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Order extends ActiveRecord
{
    public $flushCache = false;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject'], 'required'],
            [['message', 'info'], 'string'],
            [['subject', 'name', 'phone', 'email'], 'string', 'max' => 255],
            [['url', 'referrer', 'entrance_page'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject' => 'Тема',
            'name' => 'Имя',
            'phone' => 'Телефон',
            'email' => 'E-Mail',
            'message' => 'Сообщение',
            'info' => 'Информация',
            'url' => 'Url',
            'referrer' => 'Источник',
            'entrance_page' => 'Страница входа',
            'ip' => 'Ip',
            'user_agent' => 'User Agent',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }

    public function init()
    {
        if($this->getIsNewRecord())
        {
            $this->subject = 'Заявка';
        }

        parent::init();
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
        ];
    }
}
