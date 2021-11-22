<?php namespace app\commands;

use app\jobs\SitemapJob;
use yii\console\Controller;

class MainController extends Controller
{
    public function actionSitemap()
    {
        app()->queue->push(new SitemapJob());
    }
}
