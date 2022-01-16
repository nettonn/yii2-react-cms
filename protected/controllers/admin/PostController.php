<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Post;
use app\utils\AdminClientHelper;
use app\models\query\ActiveQuery;
use Yii;

class PostController extends RestController
{
    public $modelClass = Post::class;

    public $indexQuerySelectExclude = ['content', 'description'];

    public $modelWith = ['images'];

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'name',  "$search"],
            ['like', 'alias',  "$search"],
            ['like', 'description',  "$search"],
            ['like', 'content',  "$search"],
        ]);
    }

    protected function prepareQuery(ActiveQuery $query): ActiveQuery
    {
        $query = parent::prepareQuery($query);

        return $query->andWhere(['section_id' => Yii::$app->request->get('section_id')]);
    }

    public function modelOptions(): array
    {
        return [
            'status' => AdminClientHelper::getOptionsFromKeyValue(Post::instance()->statusOptions),
        ];
    }

}
