<?php namespace app\widgets;

use app\components\Widget;
use Yii;

class ChunkWidget extends Widget
{
    public $id;

    public function run()
    {
        return Yii::$app->chunks->get($this->id);
    }
}
