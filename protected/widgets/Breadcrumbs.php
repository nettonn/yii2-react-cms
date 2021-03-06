<?php namespace app\widgets;

use Yii;
use yii\helpers\Url;

class Breadcrumbs extends \yii\widgets\Breadcrumbs
{
    public $options = [];

    public function init()
    {
        parent::init();

        $this->homeLink = ['label'=>'Главная', 'url'=>Url::to(['/'])];
    }

    public function run()
    {
        if (empty($this->links)) {
            return;
        }

        $this->addMicrodata();

        $lastLink = array_pop($this->links);

        unset($lastLink['url']);

        $this->links[] = $lastLink;

        parent::run();
    }

    protected function addMicrodata()
    {
        Yii::$app->microdata->jsonLd['breadcrumbs'] = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => []
        ];

        $position = 1;
        Yii::$app->microdata->jsonLd['breadcrumbs']['itemListElement'][] = [
            [
                '@type' => 'ListItem',
                'position' => $position++,
                'item' =>
                    [
                        '@id' => Url::to('/', 'https'),
                        'name' => 'Выкуп авто',
                    ]
            ],
        ];
        
        foreach($this->links as $link) {
            if(!isset($link['url']) || !isset($link['label'])) {
                Yii::error('Нет ссылки или названия в Breadcrumbs для микроразметки');
                continue;
            }

            Yii::$app->microdata->jsonLd['breadcrumbs']['itemListElement'][] = [
                [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'item' =>
                        [
                            '@id' => Url::to($link['url'], 'https'),
                            'name' => $link['label']
                        ]
                ],
            ];
        }
    }
}
