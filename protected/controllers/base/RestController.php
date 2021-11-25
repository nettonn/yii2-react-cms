<?php namespace app\controllers\base;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

abstract class RestController extends BaseApiController
{
    public $modelWith;
    public $indexModelWith;
    public $idsParam = 'ids';
    public $sortFieldParam = 'sortField';
    public $sortDirectionParam = 'sortDirection';
    public $searchQueryParam = 'search';
    public $pageSize = 20;
    public $modelOptionsLastModifiedActions = [
        'index',
        'view',
        'create',
        'update',
        'delete',
        'model-defaults',
    ];
    public $indexQuerySelect; // columns to select on index action
    public $indexQuerySelectExclude; // array of columns to exclude from select on index action
    public $isTree = false;

    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass;
    /**
     * @var string the scenario used for updating a model.
     * @see \yii\base\Model::scenarios()
     */
    public $updateScenario = Model::SCENARIO_DEFAULT;
    /**
     * @var string the scenario used for creating a model.
     * @see \yii\base\Model::scenarios()
     */
    public $createScenario = Model::SCENARIO_DEFAULT;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->modelClass === null) {
            throw new InvalidConfigException('The "modelClass" property must be set.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function authExcept(): array
    {
        return ['options'];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'prepareDataProvider' => [$this, 'prepareDataProvider'],
            ],
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'findModel' => [$this, 'findModel'],
            ],
            'create' => [
                'class' => 'yii\rest\CreateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
            'update' => [
                'class' => 'yii\rest\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
                'findModel' => [$this, 'findModel'],
            ],
            'delete' => [
                'class' => 'app\actions\rest\DeleteAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
            'model-options' => [
                'class' => 'app\actions\rest\ModelOptionsAction',
                'modelClass' => $this->modelClass,
                'modelOptions' => [$this, 'modelOptions'],
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'model-defaults' => [
                'class' => 'app\actions\rest\ModelDefaultsAction',
                'modelClass' => $this->modelClass,
                'modelDefaults' => [$this, 'modelDefaults'],
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
            'model-options' => ['GET', 'HEAD'],
            'model-defaults' => ['GET', 'HEAD'],
        ];
    }

    protected function getModelQuery() : ActiveQuery
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;

        /* @var $query ActiveQuery */
        $query = $modelClass::find();

        if($modelClass::getTableSchema()->getColumn('is_deleted')) {
            $query = $query->andWhere(['is_deleted' => false]);
        }

        if($this->modelWith) {
            $query = $query->with($this->modelWith);
        }

        return $query;
    }

    protected function getListModelQuery() : ActiveQuery
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;

        /* @var $query ActiveQuery */
        $query = $modelClass::find();

        if($modelClass::getTableSchema()->getColumn('is_deleted')) {
            $query = $query->andWhere(['is_deleted' => false]);
        }

        if($this->indexModelWith) {
            $query = $query->with($this->indexModelWith);
        }

        return $query;
    }

    public function findModel($id) : ?ActiveRecord
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        $keys = $modelClass::primaryKey();
        if (count($keys) > 1) {
            $values = explode(',', $id);
            if (count($keys) === count($values)) {
                $model = $this->getModelQuery()->where(array_combine($keys, $values))->one();
            }
        } elseif ($id !== null) {
            $model = $this->getModelQuery()->where([$keys[0] => $id])->one();
        }

        if (isset($model)) {
            return $model;
        }
        return null;
    }

    protected function prepareQuery(ActiveQuery $query): ActiveQuery
    {
        return $query;
    }

    protected function prepareSearchQuery(ActiveQuery $query, string $search) : ActiveQuery
    {
        return $query->andWhere(['or',
            ['like', 'id',  "$search"],
        ]);
    }

    public function prepareDataProvider()
    {
        $request = Yii::$app->getRequest();
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        $modelColumns = $modelClass::getTableSchema()->getColumnNames();

        /** @var ActiveQuery $query */
        $query = $this->getListModelQuery();

        if($this->indexQuerySelect) {
            $query = $query->select($this->indexQuerySelect);
        } elseif($this->indexQuerySelectExclude) {
            $select = array_diff($modelColumns, $this->indexQuerySelectExclude );
            if($select)
                $query = $query->select($select);
        }


        $query = $this->prepareQuery($query);

        if($ids = $request->get($this->idsParam)) {
            if(!is_array($ids))
                $ids = explode(',', $ids);

            return $query->andWhere(['in', 'id', $ids])->all();
        }

        $search = $request->get($this->searchQueryParam);
        if($search) {
            $query = $this->prepareSearchQuery($query, $search);
        }

        $filterWhere = [];
        $filters = (array) $request->get('filters');
        if($filters) {
            foreach($filters as $column => $value) {
                if(!in_array($column, $modelColumns))
                    continue;

                $filterWhere[] = ['in', $column, $value];
            }

            if($filterWhere) {
                $query = $query->andWhere(array_merge(['and'], $filterWhere));
            }
        }

        $directionConst = $request->get($this->sortDirectionParam, 'ascend') == 'ascend' ? SORT_ASC : SORT_DESC;


        $sortField = $sort = $request->get($this->sortFieldParam);

        if(!in_array($sort, $modelColumns)) {
            $sort = 'id';
        }

        $pagination = [
            'pageParam' => 'page',
            'pageSize' => $this->pageSize,
        ];
        
        $isList = $request->get('list');

        if($isList) {
            if($limit = $request->get('limit')) {
                $query = $query->limit(intval($limit));
            }

            $pagination = false;
        } 
        
        if($this->isTree && !$filters && !$isList && !$search && !$sortField) {
            $query->andWhere('parent_id IS NULL OR parent_id = 0');
            $with = $this->indexModelWith? array_merge($this->indexModelWith, ['children']) : ['children'];
            $query->with($with);
            $modelClass::$childrenWith = $with;
//            $pagination = false;
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'sortParam' => false,
                'defaultOrder' => [$sort => $directionConst]
            ],
            'pagination' => $pagination
        ]);
    }

    public function modelOptions(): array
    {
        return [];
    }

    public function modelDefaults()
    {
        /** @var ActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        return $modelClass::instance();
    }

    /**
     * {@inheritdoc}
     */
    public function afterAction($action, $result)
    {
        $this->addHeaderModelOptionsLastModified($action);

        return parent::afterAction($action, $result);
    }

    protected function addHeaderModelOptionsLastModified(Action $action)
    {
        if (in_array($action->id, $this->modelOptionsLastModifiedActions)) {
            if($lastModified = $this->getModelOptionsLastModified()) {
                Yii::$app->getResponse()->getHeaders()->add('x-model-options-last-modified', $lastModified);
            }
        }
    }

    /**
     * For yii\caching\DbDependency
     * @return string sql
     */
    protected function getModelLastModifiedSql()
    {
        $modelClass = $this->modelClass;
        return 'SELECT MAX(updated_at) from '.$modelClass::tableName();
    }

    /**
     * Timestamp in seconds
     * @return int | null
     */
    protected function getModelOptionsLastModified()
    {
        $modelClass = $this->modelClass;
        return $modelClass::find()->select('MAX(updated_at)')->scalar();
    }

}
