<?php namespace app\components;

use Yii;
use yii\db\BaseActiveRecord;

class Widget extends \yii\base\Widget
{
    protected static $cache = false;

    public static function widget($config = [])
    {
        if(!static::$cache)
            return parent::widget($config);

        $key = self::getCacheKey($config);

        $data = Yii::$app->getCache()->get($key);
        if ($data === false) {
            $data = parent::widget($config);
            Yii::$app->getCache()->add($key, $data);
        }
        return $data;
    }

    protected static function getCacheKey($config)
    {
        $newConfig = [];

        foreach($config as $param => $value) {
            if(is_object($value) && is_a($value, BaseActiveRecord::class)) {
                $newConfig[$param] = $value->getPrimaryKey();
            } else {
                $newConfig[$param] = $value;
            }
        }

        return 'widget.'.get_called_class().'.'.md5(serialize($newConfig));
    }
}
