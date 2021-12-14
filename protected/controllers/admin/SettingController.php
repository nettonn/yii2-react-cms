<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Setting;
use app\utils\AdminClientHelper;
use app\models\query\ActiveQuery;

class SettingController extends RestController
{
    public $modelClass = Setting::class;
    public $indexQuerySelectExclude = ['content'];

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'name',  "$search"],
            ['like', 'key',  "$search"],
            ['like', 'value_bool',  "$search"],
            ['like', 'value_int',  "$search"],
            ['like', 'value_string',  "$search"],
        ]);
    }

    public function modelOptions(): array
    {
        return [
            'type' => AdminClientHelper::getOptionsFromKeyValue(Setting::instance()->typeOptions),
        ];
    }
}
