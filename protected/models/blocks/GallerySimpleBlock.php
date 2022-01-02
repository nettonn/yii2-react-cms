<?php namespace app\models\blocks;

use Yii;

class GallerySimpleBlock extends Block
{
    const TYPE = 'gallery_simple';

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

    public function getFileAttributes()
    {
        return [
            'images' => [
                'multiple' => true,
                'is_image' => true,
            ],
        ];
    }

    public function getDynamicAttributes()
    {
        return [
            'title' => '',
        ];
    }

}
