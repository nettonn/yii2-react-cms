<?php

namespace app\models;

use app\behaviors\TimestampBehavior;
use app\models\base\ActiveRecord;
use Yii;
use yii\helpers\StringHelper;

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

    public $version_attributes_array = [];

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

    public static function getModelLabel(): string
    {
        return 'Версии';
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['link_type_label'] = function($model) {
            $label = ActiveRecord::getModelLabelForClass($model->link_type);
            return $label ?? $model->link_type;
        };

        $fields['action_text'] = function($model) {
            return $model->actionOptions[$model->action];
        };

        $fields['attributes_compare'] = function($model) {
            if($model->version_attributes_array) {
                return $model->getAttributesCompare();
            }
            return null;
        };

        $fields['owner_update_url'] = function($model) {
            if($owner = $model->getOwner()) {
                return $owner->getAdminUpdateUrl();
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
        $versionAttributes = $this->version_attributes_array;

        $owner = $this->getOwner();

        $currentAttributes = $attributesOptions = [];

        if($owner) {
            if($this->action === self::ACTION_UPDATE && $owner->hasMethod('versionGetWatchedAttributes')) {
                $currentAttributes = $owner->versionGetWatchedAttributes();
            }
            if($owner->hasMethod('versionGetAttributesOptions')) {
                $attributesOptions = $owner->versionGetAttributesOptions();
            }
        }

        $result = [];
        foreach($versionAttributes as $attribute => $value) {
            $options = $attributesOptions[$attribute] ?? false;
            $item = [
                'attribute' => $attribute,
                'label' => $owner ? $owner->getAttributeLabel($attribute) : $attribute,
            ];

            $item['version_value'] = $options[$value] ?? $value;

            if(isset($currentAttributes[$attribute])) {
                $currentValue = $currentAttributes[$attribute];

                $item['current_value'] = $options[$currentValue] ?? $currentValue;
                $item['is_diff'] = $currentAttributes[$attribute] != $value;
            }
            $result[] = $item;
        }

        return $result;
    }

    public function getLinkTypeOptions()
    {
        $linkTypeOptions = [];
        foreach(Version::find()->select('link_type')->column() as $linkType) {
            $label = ActiveRecord::getModelLabelForClass($linkType);
            $linkTypeOptions[$linkType] = $label ?? $linkType;
        }
        asort($linkTypeOptions);
        return $linkTypeOptions;
    }

    public function getLinkIdOptions()
    {
        return Version::find()
            ->select('DISTINCT(link_id)')
            ->orderBy('link_id ASC')
            ->indexBy('link_id')
            ->column();
    }
}
