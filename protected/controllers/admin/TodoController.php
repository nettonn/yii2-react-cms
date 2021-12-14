<?php namespace app\controllers\admin;


use app\controllers\base\RestController;
use app\models\Todo;
use Yii;
use yii\base\InvalidArgumentException;
use yii\data\ActiveDataProvider;

class TodoController extends RestController
{
    public $modelClass = Todo::class;

    public function actionSort()
    {
        $sortedIds = get_post('sorted');
        if(!$sortedIds) {
            throw new InvalidArgumentException();
        }

        $sortedIds = array_values($sortedIds);
//        $sortedIds = array_reverse($sortedIds);
        $sortedIds = array_map('intval', $sortedIds);

        $models = Todo::find()
            ->where(['user_id' => Yii::$app->getUser()->id])
            ->andWhere(['in', 'id', $sortedIds])
            ->indexBy('id')
            ->all();
        foreach($sortedIds as $key => $id) {
            if(isset($models[$id])) {
                $model = $models[$id];
                $model->sort = $key+1;
                $model->save();
            }
        }

        return ['success' => true];
    }

//    public function prepareDataProvider()
//    {
//        $todoQuery = Todo::find();
//
//        $todoQuery->andWhere(['user_id' => app()->user->id]);
//
//        $search = get_get('search');
//        if($search) {
//            $todoQuery->andWhere(['or',
//                ['like', 'id',  "$search"],
//                ['like', 'title',  "$search"],
//            ]);
//        }
//
//        return new ActiveDataProvider([
//            'query' => $todoQuery,
//            'sort' => [
//                'sortParam' => false,
//                'defaultOrder' => ['sort' => SORT_ASC]
//            ],
//            'pagination' => [
//                'pageParam' => 'page',
//                'pageSize' => 9999,
//            ]
//        ]);
//    }
//
//    public function actions()
//    {
//        $actions = parent::actions();
//
//        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
//
//        return $actions;
//    }

    protected function verbs(): array
    {
        $verbs = parent::verbs();
        $verbs['sort'] = ['PUT'];
        return  $verbs;
    }


}
