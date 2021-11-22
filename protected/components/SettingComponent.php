<?php namespace app\components;

use app\models\Setting;
use yii\base\Component;
use yii\web\ServerErrorHttpException;

class SettingComponent extends Component
{
    protected $data;

    public function init()
    {
        parent::init();

        $cacheKey = self::class.'-data';

        $this->data = \Yii::$app->getCache()->get($cacheKey);

        if(false === $this->data) {
            $models = Setting::find()->notDeleted()->all();
            foreach($models as $model) {
                if($model->key) {
                    $this->data['keys'][$model->key] = $model->id;
                }

                $this->data['value'][intval($model->id)] = $model->getValue();
            }
            \Yii::$app->getCache()->set($cacheKey, $this->data);
        }
    }

    public function get($key)
    {
        if (isset($this->data['keys'][$key]))
            $key = $this->data['keys'][$key];

        if (isset($this->data['value'][$key]))
            return $this->data['value'][$key];

        \Yii::error("Нет такого параметра {$key}");
        throw new ServerErrorHttpException("Нет такого параметра {$key}");
    }
}
