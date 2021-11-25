<?php

namespace app\models;

use app\behaviors\TimestampBehavior;
use app\models\base\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "setting".
 *
 * @property integer $id
 * @property string $name
 * @property string $key
 * @property integer $type
 * @property boolean $value_bool
 * @property integer $value_int
 * @property string $value_string
 * @property integer $is_deleted
 * @property integer $created_at
 * @property integer $updated_at
 */
class Setting extends ActiveRecord
{
    const TYPE_BOOL = 1;
    const TYPE_INT = 2;
    const TYPE_STRING = 3;

    public $typeOptions = [
        self::TYPE_BOOL => 'Переключатель',
        self::TYPE_INT => 'Число',
        self::TYPE_STRING => 'Строка',
    ];

    public $typeToValueOptions = [
        self::TYPE_BOOL => 'value_bool',
        self::TYPE_INT => 'value_int',
        self::TYPE_STRING => 'value_string',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%setting}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'key', 'type'], 'required'],
            [['value_string'], 'string'],
            [['type', 'value_int'], 'integer'],
            [['value_bool'], 'boolean'],
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
            'value_bool' => 'Значение',
            'value_int' => 'Значение',
            'value_string' => 'Значение',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['value'] = function ($model) {
            return $model->getValue();
        };

        return $fields;
    }


    public function init()
    {
        parent::init();
        if($this->isNewRecord) {
            $this->type = self::TYPE_STRING;
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

    public function getValueParseFunction ()
    {
        $options = [
            self::TYPE_BOOL => 'boolval',
            self::TYPE_INT => 'intval',
            self::TYPE_STRING => 'strval',
        ];
        if(isset($options[$this->type]))
            return $options[$this->type];
        return false;
    }

    public function getValue()
    {
        if($parseFunction = $this->getValueParseFunction()) {
            if(is_callable($parseFunction)) {
                return call_user_func($parseFunction, $this->{$this->getValueAttribute()});
            }
        }

        return $this->{$this->getValueAttribute()};
    }

    public function getValueAttribute()
    {
        return $this->typeToValueOptions[$this->type];
    }
}
