<?php

namespace app\models;

use app\behaviors\TimestampBehavior;
use app\models\base\ActiveRecord;
use Yii;

/**
 * This is the model class for table "version".
 *
 * @property int $id
 * @property string $name
 * @property string $link_type
 * @property int $link_id
 * @property string $action
 * @property string|null $version_attributes
 * @property array|null $version_attributes_array
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Version extends ActiveRecord
{
    const ACTION_UPDATE = 'UPDATE';
    const ACTION_DELETE = 'DELETE';

    public $actionOptions = [
        self::ACTION_UPDATE => 'Обновление',
        self::ACTION_DELETE => 'Удаление',
    ];

    public $flushCache = false;

    public $version_attributes_array;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%version}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'link_type', 'link_id', 'action'], 'required'],
            [['link_id'], 'integer'],
            [['name', 'action'], 'string', 'max' => 255],
            [['link_type'], 'string', 'max' => 128],
            [['version_attributes_array'], 'safe'],
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
            'link_type' => 'Link Type',
            'link_id' => 'Link ID',
            'action' => 'Action',
            'version_attributes' => 'Attributes',
            'version_attributes_array' => 'Attributes',
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

    public function fields()
    {
        $fields = parent::fields();

        $fields['action_text'] = function($model) {
            return $model->actionOptions[$model->action];
        };

        $fields['attributes_compare'] = function($model) {
            if($model->version_attributes_array) {
                return $model->getAttributesCompare();
            }
            return null;
        };

        return $fields;
    }


    public function afterFind()
    {
        parent::afterFind();

        $this->version_attributes_array = $this->version_attributes ? unserialize($this->version_attributes) : $this->version_attributes_array;
    }

    public function beforeSave($insert)
    {
        $this->version_attributes = $this->version_attributes_array ? serialize($this->version_attributes_array) : $this->version_attributes;

        return parent::beforeSave($insert);
    }

    public function getOwner(): ?\yii\db\ActiveRecord
    {
        if(!class_exists($this->link_type))
            return null;
        $class = $this->link_type;
        $primaryKey = $class::primaryKey();
        $primaryKey = current($primaryKey);
        $owner = $class::find()->where([$primaryKey => $this->link_id])->notDeleted()->one();
        if($owner)
            return $owner;
        return $class::instance();
    }

    public function getAttributesCompare()
    {
        $owner = $this->getOwner();

        $currentAttributes =
            $this->action === self::ACTION_UPDATE
            && $owner
            && $owner->hasMethod('versionGetWatchedAttributes')
            ? $owner->versionGetWatchedAttributes()
            : [];


        $versionAttributes = $this->version_attributes_array;

        if(!$versionAttributes) {
            return [];
        }

        $result = [];

        foreach($versionAttributes as $attribute => $value) {
            $item = [
                'attribute' => $attribute,
                'label' => $owner ? $owner->getAttributeLabel($attribute) : $attribute,
                'version_value' => $value,
            ];
            if(isset($currentAttributes[$attribute])) {
                $item['current_value'] = $currentAttributes[$attribute];
                $item['is_diff'] = $currentAttributes[$attribute] != $value;
            }
            $result[] = $item;
        }

        return $result;
    }
}
