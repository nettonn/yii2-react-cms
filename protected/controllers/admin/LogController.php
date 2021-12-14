<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Log;
use app\utils\AdminClientHelper;
use yii\db\ActiveQuery;

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
            ->notDeleted()
            ->column();

        return [
            'name' => AdminClientHelper::getOptionsFromKeyValue($nameOptions),
        ];
    }

    /**
     * For yii\caching\DbDependency
     * @return string sql
     */
    protected function getModelLastModifiedSql(): string
    {
        $modelClass = $this->modelClass;
        return 'SELECT MAX(created_at) from '.$modelClass::tableName();
    }

    /**
     * Timestamp in seconds
     * @return int | null
     */
    protected function getModelOptionsLastModified(): ?int
    {
        $modelClass = $this->modelClass;
        return $modelClass::find()->select('MAX(created_at)')->scalar();
    }
}
