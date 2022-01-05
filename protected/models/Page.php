<?php namespace app\models;

use app\behaviors\ContentImagesBehavior;
use app\behaviors\FileBehavior;
use app\behaviors\SearchBehavior;
use app\behaviors\TimestampBehavior;
use app\behaviors\VersionBehavior;
use app\models\base\ActiveRecord;
use app\models\query\ActiveQuery;
use app\traits\ModelType;
use Yii;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii2tech\ar\dynattribute\DynamicAttributeBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use app\behaviors\TreeBehavior;

/**
 * This is the model class for table "page".
 *
 * @property integer $id
 * @property string $name
 * @property string $alias
 * @property string $_url
 * @property string $path
 * @property integer $parent_id
 * @property integer $level
 * @property string $description
 * @property string $content
 * @property string $data
 * @property string $type
 * @property boolean $status
 * @property integer $is_deleted
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $seo_title
 * @property string $seo_h1
 * @property string $seo_keywords
 * @property string $seo_description
 *
 * @property Page[] $children
 * @property Page $parent
 */
class Page extends ActiveRecord
{
    use ModelType;

    const TYPE_COMMON = 'common';
    const TYPE_MAIN = 'main';

    public $typeOptions = [
        self::TYPE_COMMON => 'Общий',
        self::TYPE_MAIN => 'Главная',
    ];

    const STATUS_ACTIVE = true;
    const STATUS_NOT_ACTIVE = false;

    public $statusOptions = [
        self::STATUS_ACTIVE => 'Активно',
        self::STATUS_NOT_ACTIVE => 'Не активно',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%page}}';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        $rules = [
            [['name', 'alias'], 'required'],
            [['parent_id'], 'number', 'skipOnEmpty' => true],
            [['parent_id'], 'safe'],
            [['description', 'content'], 'string'],
            [['name', 'alias', 'type', 'seo_title', 'seo_h1'], 'string', 'max' => 255],
            [['seo_keywords', 'seo_description'], 'string', 'max' => 500],
            [['status'], 'boolean',],
            [['alias'], 'filter', 'filter'=>[Inflector::class, 'slug']],
            [['images_id'], 'integer', 'allowArray' => true],
        ];
        return array_merge($rules, $this->getTypeRules());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        $labels = [
            'id' => 'ID',
            'name' => 'Название',
            'alias' => 'Псевдоним',
            '_url' => 'Url',
            'path' => 'Path',
            'parent_id' => 'Родитель',
            'level' => 'Level',
            'description' => 'Описание',
            'content' => 'Содержимое',
            'type' => 'Тип',
            'status' => 'Статус',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
            'seo_title' => 'Seo Title',
            'seo_h1' => 'Seo H1',
            'seo_description' => 'Seo Description',
            'seo_keywords' => 'Seo Keywords',
            'image'=>'Главное изображение',
            'images'=>'Изображения',
        ];

        return array_merge($labels, $this->getTypeAttributeLabels());
    }

    public static function getModelLabel(): string
    {
        return 'Страницы';
    }

    public function fields(): array
    {
        $fields = parent::fields();

        if($this->isRelationPopulated('images')) {
            $fields[] = 'images_id';
        }

        foreach($this->getTypeFileAttributes() as $attribute => $params) {
            $fields[] = $params['attribute_id'] ?? $attribute.'_id';
        }

        foreach($this->getTypeDynamicAttributes() as $name => $defaultValue) {
            $fields[] = $name;
        }

        if($this->isRelationPopulated('children') && $this->children) {
            $fields[] = 'children';
        }

        return $fields;
    }

    public function getParent(): ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'parent_id']);
    }

    public function getChildren(): ActiveQuery
    {
        return $this->hasMany(self::class, ['parent_id'=>'id']);
    }

    protected function configureTypes()
    {
        return [
            self::TYPE_COMMON => [],
            self::TYPE_MAIN => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = [
            'TimestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
            'TreeBehavior' => [
                'class' => TreeBehavior::class
            ],
            'ContentImagesBehavior' => [
                'class' => ContentImagesBehavior::class,
                'imagesAttribute' => 'content_images',
                'contentAttributes' => ['content'],
            ],
            'FileBehavior' => [
                'class' => FileBehavior::class,
                'attributes' => array_merge(
                    [
                        'content_images' => [
                            'multiple' => true,
                        ],
                        'images' => [
                            'multiple' => true,
                        ],
                    ],
                    $this->getTypeFileAttributes()
                ),
            ],
            'VersionBehavior' => [
                'class' => VersionBehavior::class,
                'attributes' => [
                    'name', 'alias', 'parent_id', 'description', 'content', 'type', 'status',
                    'seo_title', 'seo_h1', 'seo_description', 'seo_keywords',
                ]
            ],
            'SoftDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],
            'SearchBehavior' => [
                'class' => SearchBehavior::class,
                'attributes' => [
                    'content',
                ]
            ]
        ];

        if($dynamicAttributes = $this->getTypeDynamicAttributes()) {
            $behaviors['DynamicAttribute'] = [
                'class' => DynamicAttributeBehavior::class,
                'storageAttribute' => 'data', // field to store serialized attributes
                'dynamicAttributeDefaults' => $dynamicAttributes, // default values for the dynamic attributes
            ];
        }

        return $behaviors;
    }

    public function init()
    {
        if($this->getIsNewRecord())
        {
            $this->status = self::STATUS_NOT_ACTIVE;
            $this->type = self::TYPE_COMMON;
        }

        parent::init();
    }

    public function generateUrl(): string
    {
        if(Yii::$app->settings->get('main_page_id') == $this->id)
            return Url::to(['/site/index']);
        return Url::to(['/site/page', 'path'=>$this->treeGetPath()]);
    }

    public function getUrl($scheme = false): ?string
    {
        return Url::to($this->_url, $scheme);
    }

}
