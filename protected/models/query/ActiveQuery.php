<?php namespace app\models\query;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class ActiveQuery extends \yii\db\ActiveQuery
{
    public $softDeleteAttribute = 'is_deleted';

    protected $hasSoftDelete;

    public function init()
    {
        if(null === $this->hasSoftDelete) {
            /** @var ActiveRecord $modelClass */
            $modelClass = $this->modelClass;
            $this->hasSoftDelete = isset($modelClass::getTableSchema()->columns[$this->softDeleteAttribute]);
        }
        parent::init();
    }

    public function onlyRoots()
    {
        return $this->andWhere('parent_id IS NULL OR parent_id = 0');
    }

    public function notDeleted()
    {
        if($this->hasSoftDelete)
            return $this->andWhere([$this->softDeleteAttribute => false]);
        return $this;
    }

    public function active($state = true)
    {
        $query = $this->andWhere(['status' => $state]);
        if($this->hasSoftDelete)
            $query = $query->andWhere(['is_deleted' => false]);
        return $query;
    }

    public function orderSort($desc = false)
    {
        if($desc)
            return $this->orderBy('sort DESC');
        return $this->orderBy('sort ASC');
    }

    public function orderByIds($ids, $field = 'id')
    {
        $ids = implode(',', $ids);
        return $this->orderBy(new Expression("FIELD (`$field`, {$ids})"));
    }

    public function selectIds($field = 'id')
    {
        return $this->select($field)->asArray()->column();
    }

    public function selectMax($field)
    {
        return $this->select("MAX($field)")->createCommand()->queryScalar();
    }

    public function selectMin($field)
    {
        return $this->select("MIN($field)")->createCommand()->queryScalar();
    }

    public function selectOptions($valueField, $nameField)
    {
        return ArrayHelper::map($this->select([$valueField, $nameField])->asArray()->all(), $valueField, $nameField);
    }
}
