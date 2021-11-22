<?php namespace app\actions\rest;

use yii\rest\Action;

class ModelDefaultsAction extends Action
{
    /**
     * @var Callback
     */
    public $modelDefaults;

    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        if($this->modelDefaults)
            return call_user_func($this->modelDefaults, $this);
        return [];
    }

}
