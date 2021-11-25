<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Post;
use yii\db\ActiveQuery;

class PostController extends RestController
{
    public $modelClass = Post::class;

    public $indexQuerySelectExclude = ['content', 'introtext'];

    public $modelWith = ['images', 'files', 'picture'];

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'name',  "$search"],
            ['like', 'alias',  "$search"],
            ['like', 'introtext',  "$search"],
            ['like', 'content',  "$search"],
        ]);
    }

    public function modelOptions(): array
    {
        $instance = Post::instance();

        return [
            'status' => prepare_value_text_options($instance->statusOptions),
        ];
    }

}
