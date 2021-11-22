<?php namespace app\models\forms;

use app\models\User;
use app\services\MailService;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class RegistrationForm extends Model
{
//    public $username;
    public $email;
    public $password;
//    public $verifyCode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            ['username', 'filter', 'filter' => 'trim'],
//            ['username', 'required'],
//            ['username', 'match', 'pattern' => '#^[\w_-]+$#i'],
//            ['username', 'unique', 'targetClass' => User::class,],
//            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class,],
            ['email', 'string', 'min' => 3, 'max' => 255],

            ['password', 'required'],
            ['password', 'string', 'min' => 5, 'max' => 255],

//            ['verifyCode', 'captcha', 'captchaAction' => '/main/default/captcha'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function register()
    {
        if ($this->validate()) {
            $user = new User();
//            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->status = User::STATUS_WAIT;
            $user->generateEmailConfirmToken();

            if ($user->save()) {
                MailService::sendEmailConfirm($user);
                return $user;
            }
            $this->addErrors($user->getFirstErrors());
        }

        return null;
    }

    public function attributeLabels()
    {
        return [
            'username'=>'Имя пользователя',
            'email'=>'E-Mail',
            'password'=>'Пароль',
            'verifyCode'=>'Защита от роботов',
        ];
    }
}