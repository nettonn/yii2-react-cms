<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Block;
use app\models\Page;
use app\utils\AdminClientHelper;
use app\models\query\ActiveQuery;

class PageController extends RestController
{
    public $modelClass = Page::class;
    public $isTree = true;

    public $indexQuerySelectExclude = ['content', 'description', 'seo_title', 'seo_h1', 'seo_description', 'seo_keywords'];

    public $modelWith = ['images', 'blockLinks'];

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
        return [
            'status' => AdminClientHelper::getOptionsFromKeyValue($instance->statusOptions),
            'type' => AdminClientHelper::getOptionsFromKeyValue($instance->typeOptions),
            'blocks' => AdminClientHelper::getOptionsFromKeyValue($instance->blockOptions),
            'parent' => AdminClientHelper::getOptionsFromModelQuery(Page::find()->asArray()),
        ];
    }

    protected function getLastModifiedSql(): string
    {
        $pageTable = ($this->modelClass)::tableName();
        $blockTable = Block::tableName();
        return "
            SELECT MAX(updated_at) FROM (
                SELECT MAX(updated_at) AS updated_at FROM $pageTable
                UNION ALL 
                SELECT MAX(updated_at) AS updated_at FROM $blockTable
            ) a";
    }
}
