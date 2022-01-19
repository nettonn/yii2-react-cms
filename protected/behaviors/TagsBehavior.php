<?php namespace app\behaviors;

use app\models\query\ActiveQuery;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;
use yii\helpers\StringHelper;

class TagsBehavior extends BaseBehavior
{
    public $attribute = 'user_tags';

    public $relation = 'tags';

    public $tagNameAttribute = 'name';

    public $validate = false;

    protected $_value;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT  => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_UPDATE  => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * @param BaseActiveRecord $owner
     * @throws InvalidConfigException
     */
    public function attach($owner)
    {
        parent::attach($owner);
    }

    protected function validate()
    {
        parent::validate();

        if(!$this->relation || $this->owner->hasProperty($this->relation)) {
            throw new InvalidConfigException('$relation must be set and be valid ActiveRecord relation name');
        }
    }

    public function afterSave()
    {
        $tags = array_unique(array_map('strtolower', $this->getTags()));
        $currentTags = array_unique(array_map('strtolower', $this->getTags(true)));

        if($tags === $currentTags)
            return;

        $owner = $this->owner;
        /** @var ActiveQuery $relationQuery */
        $relationQuery = $owner->{'get'.$this->relation}();
        $tagModelClass = $relationQuery->modelClass;

        $tagModels = $tagModelClass::find()
            ->andWhere(['in', $this->tagNameAttribute, $tags])
            ->indexBy(function ($row) {
                return strtolower($row[$this->tagNameAttribute]);
            })
            ->all();

        $this->owner->unlinkAll($this->relation, true);

        foreach($tags as $tag) {
            if(isset($tagModels[$tag])) {
                $tagModel = $tagModels[$tag];
            } else {
                $tagModel = new $tagModelClass;
                $tagModel->{$this->tagNameAttribute} = $tag;
                $tagModel->save();
            }

            $owner->link($this->relation, $tagModel);
        }

        $tagsToUnlink = array_diff($currentTags, $tags);

        $tagModelsToUnlink = $tagModelClass::find()
            ->andWhere(['in', $this->tagNameAttribute, $tagsToUnlink])
            ->all();

        foreach($tagModelsToUnlink as $tagModel) {
            $owner->unlink($this->relation, $tagModel);
        }
    }

    public function afterDelete()
    {
        $this->owner->unlinkAll($this->relation, true);
    }

    protected function getTags($reselect = false)
    {
        if($this->_value === null || $reselect) {
            $tagModels = $this->owner->{$this->relation};
            $result = [];
            foreach ($tagModels as $tagModel) {
                $result[] = trim($tagModel->{$this->tagNameAttribute});
            }
            $this->_value = $result;
        }
        return $this->_value;
    }

    protected function setTags($value)
    {
        if(is_string($value) || is_numeric($value)) {
            $this->_value = [trim($value)];
        } elseif (is_array($value)) {
            $this->_value = array_map('trim', $value);
        }
    }

    public function canGetProperty($name, $checkVars = true)
    {
        if($this->attribute === $name) {
            return true;
        }

        return parent::canGetProperty($name, $checkVars);
    }

    public function canSetProperty($name, $checkVars = true)
    {
        if($this->attribute === $name) {
            return true;
        }

        return parent::canSetProperty($name, $checkVars);
    }

    public function __set($name, $value)
    {
        if($this->attribute === $name) {
            $this->setTags($value);
            return;
        }
        parent::__set($name, $value);
    }

    public function __get($name)
    {
        if($this->attribute === $name) {
            return $this->getTags();
        }
        return parent::__get($name);
    }
}
