<?php

namespace app\models;

use app\behaviors\TimestampBehavior;
use app\behaviors\TreeBehavior;
use app\models\base\ActiveRecord;
use app\models\query\ActiveQuery;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "menu_item".
 *
 * @property int $id
 * @property string $name
 * @property int $menu_id
 * @property int|null $parent_id
 * @property int $level
 * @property string $url
 * @property string|null $rel
 * @property string|null $title
 * @property int|null $sort
 * @property int $status
 * @property int $is_deleted
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Menu $menu
 * @property MenuItem[] $children
 * @property MenuItem $parent
 */
class MenuItem extends ActiveRecord
{
    const STATUS_ACTIVE = true;
    const STATUS_NOT_ACTIVE = false;

    public $statusOptions = [
        self::STATUS_ACTIVE => 'Активно',
        self::STATUS_NOT_ACTIVE => 'Не активно',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%menu_item}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'menu_id', 'url'], 'required'],
            [['menu_id', 'parent_id', 'sort'], 'integer'],
            [['name', 'url', 'rel', 'title'], 'string', 'max' => 255],
            [['status'], 'boolean'],
            [['menu_id'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::class, 'targetAttribute' => ['menu_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'menu_id' => 'Меню',
            'parent_id' => 'Родитель',
            'level' => 'Уровень вложености',
            'url' => 'Url',
            'rel' => 'Rel аттрибут',
            'title' => 'Title аттрибут',
            'sort' => 'Сортировка',
            'status' => 'Статус',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }

    public function getMenu(): ActiveQuery
    {
        return $this->hasOne(Menu::class, ['id' => 'menu_id']);
    }


    public function fields(): array
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
        if(!$this->sort && $this->menu_id) {
            $this->sort = self::find()
                ->select('MAX(sort)')
                ->where(['menu_id' => $this->menu_id, 'parent_id' => $this->parent_id])
                ->scalar()
                + 10;
        }

        return parent::beforeSave($insert);
    }
}
