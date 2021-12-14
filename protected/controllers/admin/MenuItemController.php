<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\MenuItem;
use app\utils\AdminClientHelper;
use Yii;
use yii\db\ActiveQuery;

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

        $menuId = Yii::$app->request->get('menuId');
        $query = $query->andWhere(['menu_id' => $menuId]);

        return $query;
    }


    public function modelOptions(): array
    {
        $menuId = Yii::$app->request->get('menuId');

        $parentOptionsQuery = MenuItem::find()
            ->notDeleted()
            ->andWhere(['menu_id' => $menuId])
            ->asArray();

        return [
            'status' => AdminClientHelper::getOptionsFromKeyValue(MenuItem::instance()->statusOptions),
            'parent' => AdminClientHelper::getOptionsFromModelQuery($parentOptionsQuery),
        ];
    }
}
