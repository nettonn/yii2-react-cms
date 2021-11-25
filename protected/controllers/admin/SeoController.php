<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Seo;
use yii\db\ActiveQuery;

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
        $instance = Seo::instance();

        Seo::$childrenWith = ['children'];

        $parentOptions = Seo::find()->notDeleted()->onlyRoots()->with(['children'])->all();

        return [
            'status' => prepare_value_text_options($instance->statusOptions),
            'parent' => prepare_options_from_models($parentOptions),
        ];
    }
}
