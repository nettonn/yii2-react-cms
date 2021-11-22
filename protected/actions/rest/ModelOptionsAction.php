<?php namespace app\actions\rest;

use yii\rest\Action;

class ModelOptionsAction extends Action
{
    /**
     * @var Callback
     */
    public $modelOptions;

    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        if($this->modelOptions)
            return call_user_func($this->modelOptions, $this);
        return [];
    }

}
