<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Post;
use app\models\PostTag;
use app\utils\AdminClientHelper;
use app\models\query\ActiveQuery;
use Yii;

class PostController extends RestController
{
    public $modelClass = Post::class;

    public $indexQuerySelectExclude = ['content', 'description'];

    public $modelWith = ['images', 'tags'];

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
            'tag' => AdminClientHelper::getOptionsFromKeyValue(PostTag::find()->selectOptions('id', 'name')),
        ];
    }

    protected function getLastModifiedSql(): string
    {
        $postTable = ($this->modelClass)::tableName();
        $postTagTable = PostTag::tableName();

        return "
            SELECT MAX(updated_at) FROM (
                SELECT MAX(updated_at) AS updated_at FROM $postTable
                UNION ALL 
                SELECT MAX(updated_at) AS updated_at FROM $postTagTable
            ) a";
    }

}
