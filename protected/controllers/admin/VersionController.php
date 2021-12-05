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
        $linkTypeOptions = Version::find()->select('link_type')->indexBy('link_type')->column();
        $linkIdOptions = Version::find()->select('DISTINCT(link_id)')->indexBy('link_id')->column();

        return [
            'action' => AdminClientHelper::getOptionsFromKeyValue( Version::instance()->actionOptions),
            'link_type' => AdminClientHelper::getOptionsFromKeyValue($linkTypeOptions),
            'link_id' => AdminClientHelper::getOptionsFromKeyValue($linkIdOptions),
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
