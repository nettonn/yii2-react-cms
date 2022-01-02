<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\blocks\Block;
use app\utils\AdminClientHelper;
use app\models\query\ActiveQuery;
use Yii;

class BlockController extends RestController
{
    public $modelClass = Block::class;
    public $isTree = true;

    public $indexQuerySelectExclude = ['data'];

    public function init()
    {
        $this->modelClass = Block::getTypeClass(Yii::$app->getRequest()->post('type'));

        parent::init();
    }


    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'name',  "$search"],
        ]);
    }

    public function modelOptions(): array
    {
        return [
            'status' => AdminClientHelper::getOptionsFromKeyValue(Block::instance()->statusOptions),
            'type' => AdminClientHelper::getOptionsFromKeyValue(Block::getTypeLabels()),
        ];
    }
}
