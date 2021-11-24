<?php namespace app\components;

use app\models\Chunk;
use Yii;
use yii\base\Component;

class ChunkComponent extends Component
{
    protected $data;

    public function init()
    {
        parent::init();

        $cacheKey = self::class.'-data';

        $this->data = Yii::$app->getCache()->get($cacheKey);

        if(false === $this->data) {
            $models = Chunk::find()->notDeleted()->all();
            foreach($models as $model) {
                if($model->key) {
                    $this->data['keys'][$model->key] = $model->id;
                }
                $this->data['content'][$model->id] = strval($model->content);
            }
            Yii::$app->getCache()->set($cacheKey, $this->data);
        }
    }

    public function get($key)
    {
        if (isset($this->data['keys'][$key]))
            $key = $this->data['keys'][$key];

        if (isset($this->data['content'][$key]))
            return $this->data['content'][$key];
        Yii::warning("Нет такого чанка {$key}");
        return '';
    }
}
