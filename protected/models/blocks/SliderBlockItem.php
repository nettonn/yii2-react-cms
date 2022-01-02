<?php namespace app\models\blocks;

use Yii;

class SliderBlockItem extends BlockItem
{
    const TYPE = 'slider';

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['title', 'string', 'max' => 255];
        $rules[] = ['description', 'string', 'max' => 1000];
        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['title'] = 'Заголовок';
        $labels['description'] = 'Описание';
        return $labels;
    }

    public function getFileAttributes()
    {
        return [
            'image' => [
                'multiple' => false,
                'is_image' => true,
            ],
        ];
    }

    public function getDynamicAttributes()
    {
        return [
            'title' => '',
            'description' => '',
        ];
    }

    public static function getBlockClass()
    {
        return SliderBlock::class;
    }
}
