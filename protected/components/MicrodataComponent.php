<?php namespace app\components;


use yii\base\Component;

class MicrodataComponent extends Component
{
    public $jsonLd = [];

    public function getJsonLdHtml()
    {
        $html = '';

        foreach($this->jsonLd as $jsonLdObject) {
            $html .= \yii\helpers\Html::script(\yii\helpers\Json::encode($jsonLdObject), ['type' => 'application/ld+json']);
        }

        return $html;
    }

}
