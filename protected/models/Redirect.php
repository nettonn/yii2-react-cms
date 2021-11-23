<?php

namespace app\models;

use app\behaviors\TimestampBehavior;
use app\models\base\ActiveRecord;
use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "redirect".
 *
 * @property integer $id
 * @property string $from
 * @property string $to
 * @property integer $code
 * @property integer $status
 * @property integer $sort
 * @property integer $is_deleted
 * @property integer $created_at
 * @property integer $updated_at
 */
class Redirect extends ActiveRecord
{
    public $flushCache = false;

    const STATUS_ACTIVE = true;
    const STATUS_NOT_ACTIVE = false;

    public $statusOptions = [
        self::STATUS_ACTIVE => 'Активно',
        self::STATUS_NOT_ACTIVE => 'Не активно',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%redirect}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'to', 'status'], 'required'],
            [['code', 'sort'], 'integer'],
            [['status'], 'boolean',],
            [['from', 'to'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from' => 'Откуда',
            'to' => 'Куда',
            'code' => 'Код ответа',
            'status' => 'Статус',
            'sort' => 'Сортировка',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }

    public function init()
    {
        parent::init();
        if($this->isNewRecord) {
            $this->status = self::STATUS_ACTIVE;
            $this->code = 301;
        }
    }

    public function afterFind()
    {
        parent::afterFind();

        if(!$this->sort) {
            $this->sort = intval($this->id.'0');
            $this->updateAttributes(['sort']);
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if($this->isNewRecord && !$this->sort) {
            $this->sort = intval($this->id.'0');
            $this->updateAttributes(['sort']);
        }
    }

    public static function handleRedirects()
    {
        $path = '/'.ltrim(Yii::$app->getRequest()->getPathInfo(), '/');

        foreach(self::find()->active()->orderBy('sort ASC')->asArray()->all() as $one) {
            if(preg_match("~{$one['from']}~ui", $path)) {
                $to = preg_replace('~'.$one['from'].'~ui', $one['to'], $path);
                Yii::$app->getResponse()->redirect($to, 301, true)->send();
                Yii::$app->end();
            }
        }
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
        ];
    }
}
