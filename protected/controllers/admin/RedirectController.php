<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Redirect;
use app\utils\AdminClientHelper;
use yii\db\ActiveQuery;

class RedirectController extends RestController
{
    public $modelClass = Redirect::class;

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'to',  "$search"],
            ['like', 'from',  "$search"],
            ['like', 'code',  "$search"],
        ]);
    }

    public function modelOptions(): array
    {
        return [
            'status' => AdminClientHelper::getOptionsFromKeyValue(Redirect::instance()->statusOptions),
        ];
    }
}
