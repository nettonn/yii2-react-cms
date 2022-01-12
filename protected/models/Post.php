<?php

namespace app\models;

use app\behaviors\FileBehavior;
use app\behaviors\TimestampBehavior;
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
            ->inverseOf('posts');
    }

    public function fields(): array
    {
        $fields = parent::fields();

        if($this->isRelationPopulated('images')) {
            $fields[] = 'images';
            $fields[] = 'images_id';
        }

        return $fields;
    }

    public function behaviors(): array
    {
        return [
            'TimestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
            'FileBehavior' => [
                'class' => FileBehavior::class,
                'attributes' => [
                    'images' => [
                        'multiple' => true,
                    ],
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
