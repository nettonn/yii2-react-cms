<?php

namespace app\models;

use app\behaviors\ContentImagesBehavior;
use app\behaviors\FileBehavior;
use app\behaviors\SearchBehavior;
use app\behaviors\TagsBehavior;
use app\behaviors\TimestampBehavior;
use app\behaviors\VersionBehavior;
use app\models\base\ActiveRecord;
use app\models\query\ActiveQuery;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii2tech\ar\dynattribute\DynamicAttributeBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property string $path
 * @property string|null $description
 * @property string|null $content
 * @property string|null $data
 * @property int $section_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $status
 * @property int $is_deleted
 * @property string|null $seo_title
 * @property string|null $seo_h1
 * @property string|null $seo_keywords
 * @property string|null $seo_description
 *
 * @property PostSection $section
 * @property PostTag[] $tags
 */
class Post extends ActiveRecord
{
    const STATUS_NOT_ACTIVE = false;
    const STATUS_ACTIVE = true;

    public $statusOptions = [
        self::STATUS_NOT_ACTIVE => 'Не активно',
        self::STATUS_ACTIVE => 'Активно',
    ];

    protected $adminUrlParams = ['section_id'];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%post}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'alias'], 'required'],
            [['description', 'content'], 'string'],
            [['section_id'], 'integer'],
            [['name', 'alias', 'seo_title', 'seo_h1'], 'string', 'max' => 255],
            [['seo_keywords', 'seo_description'], 'string', 'max' => 500],
            [['status'], 'boolean'],
            [['alias'], 'filter', 'filter' => [Inflector::class, 'slug']],
            [['images_id'], 'integer', 'allowArray' => true],
            ['user_tags', 'each', 'rule' => ['string', 'max' => 255]]
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
            'path' => 'Путь',
            'description' => 'Описание',
            'content' => 'Контент',
            'data' => 'Data',
            'section_id' => 'Раздел',
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
        return 'Записи';
    }

    /**
     * Gets query for [[PostSection]].
     *
     * @return ActiveQuery
     */
    public function getSection()
    {
        return $this->hasOne(PostSection::class, ['id' => 'section_id'])
            ->inverseOf('posts');
    }

    /**
     * Gets query for [[Tags]].
     *
     * @return ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(PostTag::class, ['id' => 'tag_id'])
            ->viaTable('{{%post_tag_link}}', ['post_id' => 'id'])
            ;
    }

    public function fields(): array
    {
        $fields = parent::fields();

        if($this->isRelationPopulated('images')) {
            $fields[] = 'images';
            $fields[] = 'images_id';
        }

        if($this->isRelationPopulated('tags')) {
            $fields[] = 'user_tags';
        }

        return $fields;
    }

    public function behaviors(): array
    {
        return [
            'TimestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
            'ContentImagesBehavior' => [
                'class' => ContentImagesBehavior::class,
                'imagesAttribute' => 'content_images',
                'contentAttributes' => ['content'],
            ],
            'FileBehavior' => [
                'class' => FileBehavior::class,
                'attributes' => [
                    'content_images' => [
                        'multiple' => true,
                    ],
                    'images' => [
                        'multiple' => true,
                    ],
                ]
            ],
            'VersionBehavior' => [
                'class' => VersionBehavior::class,
                'attributes' => [
                    'name', 'alias', 'description', 'content', 'status',
                    'seo_title', 'seo_h1', 'seo_description', 'seo_keywords',
                ]
            ],
            'SearchBehavior' => [
                'class' => SearchBehavior::class,
                'attributes' => [
                    'content',
                ]
            ],
            'DynamicAttribute' => [
                'class' => DynamicAttributeBehavior::class,
                'storageAttribute' => 'data', // field to store serialized attributes
                'dynamicAttributeDefaults' => [], // default values for the dynamic attributes
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],
            'TagsBehavior' => [
                'class' => TagsBehavior::class,
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

    public function beforeSave($insert)
    {
        $this->path = $this->generatePath();
        return parent::beforeSave($insert);
    }

    public function generatePath($update = false)
    {
        $path = $this->section->alias . '/' . $this->alias;
        if($update) {
            $this->path = $path;
            $this->updateAttributes(['path' => $path]);
        }
        return $path;
    }

    public function getUrl($scheme = false): ?string
    {
        return Url::to(['/site/post', 'path' => $this->path], $scheme);
    }

    public function getLayout()
    {
        return null;
    }
}
