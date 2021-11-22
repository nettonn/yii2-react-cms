<?php namespace app\models\forms;

use app\models\User;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
//            ['username', 'required'],
//            ['username', 'filter', 'filter'=>'trim'],
//            ['username', 'match', 'pattern' => '#^[\w_-]+$#i'],
//            ['username', 'string', 'min' => 2, 'max' => 255],

            [['email'], 'filter', 'filter'=>'trim'],
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'string', 'min' => 3, 'max' => 255],

            ['password', 'required'],
            ['password', 'string', 'min' => 5, 'max' => 255],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the username and password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', 'Неверные данные.');
            } elseif ($user && $user->status == User::STATUS_BLOCKED) {
                $this->addError('email', 'Ваш аккаунт заблокирован.');
            } elseif ($user && $user->status == User::STATUS_WAIT) {
                $this->addError('email', 'Ваш аккаунт не подтвежден.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return app()->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }

    public function attributeLabels()
    {
        return [
            'username'=>'Имя пользователя',
            'email'=>'E-Mail',
            'password'=>'Пароль',
            'rememberMe'=>'Запомнить',
        ];
    }
}