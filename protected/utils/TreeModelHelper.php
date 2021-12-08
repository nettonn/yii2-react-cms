<?php namespace app\utils;

use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class TreeModelHelper extends BaseObject
{
    /**
     * Query for all models in tree
     * @var ActiveQuery
     */
    public $query;

    public $maxLevel;

    /**
     * @var callback with params $helper TreeModelHelper object, $itemArray, $currentLevel
     */
    public $itemFunction;

    public $nameAttribute = 'name';

    public $pkAttribute = 'id';

    public $parentAttribute = 'parent_id';

    public $itemChildrenAttribute = 'children';

    public $levelAttribute = 'level';

    public $queryFilterLevel = true;

    public $useCache = true;

    /**
     * @inheritDoc
     */
    public function init()
    {
        if(!$this->query || !is_a($this->query, ActiveQuery::class))
            throw new InvalidConfigException('Query must be set and be instance of yii\db\ActiveQuery');

        if($this->useCache) {
            $this->query->cache();
        }

        if($this->itemFunction && !is_callable($this->itemFunction))
            throw new InvalidConfigException('ItemFunction must be callable');

        parent::init();
    }

    public function buildTree($parent = null, $currentLevel = 1)
    {
        if($this->maxLevel && $currentLevel > $this->maxLevel)
            return [];

        $children = $this->_getChildren($parent);
        if(!$children)
            return [];

        $items = [];

        foreach($children as $child) {
            if($this->itemFunction) {
                $item = call_user_func($this->itemFunction, $this, $child, $currentLevel);
            } else {
                $item = $child;

                $childrenTree = $this->buildTree($item[$this->pkAttribute], $currentLevel + 1);

                if(is_object($item) && is_a($item, ActiveRecord::class)) {
                    $item->populateRelation($this->itemChildrenAttribute, $childrenTree);
                } else {
                    $item[$this->itemChildrenAttribute] = $childrenTree;
                }
            }
            $items[] = $item;
        }

        return $items;
    }

    private $_childrenData;

    protected function _getChildren($parent = null)
    {
        if(null === $this->_childrenData) {
            $cacheKey = $this->useCache && $this->query->asArray
                ? $this->getCacheKey('childrenData')
                : null;
            if(!$this->useCache || !$cacheKey || false === $this->_childrenData = Yii::$app->cache->get($cacheKey)) {
                $query = clone $this->query;
                if($this->maxLevel && $this->levelAttribute && $this->queryFilterLevel && !$parent) {
                    $modelClass =  $query->modelClass;
                    if(isset($modelClass::getTableSchema()->columns[$this->levelAttribute])) {
                        $query = $query->andWhere(['<=', $this->levelAttribute, $this->maxLevel]);
                    }
                }
                $models = $query->all();

                $this->_childrenData = [];
                foreach($models as $model) {
                    $key = $model[$this->parentAttribute] ?? null;
                    $this->_childrenData[$key][] = $model;
                }

                if($this->useCache && $cacheKey) {
                    Yii::$app->cache->set($cacheKey, $this->_childrenData);
                }
            }
        }

        return $this->_childrenData[$parent] ?? false;
    }

    public function getChildrenIds($parent = null)
    {
        $ids = $this->_getChildrenIds($parent);
        if(!$ids)
            return [];

        $items = [];
        foreach($ids as $id) {
            $items[] = $id;
            if($childrenIds = $this->getChildrenIds($id)) {
                $items = array_merge($items, $childrenIds);
            }
        }

        return $items;
    }

    private $_childrenIdsData;

    protected function _getChildrenIds($parent = null)
    {
        if(null === $this->_childrenIdsData) {
            $cacheKey = $this->useCache
                ? $this->getCacheKey('childrenIds')
                : null;
            if(!$this->useCache || !$cacheKey || false === $this->_childrenIdsData = Yii::$app->cache->get($cacheKey)) {

                $query = clone $this->query;
                $items = $query
                    ->select([$this->pkAttribute, $this->parentAttribute])
                    ->asArray()
                    ->all();

                $this->_childrenIdsData = [];
                foreach ($items as $item) {
                    $key = $item[$this->parentAttribute] ?? null;
                    $this->_childrenIdsData[$key][] = $item[$this->pkAttribute];
                }
                if($this->useCache && $cacheKey) {
                    Yii::$app->cache->set($cacheKey, $this->_childrenIdsData);
                }
            }
        }
        return $this->_childrenIdsData[$parent] ?? false;
    }

    public function getParentModels($pk)
    {
        $ids = $this->getParentIds($pk);
        if(!$ids)
            return [];

        $query = clone $this->query;
        $models = $query->andWhere(['in', $this->pkAttribute, $ids])->indexBy($this->pkAttribute)->all();
        $sortedModels = [];
        foreach ($ids as $id) {
            if(!isset($models[$id]))
                continue;

            $sortedModels[] = $models[$id];
        }
        return $sortedModels;
    }

    public function getParentIds($pk)
    {
        $parent = $this->_getParent($pk);
        if(!$parent)
            return [];

        $items = [];

        $items[] = $parent;
        if($parentIds = $this->getParentIds($parent)) {
            $items = array_merge($items, $parentIds);
        }

        return $items;
    }

    private $_parentIdsData;

    protected function _getParent($pk)
    {
        if(null === $this->_parentIdsData) {
            $cacheKey = $this->useCache
                ? $this->getCacheKey('parentIds')
                : null;
            if(!$this->useCache || !$cacheKey || false === $this->_parentIdsData = Yii::$app->cache->get($cacheKey)) {
                $query = clone $this->query;
                $this->_parentIdsData = $query
                    ->select([$this->parentAttribute, $this->pkAttribute, ])
                    ->indexBy($this->pkAttribute)
                    ->asArray()
                    ->column();
                if($this->useCache && $cacheKey) {
                    Yii::$app->cache->set($cacheKey, $this->_parentIdsData);
                }
            }

        }
        return $this->_parentIdsData[$pk] ?? null;
    }

    public function getTabList($parent = null, $indent = 0)
    {
        $items = $this->_getTabListData($parent);
        if(!$items)
            return [];

        $result = [];
        foreach($items as $item) {
            $id = $item[$this->pkAttribute];
            $result[$item[$this->pkAttribute]] = trim(str_repeat('-', $indent) .' '. $item[$this->nameAttribute]);

            if($childrenItems = $this->getTabList($id, $indent + 1)) {
                $result = array_merge($result, $childrenItems);
            }
        }

        return $result;
    }

    private $_tabListData;

    protected function _getTabListData($parent = null)
    {
        if(null === $this->_tabListData) {
            $cacheKey = $this->useCache
                ? $this->getCacheKey('tabList')
                : null;
            if(!$this->useCache || !$cacheKey || false === $this->_tabListData = Yii::$app->cache->get($cacheKey)) {
                $query = clone $this->query;
                $query = $query
                    ->select([$this->pkAttribute, $this->parentAttribute, $this->nameAttribute])
                    ->asArray();

                $this->_tabListData = [];
                foreach($query->all() as $item) {
                    $key = $item[$this->parentAttribute] ?? null;
                    $this->_tabListData[$key][] = $item;
                }

                if($this->useCache && $cacheKey) {
                    Yii::$app->cache->set($cacheKey, $this->_tabListData);
                }
            }

        }
        return $this->_tabListData[$parent] ?? null;
    }

    protected function getCacheKey($prefix)
    {
        $cacheKey = static::class.'-prefix:'.$prefix.'-sql:'.$this->query->createCommand()->getRawSql();
        $attributes = [
            'maxLevel',
            'nameAttribute',
            'pkAttribute',
            'parentAttribute',
            'itemChildrenAttribute',
            'levelAttribute',
            'queryFilterLevel',
        ];
        foreach($attributes as $attribute) {
            $cacheKey .= '-'.$attribute.':'.$this->{$attribute};
        }

        return $cacheKey;
    }
}
