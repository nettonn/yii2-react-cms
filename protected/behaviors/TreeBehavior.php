<?php namespace app\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

class TreeBehavior extends Behavior
{
    public $pkAttribute = 'id';

    public $parentAttribute = 'parent_id';

    public $nameAttribute = 'name';

    public $breadcrumbNameAttribute = 'name';

    public $aliasAttribute = 'alias';

    public $urlAttribute = '_url';

    public $urlGenerateMethod = 'generateUrl';

    public $pathAttribute = 'path';

    public $levelAttribute = 'level';

    public $parentRelation = 'parent';

    public $childrenRelation = 'children';

    public $updateChildren = true;

    public $query;

    protected $_beforeDeleteChildren;
    protected $_beforeDeleteChildrenIds;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT   => 'beforeSave',
//            ActiveRecord::EVENT_AFTER_INSERT    => 'afterSave',
            BaseActiveRecord::EVENT_BEFORE_UPDATE   => 'beforeSave',
            BaseActiveRecord::EVENT_AFTER_UPDATE    => 'afterSave',
            BaseActiveRecord::EVENT_BEFORE_DELETE   => 'beforeDelete',
            BaseActiveRecord::EVENT_AFTER_DELETE    => 'afterDelete',
        ];
    }

    protected function getQuery()
    {
        if(!$this->query) {
            $this->query = get_class($this->owner)::find();
            if($this->query->hasMethod('notDeleted')) {
                $this->query = $this->query->notDeleted();
            }
        }
        return clone $this->query;
    }

    public function beforeSave()
    {
        $owner = $this->owner;
        if(!$owner->getAttribute($this->parentAttribute) || $owner->getAttribute($this->parentAttribute) == $owner->{$this->pkAttribute})
            $owner->setAttribute($this->parentAttribute, null);

        $this->updateNeededAttributes($owner, false);
    }

    public function afterSave()
    {
        if($this->updateChildren) {
            $this->updateChildrenAttributes();
        }
    }

    public function beforeDelete()
    {
        $owner = $this->owner;
        $this->_beforeDeleteChildren = $owner->{$this->childrenRelation};
        $this->_beforeDeleteChildrenIds = $this->treeGetChildrenArray($owner->{$this->pkAttribute});
    }

    public function afterDelete()
    {
        $owner = $this->owner;
        $owner->updateAll(
            [$this->parentAttribute => $owner->getAttribute($this->parentAttribute)],
            [$this->parentAttribute => $owner->{$this->pkAttribute}]
        );
//        $modelParent = $owner->{$this->parentAttribute};
        if($this->updateChildren) {
            foreach ($this->_beforeDeleteChildren as $child) {
                $child->{$this->parentAttribute} = null;
                $child->updateAttributes([$this->parentAttribute]);
            }
            $this->updateChildrenAttributes($this->_beforeDeleteChildrenIds);
        }
    }

    public function treeGetPath($separator = '/')
    {
        $owner = $this->owner;
        $uri = [$owner->getAttribute($this->aliasAttribute)];

        $model = $owner;

        $i = 10;

        while ($i-- && $model->{$this->parentRelation}){
            $uri[] = $model->{$this->parentRelation}->{$this->aliasAttribute};
            $model = $model->{$this->parentRelation};
            if($model->parent_id === 0 || $model->parent_id === null)
                break;
        }
        return implode($separator, array_reverse($uri));
    }

    /**
     * Returns array of primary keys of children items
     * @param mixed $parent_id
     * @return array
     */
    public function treeGetChildrenArray($parent_id = null)
    {
        $items = $this->getQuery()
            ->select([$this->pkAttribute, $this->parentAttribute])
            ->asArray()
            ->all();
        $result = [];

        $this->_childrenArrayRecursive($items, $result, $parent_id);
        return array_unique($result);
    }

    protected function _childrenArrayRecursive(&$items, &$result, $parent_id)
    {
        foreach ($items as $item){
            if ($item[$this->parentAttribute] == $parent_id){
                $result[] = $item[$this->pkAttribute];
                $this->_childrenArrayRecursive($items, $result, $item[$this->pkAttribute]);
            }
        }
    }

    /**
     * Returns tabulated array ($id=>$title, $id=>$title, ...)
     * @param integer|null $parent_id number
     * @param integer $level number
     * @param \yii\db\ActiveQuery $query
     * @return array
     */
    public function treeGetTabList($parent_id = null, $level = 9999, $query = false)
    {
        if($query) {
            $items = $query;
        } else {
            $items = $this->getQuery();
        }
        $items = $items->select([$this->pkAttribute, $this->parentAttribute, $this->nameAttribute])
            ->asArray()
            ->all();

        $result = [];

        $this->_getTabListRecursive($items, $result, $parent_id, 0, $level);

        return $result;
    }

    protected function _getTabListRecursive(&$items, &$result, $parent_id, $indent, $level)
    {
        if(!$level--) return;

        foreach ($items as $item){
            if ($item[$this->parentAttribute] == $parent_id && !isset($result[$item[$this->pkAttribute]])){
                $result[$item[$this->pkAttribute]] = trim(str_repeat('-', $indent) .' '. $item[$this->nameAttribute]);
                $this->_getTabListRecursive($items, $result, $item[$this->pkAttribute], $indent + 1, $level);
            }
        }
    }

    public function treeGetParents($reverse = true)
    {
        $parents = [];
        $model = $this->owner;
        if($model->{$this->parentAttribute} !== null) {
            while($parent = $model->{$this->parentRelation}) {
                $parents[] = $parent;
                $model = $parent;
            }
        }
        if($reverse) {
            $parents = array_reverse($parents);
        }
        return $parents;
    }

    public function treeGetBreadcrumbs()
    {
        $result = [];
        foreach($this->treeGetParents() as $model) {
            $result[] = ['label'=>$this->getBreadcrumbName($model), 'url'=>$model->getUrl()];
        }
        $result[] = ['label'=>$this->getBreadcrumbName($this->owner), 'url' => $this->owner->getUrl()];
        return $result;
    }

    public function treeRootModels($parentId = null)
    {
        return $this->getQuery()->andWhere(['parent_id'=>$parentId])->all();
    }

    protected function updateChildrenAttributes($childrenIds = false)
    {
        if(false === $childrenIds)
            $childrenIds = $this->treeGetChildrenArray($this->owner->{$this->pkAttribute});
        $children = $this->getQuery()
            ->andWhere(['in', 'id', $childrenIds])->all();

        foreach($children as $model) {
            $this->updateNeededAttributes($model);
        }
    }

    protected function updateNeededAttributes($model, $save = true)
    {
        $attributes = [];
        if($this->pathAttribute && $model->hasAttribute($this->pathAttribute)) {
            $model->{$this->pathAttribute} = $model->treeGetPath();
            $attributes[] = $this->pathAttribute;
        }
        if($this->levelAttribute && $model->hasAttribute($this->levelAttribute)) {
            $model->{$this->levelAttribute} = count($model->treeGetParents()) + 1;
            $attributes[] = $this->levelAttribute;
        }
        if($this->urlAttribute && $this->urlGenerateMethod && $model->hasAttribute($this->urlAttribute) && $model->hasMethod($this->urlGenerateMethod)) {
            $model->{$this->urlAttribute} = $model->{$this->urlGenerateMethod}();
            $attributes[] = $this->urlAttribute;
        }

        if($save && $attributes)
            $model->updateAttributes($attributes);
    }

    protected function getBreadcrumbName($model)
    {
        if(is_callable($this->breadcrumbNameAttribute)) {
            return ($this->breadcrumbNameAttribute)($model);
        }
        return $model->{$this->breadcrumbNameAttribute};
    }
}
