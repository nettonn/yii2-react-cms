<?php namespace app\controllers\admin;

use app\models\User;
use app\services\FilterService;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class BaseApiController extends \yii\rest\Controller
{
    public function afterAction($action, $result)
    {
        if(!\Yii::$app->getUser()->isGuest) {
            \Yii::$app->admin->setIsAdminEdit(true);
        }

        return parent::afterAction($action, $result);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        $behaviors = [];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'text/html' => \yii\web\Response::FORMAT_HTML,
                'application/json' => \yii\web\Response::FORMAT_JSON,
                'application/xml' => \yii\web\Response::FORMAT_XML,
                'text/plain' => \yii\web\Response::FORMAT_RAW,
            ],
        ];

        if(DEV) {
            $behaviors['corsFilter'] = [
                'class' => \yii\filters\Cors::class,
                'cors' => [
                    'Origin' => ['http://localhost:3000'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type', 'Accept', 'X-Requested-With', 'Origin'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 3600,
                    'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page', 'X-pagination-total-count', 'X-pagination-per-page', 'X-pagination-page-count', 'X-model-options-last-modified'],
                ],
            ];
        }

        $behaviors['verbFilter'] =  [
            'class' => VerbFilter::class,
            'actions' => $this->verbs(),
        ];
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::class,
        ];
        $behaviors['authenticator'] = [
            'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
            'except' => $this->authExcept(),
            'auth' => function ($token, $authMethod) {
                $user = User::findOne($token->getClaim('uid'));
                return $user && app()->user->login($user);
            }
        ];

        return $behaviors;
    }

    protected function authExcept()
    {
        return [];
    }

    /**
     * Checks the privilege of the current user.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data model.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $model = null, $params = [])
    {
    }
}
