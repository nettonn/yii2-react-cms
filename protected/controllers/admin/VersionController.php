<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Version;
use app\utils\AdminClientHelper;
use yii\db\ActiveQuery;

class VersionController extends RestController
{
    public $modelClass = Version::class;
    public $indexQuerySelectExclude = ['version_attributes'];

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
        return [
            'action' => AdminClientHelper::getOptionsFromKeyValue( Version::instance()->actionOptions),
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
