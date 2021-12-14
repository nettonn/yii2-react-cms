<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\base\ActiveRecord;
use app\models\Version;
use app\utils\AdminClientHelper;
use app\models\query\ActiveQuery;

class VersionController extends RestController
{
    public $modelClass = Version::class;
    public $indexQuerySelectExclude = ['version_attributes'];
    public $defaultSortAttribute = 'created_at';
    public $defaultSortDirection = SORT_DESC;

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'name',  "$search"],
            ['like', 'link_type',  "$search"],
            ['like', 'link_id',  "$search"],
        ]);
    }

    public function modelOptions(): array
    {
        $linkTypeOptions = [];
        foreach(Version::find()->select('link_type')->notDeleted()->column() as $linkType) {
            $label = ActiveRecord::getModelLabelForClass($linkType);
            $linkTypeOptions[$linkType] = $label ?? $linkType;
        }
        asort($linkTypeOptions);

        $linkIdOptions = Version::find()
            ->select('DISTINCT(link_id)')
            ->orderBy('link_id ASC')
            ->indexBy('link_id')
            ->notDeleted()
            ->column();

        return [
            'action' => AdminClientHelper::getOptionsFromKeyValue( Version::instance()->actionOptions),
            'link_type' => AdminClientHelper::getOptionsFromKeyValue($linkTypeOptions),
            'link_id' => AdminClientHelper::getOptionsFromKeyValue($linkIdOptions),
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
