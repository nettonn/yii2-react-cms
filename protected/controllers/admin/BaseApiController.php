<?php namespace app\controllers\admin;

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
            $behaviors['corsFilter'] = FilterService::corsFilter();
        }

        $behaviors['verbFilter'] =  [
            'class' => VerbFilter::class,
            'actions' => $this->verbs(),
        ];
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::class,
        ];
        $behaviors['authenticator'] = FilterService::authenticator($this->authExcept());

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
