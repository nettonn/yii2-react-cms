<?php namespace app\models\blocks;

use app\models\query\ActiveQuery;

class BlockQuery extends ActiveQuery
{
    public $type;

    public function prepare($builder)
    {
        if ($this->type !== null) {
            $this->andWhere(['type' => $this->type]);
        }
        return parent::prepare($builder);
    }
}
