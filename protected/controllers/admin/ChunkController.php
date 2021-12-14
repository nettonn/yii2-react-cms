<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\Chunk;
use app\utils\AdminClientHelper;
use app\models\query\ActiveQuery;

class ChunkController extends RestController
{
    public $modelClass = Chunk::class;
    public $indexQuerySelectExclude = ['content'];

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'name',  "$search"],
            ['like', 'key',  "$search"],
            ['like', 'content',  "$search"],
        ]);
    }

    public function modelOptions(): array
    {
        return [
            'type' => AdminClientHelper::getOptionsFromKeyValue( Chunk::instance()->typeOptions),
        ];
    }
}
