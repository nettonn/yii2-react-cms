<?php namespace app\controllers\admin;

use app\controllers\base\BaseApiController;
use app\models\forms\LoginForm;
use app\models\forms\RegistrationForm;
use app\models\User;
use app\models\UserRefreshToken;
use Lcobucci\JWT\Token;
use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Cookie;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

class AuthController extends BaseApiController
{
    public function actionLogin()
    {
        $model = new LoginForm();

        if ($model->load(Yii::$app->getRequest()->post(), '') && $model->login()) {
            $user = Yii::$app->getUser()->identity;

            $token = $this->generateJwt($user);

            $this->generateRefreshToken($user);

            return [
                'identity' => $user,
                'token' => (string) $token,
            ];
        }
        return $model;
    }

    public function actionRegistration()
    {
        $model = new RegistrationForm();
        if ($model->load(Yii::$app->getRequest()->post(), '')) {
            if ($user = $model->register()) {
                return [
                    'message' => "Для завершения регистрации, перейдите по ссылке присланной вам на E-Mail ({$user->email}) ({$user->email_confirm_token})",
                ];
            }
        }

        return $model;
    }

    public function actionEmailConfirm()
    {
        $token = Yii::$app->getRequest()->post('token');

        if (empty($token) || !is_string($token)) {
            throw new BadRequestHttpException('Отсутствует код подтверждения.');
        }

        $user = User::findByEmailConfirmToken($token);

        if (!$user) {
            throw new BadRequestHttpException('Неверный токен.');
        }

        if ($user->emailConfirm()) {
            return [
                'message' => 'Спасибо! Ваш Email успешно подтверждён.'
            ];
        }
        return $user;
    }

    public function actionRefreshToken()
    {
        $request = Yii::$app->getRequest();
        $refreshToken = $request->getCookies()->getValue('refresh-token', false);
        if (!$refreshToken) {
            throw new UnauthorizedHttpException('Не найден refresh token.');
        }

        $userRefreshToken = UserRefreshToken::findOne(['token' => $refreshToken]);

        if ($request->getMethod() == 'POST') {
            // Getting new JWT after it has expired
            if (!$userRefreshToken) {
                throw new UnauthorizedHttpException('Refresh token не существует.');
            }

            $user = User::find()  //adapt this to your needs
                ->where(['id' => $userRefreshToken->user_id])
                ->andWhere(['status' => User::STATUS_ACTIVE])
                ->one();

            if (!$user) {
                $userRefreshToken->delete();
                throw new UnauthorizedHttpException('Пользователь не активен.');
            }

            $token = $this->generateJwt($user);

            return [
                'token' => (string) $token,
                'user' => $user,
            ];

        } elseif ($request->getMethod() == 'DELETE') {
            // Logging out
            if ($userRefreshToken && !$userRefreshToken->delete()) {
                throw new ServerErrorHttpException('Ошибка удаления токена.');
            }

            return [
                'message' => 'Вы вышли из системы'
            ];
        }
        throw new BadRequestHttpException('Пользователь не найден.');
    }

    protected function authExcept(): array
    {
        return [
            'login',
            'refresh-token',
            'email-confirm',
            'registration',
            'options',
        ];
    }

    protected function verbs(): array
    {
        return [
            'registration'  => ['POST'],
            'email-confirm'   => ['POST'],
            'login' => ['POST'],
            'refresh-token' => ['POST', 'DELETE'],
            'options' => ['OPTIONS']
        ];
    }

    private function generateJwt(User $user): Token {
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();
        $time = time();

        $jwtParams = Yii::$app->params['jwt'];

        return $jwt->getBuilder()
            ->issuedBy($jwtParams['issuer'])
            ->permittedFor($jwtParams['audience'])
            ->identifiedBy($jwtParams['id'], true)
            ->issuedAt($time)
            ->expiresAt($time + $jwtParams['expire'])
            ->withClaim('uid', $user->id)
            ->getToken($signer, $key);
    }

    /**
     * @throws \yii\base\Exception
     */
    private function generateRefreshToken(User $user): UserRefreshToken {
        $refreshToken = Yii::$app->getSecurity()->generateRandomString(200);

        $request = Yii::$app->getRequest();

        $userRefreshToken = UserRefreshToken::find()->where([
            'user_id' => $user->id,
            'ip' => $request->userIP,
            'user_agent' => $request->userAgent,
        ])->one();

        if(!$userRefreshToken) {
            $userRefreshToken = new UserRefreshToken([
                'user_id' => $user->id,
                'token' => $refreshToken,
                'ip' => $request->userIP,
                'user_agent' => $request->userAgent,
            ]);
        }

        if (!$userRefreshToken->save()) {
            throw new ServerErrorHttpException('Ошибка сохранения refresh token: '. $userRefreshToken->getErrorSummary(true));
        }

        // Send the refresh-token to the user in a HttpOnly cookie that Javascript can never read and that's limited by path
        Yii::$app->getResponse()->getCookies()->add(new Cookie([
            'name' => 'refresh-token',
            'value' => $refreshToken,
            'httpOnly' => true,
            'sameSite' => IS_SECURE && !DEV ? Cookie::SAME_SITE_LAX :  Cookie::SAME_SITE_NONE,
            'secure' => IS_SECURE,
            'path' => Url::to(['refresh-token']),  //endpoint URI for renewing the JWT token using this refresh-token, or deleting refresh-token
        ]));

        return $userRefreshToken;
    }

    public function actionOptions($action)
    {
        $verbs = $this->verbs();
        if (Yii::$app->getRequest()->getMethod() !== 'OPTIONS' || !isset($verbs[$action])) {
            Yii::$app->getResponse()->setStatusCode(405);
        }
        $headers = Yii::$app->getResponse()->getHeaders();

        $headers->set('Allow', implode(', ', $verbs[$action]));
        $headers->set('Access-Control-Allow-Methods', implode(', ', $verbs[$action]));
    }

}
