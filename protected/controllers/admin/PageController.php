<?php namespace app\controllers\admin;

use app\models\Page;
use yii\db\ActiveQuery;

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
        $instance = Page::instance();

        Page::$childrenWith = ['children'];

        $parentOptions = Page::find()->notDeleted()->onlyRoots()->with(['children'])->all();

        return [
            'status' => prepare_value_text_options($instance->statusOptions),
            'parent' => prepare_options_from_models($parentOptions),
        ];
    }
}
