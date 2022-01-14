<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\MenuItem;
use app\utils\AdminClientHelper;
use Yii;
use app\models\query\ActiveQuery;

class MenuItemController extends RestController
{
    public $modelClass = MenuItem::class;
    public $isTree = true;

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'name',  "$search"],
            ['like', 'url',  "$search"],
            ['like', 'rel',  "$search"],
            ['like', 'title',  "$search"],
        ]);
    }

    protected function prepareQuery(ActiveQuery $query): ActiveQuery
    {
        $query = parent::prepareQuery($query);

        return $query->andWhere(['menu_id' => Yii::$app->request->get('menu_id')]);
    }

    public function modelOptions(): array
    {
        $parentOptionsQuery = MenuItem::find()
            ->andWhere(['menu_id' => Yii::$app->request->get('menu_id')])
            ->asArray();

        return [
            'status' => AdminClientHelper::getOptionsFromKeyValue(MenuItem::instance()->statusOptions),
            'parent' => AdminClientHelper::getOptionsFromModelQuery($parentOptionsQuery),
        ];
    }
}
