<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Block;
use app\models\BlockItem;
use app\utils\AdminClientHelper;
use app\models\query\ActiveQuery;
use Yii;
use yii\web\NotFoundHttpException;

class BlockItemController extends RestController
{
    public $modelClass = BlockItem::class;
    public $isTree = true;

    public $indexQuerySelectExclude = ['data'];

    public function init()
    {
        if($blockId = Yii::$app->getRequest()->get('block_id')) {
            $block = Block::find()->where(['id' => $blockId])->notDeleted()->one();
            if(!$block) {
                throw new NotFoundHttpException('Block not found');
            }
            $this->createScenario = $block->type;
        }

        parent::init();
    }

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'name',  "$search"],
        ]);
    }

    protected function prepareQuery(ActiveQuery $query): ActiveQuery
    {
        $query = parent::prepareQuery($query);

        $blockId = Yii::$app->request->get('block_id');
        $query = $query->andWhere(['block_id' => $blockId]);

        return $query;
    }

    public function modelOptions(): array
    {
        return [
            'status' => AdminClientHelper::getOptionsFromKeyValue(BlockItem::instance()->statusOptions),
        ];
    }

}
