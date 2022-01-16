<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Order;
use app\models\query\ActiveQuery;

class OrderController extends RestController
{
    public $modelWith = ['files'];
    public $modelClass = Order::class;
    public $indexQuerySelectExclude = ['info', 'message'];
    public $defaultSortAttribute = 'created_at';
    public $defaultSortDirection = SORT_DESC;

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'subject',  "$search"],
            ['like', 'name',  "$search"],
            ['like', 'phone',  "$search"],
            ['like', 'email',  "$search"],
            ['like', 'message',  "$search"],
            ['like', 'info',  "$search"],
            ['like', 'url',  "$search"],
            ['like', 'referrer',  "$search"],
            ['like', 'entrance_page',  "$search"],
            ['like', 'ip',  "$search"],
            ['like', 'user_agent',  "$search"],
        ]);
    }

    public function modelOptions(): array
    {
        return [
        ];
    }

    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update'], $actions['model-defaults']);
        return $actions;
    }

    public function verbs(): array
    {
        $verbs = parent::verbs();
        unset($verbs['create'], $verbs['update'], $verbs['model-defaults']);
        return $verbs;
    }
}
