<?php namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        if (!$this->confirm("Are you sure? It will re-create permissions tree.")) {
            return self::EXIT_CODE_NORMAL;
        }
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $auth->add($admin);

        $user = $auth->createRole('user');
        $user->description = 'User';
        $auth->add($user);
    }
}
