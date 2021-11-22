<?php
namespace app\components;

use app\components\Widget;

class ChunkWidget extends Widget
{
    public $id;

    public function run()
    {
        return \Yii::$app->chunks->get($this->id);
    }
}
