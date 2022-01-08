<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Log;
use app\utils\AdminClientHelper;
use app\models\query\ActiveQuery;

class LogController extends RestController
{
    public $modelClass = Log::class;
    public $indexQuerySelectExclude = ['messages'];
    public $defaultSortAttribute = 'created_at';
    public $defaultSortDirection = SORT_DESC;

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'name',  "$search"],
            ['like', 'url',  "$search"],
            ['like', 'messages',  "$search"],
        ]);
    }

    public function modelOptions(): array
    {
        $nameOptions = Log::find()
            ->select('DISTINCT(name)')
            ->orderBy('name ASC')
            ->indexBy('name')
            ->column();

        return [
            'name' => AdminClientHelper::getOptionsFromKeyValue($nameOptions),
        ];
    }

    /**
     * @return string sql
     */
    protected function getLastModifiedSql(): string
    {
        return 'SELECT MAX(created_at) from '.($this->modelClass)::tableName();
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
