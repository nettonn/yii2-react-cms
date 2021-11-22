<?php namespace app\models\query;

use yii\db\Expression;
use yii\helpers\ArrayHelper;

class ActiveQuery extends \yii\db\ActiveQuery
{
    public function onlyRoots()
    {
        return $this->andWhere('parent_id IS NULL OR parent_id = 0');
    }

    public function notDeleted()
    {
        return $this->andWhere(['is_deleted' => false]);
    }

    public function active($state = true)
    {
        return $this->andWhere(['status' => $state])->andWhere(['is_deleted' => false]);
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
