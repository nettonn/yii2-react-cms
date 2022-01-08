<?php

namespace app\models;

use app\models\base\ActiveRecord;
use Yii;

/**
 * This is the model class for table "block_link".
 *
 * @property string $link_class
 * @property int $link_id
 * @property string $value id|key of Block model or special words like "content"
 * @property int|null $sort
 *
 * @property Block $block
 */
class BlockLink extends ActiveRecord
{
    public $flushCache = false;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'block_link';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['link_class', 'link_id', 'value'], 'required'],
            [['link_id', 'sort'], 'integer'],
            [['link_class'], 'string', 'max' => 128],
            [['value'], 'string', 'max' => 50],
            [['link_class', 'link_id', 'value'], 'unique', 'targetAttribute' => ['link_class', 'link_id', 'value']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'link_class' => 'Link Class',
            'link_id' => 'Link ID',
            'sort' => 'Sort',
            'value' => 'Value',
        ];
    }
}
