<?php namespace app\commands;

use app\jobs\SitemapJob;
use Yii;
use yii\console\Controller;

class MainController extends Controller
{
    public function actionSitemap()
    {
        Yii::$app->queue->push(new SitemapJob());
    }
}
