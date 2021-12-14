<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Queue;
use app\utils\AdminClientHelper;
use app\models\query\ActiveQuery;

class QueueController extends RestController
{
    public $modelClass = Queue::class;
    public $indexQuerySelectExclude = ['job'];
    public $defaultSortAttribute = 'pushed_at';
    public $defaultSortDirection = SORT_DESC;

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'channel',  "$search"],
            ['like', 'job',  "$search"],
        ]);
    }

    public function modelOptions(): array
    {
        $channelOptions = Queue::find()
            ->select('DISTINCT(channel)')
            ->orderBy('channel ASC')
            ->indexBy('channel')
            ->notDeleted()
            ->column();

        return [
            'name' => AdminClientHelper::getOptionsFromKeyValue($channelOptions),
        ];
    }

    /**
     * @return string sql
     */
    protected function getLastModifiedSql(): string
    {
        return 'SELECT GREATEST(MAX(pushed_at), MAX(reserved_at), MAX(done_at)) from '.($this->modelClass)::tableName();
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
