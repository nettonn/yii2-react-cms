<?php namespace app\utils;

use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class TreeModelHelper extends \yii\base\BaseObject
{
    public $query;

    public $maxLevel;

    /**
     * @var callback with params $helper TreeModelHelper object, $itemArray, $currentLevel
     */
    public $itemFunction;

    public $idAttribute = 'id';

    public $parentAttribute = 'parent_id';

    public $itemChildrenAttribute = 'children';

    public $levelAttribute = 'level';

    public $queryFilterLevel = true;

    private $_childrenData;

    /**
     * @inheritDoc
     */
    public function init()
    {
        if(!$this->query || !is_a($this->query, ActiveQuery::class))
            throw new InvalidConfigException('Query must be set and be instance of yii\db\ActiveQuery');

        if($this->itemFunction && !is_callable($this->itemFunction))
            throw new InvalidConfigException('ItemFunction must be callable');

        parent::init();
    }


    public function getItems($parent = 'root', $currentLevel = 1)
    {
        if($this->maxLevel && $currentLevel > $this->maxLevel)
            return [];

        $data = $this->getChildrenData($parent);
        if(!$data)
            return [];

        $items = [];

        foreach($data as $oneData) {
            if($this->itemFunction) {
                $item = call_user_func($this->itemFunction, $this, $oneData, $currentLevel);
            } else {
                $item = $oneData;

                $childrenItems = $this->getItems($item[$this->idAttribute], $currentLevel + 1);

                if(is_object($item) && is_a($item, ActiveRecord::class)) {
                    $item->populateRelation($this->itemChildrenAttribute, $childrenItems);
                } else {
                    $item[$this->itemChildrenAttribute] = $childrenItems;
                }
            }
            $items[] = $item;
        }

        return $items;

    }

    public function getChildrenData($parent = 'root')
    {
        if(null === $this->_childrenData) {
            if($this->maxLevel && $this->levelAttribute && $this->queryFilterLevel) {
                $modelClass =  $this->query->modelClass;
                if(isset($modelClass::getTableSchema()->columns[$this->levelAttribute])) {
                    $this->query->andWhere(['<=', $this->levelAttribute, $this->maxLevel]);
                }
            }
            $models = $this->query->all();

            $this->_childrenData = [];
            foreach($models as $model) {
                $key = $model[$this->parentAttribute] ? $model[$this->parentAttribute] : 'root';
                $this->_childrenData[$key][] = $model;
            }
        }

        if(isset($this->_childrenData[$parent]))
            return $this->_childrenData[$parent];

        return false;
    }
}
