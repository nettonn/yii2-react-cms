<?php namespace app\models\base;

use app\models\query\ActiveQuery;
use Yii;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * @method ActiveQuery hasMany($class, array $link) see [[BaseActiveRecord::hasMany()]] for more info
 * @method ActiveQuery hasOne($class, array $link) see [[BaseActiveRecord::hasOne()]] for more info
 */
abstract class ActiveRecord extends \yii\db\ActiveRecord
{
    public $flushCache = true;

    public static $flushCacheGlobal = true;

    protected $adminUrlPrefix = ADMIN_URL_PREFIX;

    public static function getModelLabel(): string
    {
        return StringHelper::basename(static::class);
    }

    public static function getModelLabelForClass($class)
    {
        if(!class_exists($class) || !is_subclass_of($class, self::class))
            return false;

        return $class::getModelLabel();
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

        if($this->hasMethod('getUrl')) {
            $fields['view_url'] = function($model) {
                return $model->getUrl();
            };
        }

        if($this->hasMethod('versionGetVersionsUrl')) {
            $fields['versions_url'] = function ($model) {
                return $model->versionGetVersionsUrl();
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

    public static function getClassNameId(): string
    {
        return Inflector::camel2id(StringHelper::basename(static::class));
    }

    public function getAdminIndexUrl($params = [], $scheme = false): string
    {
        $name = self::getClassNameId();

        return Url::to(array_merge(["{$this->adminUrlPrefix}/$name"], (array) $params), $scheme);
    }

    public function getAdminUpdateUrl($params = [], $scheme = false): string
    {
        $name = self::getClassNameId();

        return Url::to(array_merge(["{$this->adminUrlPrefix}/$name/update", 'id' => $this->id], (array) $params), $scheme);
    }

    public function getAdminUpdateUrlById($id, $params = [], $scheme = false): string
    {
        $name = self::getClassNameId();

        return Url::to(array_merge(["{$this->adminUrlPrefix}/$name/update", 'id' => $id], (array) $params), $scheme);
    }

    public function getAdminCreateUrl($params = [], $scheme = false): string
    {
        $name = self::getClassNameId();

        return Url::to(array_merge(["{$this->adminUrlPrefix}/$name/create"], (array) $params), $scheme);
    }
}
