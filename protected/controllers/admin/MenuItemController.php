<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\MenuItem;
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
        $instance = MenuItem::instance();

        $menuId = Yii::$app->request->get('menuId');

        MenuItem::$childrenWith = ['children'];

        $parentOptions = MenuItem::find()
            ->notDeleted()
            ->onlyRoots()
            ->andWhere(['menu_id' => $menuId])
            ->with(['children'])
            ->all();

        return [
            'status' => prepare_value_text_options($instance->statusOptions),
            'parent' => prepare_options_from_models($parentOptions),
        ];
    }
}
