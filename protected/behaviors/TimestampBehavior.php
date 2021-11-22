<?php namespace app\behaviors;

use yii\db\BaseActiveRecord;

class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    public function init()
    {
        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdAtAttribute, $this->updatedAtAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedAtAttribute,
                BaseActiveRecord::EVENT_BEFORE_DELETE => $this->updatedAtAttribute,
            ];
        }
    }

}
