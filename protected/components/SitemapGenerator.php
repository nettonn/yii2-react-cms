<?php namespace app\components;

use app\models\Page;
use samdark\sitemap\Sitemap;
use samdark\sitemap\Index;

class SitemapGenerator
{
    public $regionId = 1;

    public $scheme = 'https://';

    protected $sitemapFilename = '/sitemap-{{alias}}.xml';

    public function generate()
    {
//        $region = Region::find()->where(['id' => $this->regionId])->active()->one();

        $sitemapFilename = DOCROOT.str_replace('{{alias}}', $region->alias, $this->sitemapFilename);

//        $sitemapIndexFilename = DOCROOT.'/sitemap_index.xml';

        $host = $this->scheme.$region->domain;

        // create sitemap
        $sitemap = new Sitemap($sitemapFilename);

        // add some URLs

        foreach(Page::find()->active()->all() as $model) {
            $sitemap->addItem($host.str_replace($host, '', $model->getUrl()), $model->updated_at, Sitemap::WEEKLY, 1);
        }

//        $regionCategoryQuery = RegionCategory::find()
//            ->select('category_id')
//            ->where(['region_id' => $this->regionId])
//            ->andWhere(['>', 'business_count', 0]);
//
//        foreach(Category::find()->andWhere(['in', 'id', $regionCategoryQuery])->active()->all() as $model) {
//            $sitemap->addItem($host.str_replace($host, '', $model->getUrl()), null, Sitemap::WEEKLY, 1);
//        }
//
//        foreach(Auto::find()->where(['region_id' => $this->regionId])->active()->all() as $model) {
//            $sitemap->addItem($host.str_replace($host, '', $model->getUrl()), $model->updated_at, Sitemap::WEEKLY, 0.9);
//        }
//
//        $autoMarksIds = Auto::find()->where(['region_id' => $this->regionId])->select('DISTINCT(auto_mark_id)')->column();
//        foreach(AutoMark::find()->where(['in', 'id', $autoMarksIds])->active()->all() as $model) {
//            $sitemap->addItem($host.str_replace($host, '', $model->getUrl()), $model->updated_at, Sitemap::WEEKLY, 0.8);
//        }
//
//        $autoModelsIds = Auto::find()->where(['region_id' => $this->regionId])->select('DISTINCT(auto_model_id)')->column();
//        foreach(AutoModel::find()->where(['in', 'id', $autoModelsIds])->active()->all() as $model) {
//            $sitemap->addItem($host.str_replace($host, '', $model->getUrl()), $model->updated_at, Sitemap::WEEKLY, 0.8);
//        }
//
//        $sitemap->addItem($host.str_replace($host, '', url(['/aggregator/default/districts'], 'https')), null, Sitemap::WEEKLY, 0.7);
//
//        foreach(District::find()->where(['region_id' => $this->regionId])->andWhere(['>', 'count_businesses', 0])->active()->all() as $model) {
//            $sitemap->addItem($host.str_replace($host, '', $model->getUrl()), null, Sitemap::WEEKLY, 0.7);
//        }
//
//        $sitemap->addItem($host.str_replace($host, '', url(['/aggregator/default/cities'], 'https')), null, Sitemap::WEEKLY, 0.7);
//
//        foreach(City::find()->where(['region_id' => $this->regionId])->andWhere(['is_main' => City::IS_MAIN_NO])->active()->all() as $model) {
//            $sitemap->addItem($host.str_replace($host, '', $model->getUrl()), null, Sitemap::WEEKLY, 0.7);
//        }
//
//        $autoIdsQuery = Auto::find()->select('id')->where(['region_id' => $this->regionId]);
//
//        $tagIds = db_query()->select('tag_id')->from('auto_tag_auto')->where(['in', 'auto_id', $autoIdsQuery])->column();
//
//        foreach(AutoTag::find()->andWhere(['in', 'id', $tagIds])->active()->all() as $model) {
//            $sitemap->addItem($host.str_replace($host, '', $model->getUrl()), $model->updated_at, Sitemap::WEEKLY, 0.5);
//        }

//        foreach(Article::find()->active()->all() as $model) {
//            $sitemap->addItem($host.$model->getUrl(), $model->updated_at, Sitemap::WEEKLY, 0.2);
//        }

        // write it
        $sitemap->write();

//        return file_get_contents($sitemapFilename);

//        // get URLs of sitemaps written
//        $sitemapFileUrls = $sitemap->getSitemapUrls($host.DS);
//
//        // create sitemap index file
//        $index = new Index($sitemapIndexFilename);
//
//        // add URLs
//        foreach ($sitemapFileUrls as $sitemapUrl) {
//            $index->addSitemap($sitemapUrl);
//        }
//
//        // write it
//        $index->write();
    }
}
