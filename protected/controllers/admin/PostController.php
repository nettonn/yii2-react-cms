<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Post;
use app\utils\AdminClientHelper;
use app\models\query\ActiveQuery;

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
        return [
            'status' => AdminClientHelper::getOptionsFromKeyValue(Post::instance()->statusOptions),
        ];
    }

}
