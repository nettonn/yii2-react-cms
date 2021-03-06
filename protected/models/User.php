<?php

namespace app\models;

use app\models\base\ActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\BadRequestHttpException;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property string|null $email_confirm_token
 * @property string $role
 * @property boolean $status
 * @property int $created_at
 * @property int $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE = true;
    const STATUS_NOT_ACTIVE = false;

    public $statusOptions = [
        self::STATUS_ACTIVE => 'Активно',
        self::STATUS_NOT_ACTIVE => 'Не активно',
    ];

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    public $roleOptions = [
        self::ROLE_ADMIN => 'Администратор',
        self::ROLE_USER => 'Пользователь',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
//            ['username', 'required'],
//            ['username', 'match', 'pattern' => '#^[\w_-]+$#i'],
//            ['username', 'unique', 'targetClass' => self::class,],
//            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => self::class,],
            ['email', 'string', 'max' => 255],

            ['password', 'string', 'min' => 2, 'max' => 255],

            [['status'], 'boolean',],
            [['status'], 'validateSaveRestrictions',],
            ['role', 'safe'],
            ['role', 'validateSaveRestrictions'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'E-Mail',
            'email_confirm_token' => 'Email Confirm Token',
            'status' => 'Статус',
            'role' => 'Роль',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Пользователи';
    }

    public function fields(): array
    {
        $fields = [
            'id', 'username', 'email', 'role', 'status'
        ];
        $fields['role_text'] = function($model) {
            return $model->roleOptions[$model->role];
        };
        $fields['status_text'] = function($model) {
            return $model->statusOptions[$model->status];
        };
        $fields['created_at_date'] = function($model) {
            return Yii::$app->getFormatter()->asDate($model->created_at);
        };
        $fields['updated_at_date'] = function($model) {
            return Yii::$app->getFormatter()->asDate($model->updated_at);
        };
        return $fields;
    }

    public function validateSaveRestrictions($attribute, $params, $validator)
    {
        if($this->isNewRecord)
            return;

        if(!in_array($attribute, ['role', 'status']))
            return;

        $user = Yii::$app->getUser()->getIdentity();
        if($this->id !== $user->id)
            return;

        if($attribute === 'role' && $this->$attribute != $user->role) {
            $this->addError($attribute, 'Нельзя изменить роль у самого себя');
        }
        if($attribute === 'status' && $this->$attribute != $user->status) {
            $this->addError($attribute, 'Нельзя изменить статус у самого себя');
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function init()
    {
        if($this->getIsNewRecord()) {
            $this->status = self::STATUS_ACTIVE;
            $this->role = self::ROLE_USER;
            $this->password_hash = '';
        }

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password): bool
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    public function getPassword(): string
    {
        return '';
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function beforeDelete()
    {
        $user = Yii::$app->getUser()->getIdentity();
        if($this->id === $user->id) {
            throw new BadRequestHttpException('Нельзя удалить самого себя');
        }

        return parent::beforeDelete();
    }


    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes) {
        // Purge the user tokens when the password is changed
        if (array_key_exists('password_hash', $changedAttributes)) {
            UserRefreshToken::deleteAll(['user_id' => $this->id]);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public static function findByUsername($username)
    {
        return self::find()->where(['username' => $username]);
    }

    public static function findByEmail($email)
    {
        return self::find()->where(['email' => $email]);
    }

    public static function findIdentityByAccessToken($token, $type = null) {
        return static::find()
            ->where(['id' => (string) $token->getClaim('uid') ])
            ->active()  //adapt this to your needs
            ->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     */
    public static function findByPasswordResetToken($token)
    {
        if (!self::isPasswordResetTokenValid($token)) {
            return null;
        }
        return self::find()
            ->where(['password_reset_token' => $token,])
            ->active()
            ->one();
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->getSecurity()->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @param string $email_confirm_token
     * @return static|null
     */
    public static function findByEmailConfirmToken($email_confirm_token)
    {
        return static::findOne([
            'email_confirm_token' => $email_confirm_token,
            'status' => self::STATUS_NOT_ACTIVE
        ]);
    }

    /**
     * Generates email confirmation token
     */
    public function generateEmailConfirmToken()
    {
        $this->email_confirm_token = Yii::$app->getSecurity()->generateRandomString();
    }

    public function emailConfirm()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->email_confirm_token = null;

        return $this->save();
    }
}
