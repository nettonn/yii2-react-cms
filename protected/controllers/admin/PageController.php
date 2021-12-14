<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Page;
use app\utils\AdminClientHelper;
use app\models\query\ActiveQuery;

class PageController extends RestController
{
    public $modelClass = Page::class;
    public $isTree = true;

    public $indexQuerySelectExclude = ['content', 'description', 'seo_title', 'seo_h1', 'seo_description', 'seo_keywords'];

    public $modelWith = ['images'];

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'name',  "$search"],
            ['like', 'alias',  "$search"],
            ['like', 'description',  "$search"],
            ['like', 'content',  "$search"],
            ['like', 'seo_title',  "$search"],
            ['like', 'seo_h1',  "$search"],
            ['like', 'seo_description',  "$search"],
            ['like', 'seo_keywords',  "$search"],
        ]);
    }

    public function modelOptions(): array
    {
        return [
            'status' => AdminClientHelper::getOptionsFromKeyValue(Page::instance()->statusOptions),
            'parent' => AdminClientHelper::getOptionsFromModelQuery(Page::find()->notDeleted()->asArray()),
        ];
    }
}
