<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Seo;
use app\utils\AdminClientHelper;
use app\models\query\ActiveQuery;

class SeoController extends RestController
{
    public $modelClass = Seo::class;
    public $isTree = true;

    public $indexQuerySelectExclude = ['top_content', 'bottom_content', 'title', 'h1', 'description', 'keywords'];

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'name',  "$search"],
            ['like', 'url',  "$search"],
            ['like', 'top_content',  "$search"],
            ['like', 'bottom_content',  "$search"],
            ['like', 'title',  "$search"],
            ['like', 'h1',  "$search"],
            ['like', 'description',  "$search"],
            ['like', 'keywords',  "$search"],
        ]);
    }

    public function modelOptions(): array
    {
        return [
            'status' => AdminClientHelper::getOptionsFromKeyValue(Seo::instance()->statusOptions),
            'parent' => AdminClientHelper::getOptionsFromModelQuery(Seo::find()->asArray()),
        ];
    }
}
