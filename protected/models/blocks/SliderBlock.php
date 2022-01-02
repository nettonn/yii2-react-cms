<?php namespace app\models\blocks;

use Yii;

class SliderBlock extends Block
{
    const TYPE = 'slider';

    public $has_items = true;

    public function rules()
    {
        $rules = parent::rules();

        $rules[] = ['title', 'string', 'max' => 255];

        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['title'] = 'Заголовок';
        return $labels;
    }

    public function getDynamicAttributes()
    {
        return [
            'title' => '',
        ];
    }

    public static function getBlockItemClass()
    {
        return SliderBlockItem::class;
    }
}
