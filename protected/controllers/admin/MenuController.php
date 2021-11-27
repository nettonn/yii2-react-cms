<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Menu;
use app\utils\AdminClientHelper;
use yii\db\ActiveQuery;

class MenuController extends RestController
{
    public $modelClass = Menu::class;

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'name',  "$search"],
            ['like', 'key',  "$search"],
        ]);
    }

    public function modelOptions(): array
    {
        return [
            'status' => AdminClientHelper::getOptionsFromKeyValue(Menu::instance()->statusOptions),
        ];
    }
}
