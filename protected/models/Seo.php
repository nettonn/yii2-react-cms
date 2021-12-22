<?php namespace app\models;

use app\behaviors\ContentImagesBehavior;
use app\behaviors\FileBehavior;
use app\behaviors\TimestampBehavior;
use app\behaviors\TreeBehavior;
use app\models\base\ActiveRecord;
use app\models\query\ActiveQuery;
use yii\helpers\Url;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "seo".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property string $url
 * @property string $title
 * @property string $h1
 * @property string $description
 * @property string $keywords
 * @property string $top_content
 * @property string $bottom_content
 * @property integer $status
 * @property integer $is_deleted
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Seo[] $children
 * @property Seo $parent
 */
class Seo extends ActiveRecord
{
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
        return '{{%seo}}';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'url'], 'required'],
            [['top_content', 'bottom_content'], 'string'],
            [['parent_id'], 'integer'],
            [['name', 'title', 'h1',], 'string', 'max' => 255],
            [['description', 'keywords'], 'string', 'max' => 500],
            [['url'], 'string', 'max' => 255],
            [['status'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'parent_id' => 'Родитель',
            'url' => 'Url',
            'title' => 'Title',
            'h1' => 'H1',
            'description' => 'Description',
            'keywords' => 'Keywords',
            'top_content' => 'Содержимое сверху',
            'bottom_content' => 'Содержимое снизу',
            'status' => 'Статус',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }

    public static function getModelLabel(): string
    {
        return 'SEO';
    }

    public function fields()
    {
        $fields = parent::fields();

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

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'TimestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
            'TreeBehavior' => [
                'class' => TreeBehavior::class
            ],
            'ContentImagesBehavior' => [
                'class' => ContentImagesBehavior::class,
                'imagesAttribute' => 'content_images',
                'contentAttributes' => ['top_content', 'bottom_content'],
            ],
            'FileBehavior' => [
                'class' => FileBehavior::class,
                'attributes' => [
                    'content_images' => [
                        'multiple' => true,
                    ],
                ]
            ],
            'SoftDeleteBehavior' => [
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

    /**
     * @inheritdoc
     */
    public function beforeSave($insert): bool
    {
        $this->url = $this->filterUrl($this->url);
        return parent::beforeSave($insert);
    }

    public function getUrl($scheme = false): string
    {
        return Url::to($this->url, $scheme);
    }

    protected function filterUrl($url): string
    {
        $url = urldecode($url);
        $url = '/'.ltrim($url, '/');

        return $url;
    }
}
