<?php

namespace app\models;

use app\models\base\ActiveRecord;
use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "queue".
 *
 * @property int $id
 * @property string $channel
 * @property resource $job
 * @property int $pushed_at
 * @property int $ttr
 * @property int $delay
 * @property int $priority
 * @property int|null $reserved_at
 * @property int|null $attempt
 * @property int|null $done_at
 */
class Queue extends ActiveRecord
{
    public $flushCache = false;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%queue}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['channel', 'job', 'pushed_at', 'ttr'], 'required'],
            [['job'], 'string'],
            [['pushed_at', 'ttr', 'delay', 'priority', 'reserved_at', 'attempt', 'done_at'], 'integer'],
            [['channel'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'channel' => 'Channel',
            'job' => 'Job',
            'pushed_at' => 'Pushed At',
            'ttr' => 'Ttr',
            'delay' => 'Delay',
            'priority' => 'Priority',
            'reserved_at' => 'Reserved At',
            'attempt' => 'Attempt',
            'done_at' => 'Done At',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['pushed_at_date'] = function($model) {
            return Yii::$app->getFormatter()->asDate($model->pushed_at);
        };
        $fields['pushed_at_datetime'] = function($model) {
            return Yii::$app->getFormatter()->asDatetime($model->pushed_at);
        };

        $fields['reserved_at_date'] = function($model) {
            return Yii::$app->getFormatter()->asDate($model->reserved_at);
        };
        $fields['reserved_at_datetime'] = function($model) {
            return Yii::$app->getFormatter()->asDatetime($model->reserved_at);
        };

        $fields['done_at_date'] = function($model) {
            return Yii::$app->getFormatter()->asDate($model->done_at);
        };
        $fields['done_at_datetime'] = function($model) {
            return Yii::$app->getFormatter()->asDatetime($model->done_at);
        };

        if($this->job) {
            $fields['job_data'] = function ($model) {
                return VarDumper::dumpAsString(unserialize($model->job));
            };
        }

        return $fields;
    }


}
