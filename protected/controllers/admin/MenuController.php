<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Menu;
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
        $instance = Menu::instance();

        return [
            'status' => prepare_value_text_options($instance->statusOptions),
        ];
    }
}
