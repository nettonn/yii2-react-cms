<?php namespace app\controllers\admin;

use app\models\Redirect;
use yii\db\ActiveQuery;

class RedirectController extends RestController
{
    public $modelClass = Redirect::class;

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'to',  "$search"],
            ['like', 'from',  "$search"],
            ['like', 'code',  "$search"],
        ]);
    }

    public function modelOptions(): array
    {
        $instance = Redirect::instance();

        return [
            'status' => prepare_value_text_options($instance->statusOptions),
        ];
    }
}
