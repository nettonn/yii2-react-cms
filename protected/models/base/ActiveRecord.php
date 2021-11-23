<?php namespace app\models\base;

use app\models\query\ActiveQuery;
use Yii;

abstract class ActiveRecord extends \yii\db\ActiveRecord
{
    protected static $_moduleName = null;

    public $flushCache = true;

    public static $flushCacheGlobal = true;

    protected static function getModuleName()
    {
        if(static::$_moduleName === null) {
            static::$_moduleName = lcfirst(class_basename(get_called_class()));
        }
        return static::$_moduleName;
    }

    public function fields()
    {
        $fields = parent::fields();
        if($this->hasAttribute('created_at')) {
            $fields['created_at_date'] = function($model) {
                return Yii::$app->getFormatter()->asDate($model->created_at);
            };
        }

        if($this->hasAttribute('updated_at')) {
            $fields['updated_at_date'] = function($model) {
                return Yii::$app->getFormatter()->asDate($model->updated_at);
            };
        }
        return $fields;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($this->flushCache && self::$flushCacheGlobal)
            Yii::$app->getCache()->flush();
    }

    public function afterDelete()
    {
        parent::afterDelete();

        if($this->flushCache && self::$flushCacheGlobal)
            Yii::$app->getCache()->flush();
    }

    public static function find()
    {
        return new ActiveQuery(get_called_class());
    }
}
