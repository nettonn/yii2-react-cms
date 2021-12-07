<?php namespace app\behaviors;

use app\models\Version;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\helpers\VarDumper;

/**
 * @property ActiveRecord $owner
 */
class VersionBehavior extends Behavior
{
    public $nameAttribute = 'name';

    public $attributes;

    public $validateModel = false;

    /**
     * attribute options in model field $attributeOptions
     * uses Inflector::variablize()
     * some_attribute will be someAttributeOptions
     * @var bool
     */
    public $useOptions = true;

    public $optionsSuffix = 'Options';

    protected $_oldAttributes;
    protected $_attributes;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE   => 'beforeUpdate',
            ActiveRecord::EVENT_AFTER_UPDATE    => 'afterUpdate',
            ActiveRecord::EVENT_BEFORE_DELETE   => 'beforeDelete',
            ActiveRecord::EVENT_AFTER_DELETE    => 'afterDelete',
        ];
    }

    /**
     * @param ActiveRecord $owner
     * @throws InvalidConfigException
     */
    public function attach($owner)
    {
        parent::attach($owner);

        if(!$this->validateModel)
            return;

        $owner = $this->owner;

        if(is_array($owner->getPrimaryKey())) {
            throw new InvalidConfigException('Composite primary keys not allowed');
        }

        if(!$this->attributes) {
            throw new InvalidConfigException('Please set attributes to save');
        }

        if(!$owner->hasAttribute($this->nameAttribute)) {
            throw new InvalidConfigException('Invalid name attribute');
        }
    }

    public function beforeUpdate()
    {
        $this->_oldAttributes = $this->versionGetWatchedAttributes(true);
    }

    public function afterUpdate()
    {
        $this->_attributes = $this->versionGetWatchedAttributes();
        if($this->isChanged()) {
            $this->saveVersion(Version::ACTION_UPDATE);
        }
    }

    public function beforeDelete()
    {
        $this->_oldAttributes = $this->versionGetWatchedAttributes(true);
    }

    public function afterDelete()
    {
        $this->_attributes = $this->versionGetWatchedAttributes();
        $this->saveVersion(Version::ACTION_DELETE);
    }

    public function versionGetAttributesOptions()
    {
        if(!$this->useOptions)
            return [];

        $result = [];
        $owner = $this->owner;
        foreach($this->attributes as $attribute) {
            $attributeOptionsProp = Inflector::variablize($attribute).$this->optionsSuffix;
            if(!$owner->hasProperty($attributeOptionsProp))
                continue;

            $result[$attribute] = $owner->{$attributeOptionsProp};
        }
        return $result;
    }

    public function versionGetWatchedAttributes($old = false): array
    {
        $result = [];
        $owner = $this->owner;
        foreach($this->attributes as $attribute) {
            if(!$owner->hasAttribute($attribute))
                continue;

            $result[$attribute] = $old ? $owner->getOldAttribute($attribute) : $owner->getAttribute($attribute);
        }
        return $result;
    }

    public function versionGetVersionsUrl()
    {
        $owner = $this->owner;
        return Version::instance()->getAdminIndexUrl([
            'filters' => [
                'link_type' => get_class($owner),
                'link_id' =>  $owner->getPrimaryKey(),
            ]
        ]);
    }

    protected function isChanged()
    {
        return $this->_oldAttributes != $this->_attributes;
    }

    protected function saveVersion($action)
    {
        if(!$this->_oldAttributes)
            return;
        $owner = $this->owner;
        $version = new Version();
        $version->name = $owner->getAttribute($this->nameAttribute);
        $version->action = $action;
        $version->link_type = get_class($owner);
        $version->link_id = $owner->getPrimaryKey();
        $version->version_attributes_array = $this->_oldAttributes;
        if(!$version->save()) {
            Yii::error(VarDumper::dumpAsString($version->getErrors()));
        }
    }
}
