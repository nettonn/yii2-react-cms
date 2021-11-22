<?php

namespace app\jobs;

use app\components\SitemapGenerator;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class SitemapJob extends BaseObject implements JobInterface
{
    public function execute($queue)
    {
        $sitemapGenerator = new SitemapGenerator();
        $sitemapGenerator->generate();
    }
}
