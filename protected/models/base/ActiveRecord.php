<?php namespace app\models\base;

use app\models\query\ActiveQuery;
use Yii;

/**
 * @method ActiveQuery hasMany($class, array $link) see [[BaseActiveRecord::hasMany()]] for more info
 * @method ActiveQuery hasOne($class, array $link) see [[BaseActiveRecord::hasOne()]] for more info
 */
abstract class ActiveRecord extends \yii\db\ActiveRecord
{
    public $flushCache = true;

    public static $flushCacheGlobal = true;

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

        if($this->hasMethod('getUrl')) {
            $fields['view_url'] = function($model) {
                return $model->getUrl();
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

    public static function find(): ActiveQuery
    {
        return new ActiveQuery(get_called_class());
    }
}
