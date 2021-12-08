<?php namespace app\behaviors;

use app\utils\TreeModelHelper;
use yii\base\Behavior;
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
        $this->_beforeDeleteChildrenIds = $this->treeGetChildrenIds();
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
        $parts = [$owner->getAttribute($this->aliasAttribute)];

        foreach($this->treeGetParents(false) as $model) {
            $parts[] = $model->{$this->aliasAttribute};
        }

        return implode($separator, array_reverse($parts));
    }

    public function treeGetChildrenIds()
    {
        $treeModelHelper = new TreeModelHelper([
            'pkAttribute' => $this->pkAttribute,
            'parentAttribute' => $this->parentAttribute,
            'query' => $this->getQuery(),
        ]);
        return $treeModelHelper->getChildrenIds($this->owner->{$this->pkAttribute});
    }

    /**
     * Returns tabulated array ($id=>$title, $id=>$title, ...)
     * @param integer|null $parent_id number
     * @param integer $level number
     * @param \yii\db\ActiveQuery $query
     * @return array
     */
    public function treeGetTabList()
    {
        $treeModelHelper = new TreeModelHelper([
            'nameAttribute' => $this->nameAttribute,
            'pkAttribute' => $this->pkAttribute,
            'parentAttribute' => $this->parentAttribute,
            'query' => $this->getQuery(),
        ]);
        return $treeModelHelper->getTabList($this->owner->{$this->pkAttribute});
    }

    public function treeGetParents($reverse = true)
    {
        if(!$this->owner->{$this->parentAttribute}) {
            return [];
        }
        $treeModelHelper = new TreeModelHelper([
            'pkAttribute' => $this->pkAttribute,
            'parentAttribute' => $this->parentAttribute,
            'query' => $this->getQuery(),
        ]);

        $models = $treeModelHelper->getParentModels($this->owner->{$this->pkAttribute});
        if($reverse) {
            $models = array_reverse($models);
        }
        return $models;
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

    public function treeRootModels()
    {
        return $this->getQuery()->andWhere(['parent_id'=>null])->all();
    }

    protected function updateChildrenAttributes($childrenIds = false)
    {
        if(false === $childrenIds) {
            $childrenIds = $this->treeGetChildrenIds();
        }

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
