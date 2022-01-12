<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\PostSection;
use app\utils\AdminClientHelper;
use app\models\query\ActiveQuery;

class PostSectionController extends RestController
{
    public $modelClass = PostSection::class;

    public $indexQuerySelectExclude = ['content', 'description'];

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

    public function modelOptions(): array
    {
        $instance = PostSection::instance();

        return [
            'status' => AdminClientHelper::getOptionsFromKeyValue($instance->statusOptions),
            'type' => AdminClientHelper::getOptionsFromKeyValue($instance->typeOptions),
        ];
    }

}
