<?php

namespace app\models;

use app\behaviors\FileBehavior;
use app\behaviors\TimestampBehavior;
use app\models\base\ActiveRecord;
use app\models\query\ActiveQuery;
use Yii;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "post_section".
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property string|null $description
 * @property string|null $content
 * @property string|null $type
 * @property string|null $data
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $status
 * @property int $is_deleted
 * @property string|null $seo_title
 * @property string|null $seo_h1
 * @property string|null $seo_keywords
 * @property string|null $seo_description
 *
 * @property Post[] $posts
 */
class PostSection extends ActiveRecord
{
    const STATUS_NOT_ACTIVE = false;
    const STATUS_ACTIVE = true;

    public $statusOptions = [
        self::STATUS_NOT_ACTIVE => 'Не активно',
        self::STATUS_ACTIVE => 'Активно',
    ];

    public $typeOptions = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%post_section}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'alias'], 'required'],
            [['description', 'content'], 'string'],
            [['status'], 'boolean'],
            [['name', 'alias', 'type', 'seo_title', 'seo_h1'], 'string', 'max' => 255],
            [['seo_keywords', 'seo_description'], 'string', 'max' => 500],
            [['alias'], 'filter', 'filter' => [Inflector::class, 'slug']],
            [['alias'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'alias' => 'Псевдоним',
            'description' => 'Описание',
            'content' => 'Контент',
            'type' => 'Тип',
            'data' => 'Data',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
            'status' => 'Статус',
            'is_deleted' => 'Is Deleted',
            'seo_title' => 'Seo Title',
            'seo_h1' => 'Seo H1',
            'seo_keywords' => 'Seo Keywords',
            'seo_description' => 'Seo Description',
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Разделы записей';
    }

    /**
     * Gets query for [[Posts]].
     *
     * @return ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, ['section_id' => 'id'])
            ->inverseOf('section');
    }

    public function behaviors(): array
    {
        return [
            'TimestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],
        ];
    }

    public function init()
    {
        if($this->getIsNewRecord())
        {
            $this->status = self::STATUS_NOT_ACTIVE;
        }
        parent::init();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        foreach ($this->posts as $post) {
            $post->generatePath(true);
        }
    }

    public function getUrl($scheme = false): ?string
    {
        return Url::to(['/site/post-section', 'alias' => $this->alias], $scheme);
    }

    public function getLayout()
    {
        return null;
    }
}
