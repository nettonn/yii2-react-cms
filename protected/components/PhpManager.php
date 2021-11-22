<?php namespace app\components;

use Yii;
use yii\rbac\Assignment;

class PhpManager extends \yii\rbac\PhpManager
{
    public function init()
    {
        parent::init();
    }

    public function getAssignments($userId)
    {
        if(!Yii::$app->user->isGuest){
            $assignment = new Assignment;
            $assignment->userId = $userId;
            $assignment->roleName = Yii::$app->user->identity->role;
            return [$assignment->roleName => $assignment];
        }
        return [];
    }
}
