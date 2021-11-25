<?php namespace app\controllers\admin;

use app\controllers\base\RestController;
use app\models\User;
use yii\db\ActiveQuery;

class UserController extends RestController
{
    public $modelClass = User::class;

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
            ['like', 'email',  "$search"],
            ['like', 'role',  "$search"],
        ]);
    }

    public function actions(): array
    {
        $actions = parent::actions();

//        unset($actions['delete']);

        return $actions;
    }

    public function modelOptions(): array
    {
        $instance = User::instance();

        return [
            'status' => prepare_value_text_options($instance->statusOptions),
            'role' => prepare_value_text_options($instance->roleOptions),
        ];
    }


}
