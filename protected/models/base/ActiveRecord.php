<?php namespace app\models\base;

use app\models\query\ActiveQuery;
use yii\helpers\Inflector;

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
                return app()->formatter->asDate($model->created_at);
            };
        }

        if($this->hasAttribute('updated_at')) {
            $fields['updated_at_date'] = function($model) {
                return app()->formatter->asDate($model->updated_at);
            };
        }
        return $fields;
    }

    public function defaultAttributes()
    {

    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($this->flushCache && self::$flushCacheGlobal)
            cache()->flush();
    }

    public function afterDelete()
    {
        parent::afterDelete();

        if($this->flushCache && self::$flushCacheGlobal)
            cache()->flush();
    }

    public static function getAdminIndexUrl($params = [], $scheme = false)
    {
        $class = Inflector::camel2id(class_basename(get_called_class()));
        $module = self::getModuleName();
        return url(["/{$module}/{$class}-admin/index"]+$params, $scheme);
    }

    public function getAdminViewUrl($params = [], $scheme = false)
    {
        $class = Inflector::camel2id(class_basename(get_called_class()));
        $module = self::getModuleName();
        return url(["/{$module}/{$class}-admin/view"] + ['id' => $this->id] + $params, $scheme);
    }

    public static function getAdminCreateUrl($params = [], $scheme = false)
    {
        $class = Inflector::camel2id(class_basename(get_called_class()));
        $module = self::getModuleName();
        return url(["/{$module}/{$class}-admin/create"] + $params, $scheme);
    }

    public function getAdminUpdateUrl($params = [], $scheme = false)
    {
        $class = Inflector::camel2id(class_basename(get_called_class()));
        $module = self::getModuleName();
        return url(["/{$module}/{$class}-admin/update"] + ['id' => $this->id] + $params, $scheme);
    }

    public function getAdminDeleteUrl($params = [], $scheme = false)
    {
        $class = Inflector::camel2id(class_basename(get_called_class()));
        $module = self::getModuleName();
        return url(["/{$module}/{$class}-admin/delete"] + ['id' => $this->id] + $params, $scheme);
    }

    public static function find()
    {
        return new ActiveQuery(get_called_class());
    }
}
