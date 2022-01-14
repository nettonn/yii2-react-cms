<?php namespace app\behaviors;

use app\models\Block;
use app\models\BlockLink;
use app\models\query\ActiveQuery;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Handle blocks order on page
 */
class BlockBehavior extends Behavior
{
    public $relationName = 'blockLinks';

    public $blockLinkTable = '{{%block_link}}';

    public $defaultOptions = [
        'content' => 'Контент',
    ];

    public $defaultValue = ['content'];

    public $specialValues = ['content'];

    public $contentBlock = 'content';

    public $useCache = true;

    protected $pkAttribute;
    protected $ownerClass;

    protected $_blocks;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT  => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_UPDATE  => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * @param BaseActiveRecord $owner
     * @throws InvalidConfigException
     */
    public function attach($owner)
    {
        parent::attach($owner);

        if(!is_array($this->defaultValue) || !is_array($this->defaultOptions) || !is_array($this->specialValues)) {
            throw new InvalidConfigException('$defaultValue and $defaultOptions and $specialValues must be array');
        }

        $ownerClass = get_class($this->owner);
        if(!is_subclass_of($ownerClass, ActiveRecord::class)) {
            throw new InvalidConfigException('Attach allowed only for children of ActiveRecord');
        }

        $primaryKey = $ownerClass::primaryKey();

        if(count($primaryKey) > 1) {
            throw new InvalidConfigException('Composite primary keys not allowed');
        }
        $this->ownerClass = $ownerClass;
        $this->pkAttribute = current($primaryKey);
    }

    public function afterSave()
    {
        if(!$this->_blocks && !$this->owner->{$this->relationName})
            return;

        $blocks = array_unique($this->getBlocks());
        $currentBlocks = array_unique($this->getBlocks(true));
        if($blocks === $currentBlocks)
            return;

        BlockLink::deleteAll([
            'link_class' => $this->ownerClass,
            'link_id' => $this->owner->{$this->pkAttribute},
        ]);

        if(!$blocks)
            return;

        $blockIdsKeys = Block::Find()->select('id, LOWER(`key`) as key')->asArray()->all();
        $blockIds = $blockKeys = [];
        foreach($blockIdsKeys as $data) {
            $blockIds[] = $data['id'];
            $blockKeys[] = $data['key'];
        }

        $values = array_merge($this->specialValues, $blockIds, $blockKeys);

        $rows = [];
        $sort = 1;
        foreach($blocks as $value) {
            if(!$value)
                continue;

            $value = strtolower($value);

            if(!in_array($value, $values))
                continue;

            $row = [
                'link_class' => $this->ownerClass,
                'link_id' => $this->owner->{$this->pkAttribute},
                'value' => $value,
                'sort' => $sort++,
            ];

            $rows[] = $row;
        }
        Yii::$app->getDb()->createCommand()
            ->batchInsert($this->blockLinkTable, [
                'link_class', 'link_id', 'value', 'sort',
            ], $rows)
            ->execute();

        $this->owner->populateRelation($this->relationName, $this->getRelation()->all());
        $this->_blocks = null;
    }

    public function afterDelete()
    {
        BlockLink::deleteAll([
            'link_class' => $this->ownerClass,
            'link_id' => $this->owner->{$this->pkAttribute},
        ]);
    }

    public function getBlocks($reselect = false)
    {
        if(null === $this->_blocks || $reselect) {
            $cacheKey = self::class.'-blocks-'.($this->ownerClass).'-'.($this->owner->{$this->pkAttribute});
            if(
                !$this->useCache
                || $reselect
                || $this->owner->isRelationPopulated($this->relationName)
                || false === $result = Yii::$app->getCache()->get($cacheKey)
            ) {
                /**
                 * @var BlockLink[] $blockLinks
                 */
                $blockLinks = $this->owner->{$this->relationName};

                $result = [];
                foreach($blockLinks as $blockLink) {
                    $result[] = $blockLink->value;
                }
                if($this->useCache)
                    Yii::$app->getCache()->set($cacheKey, $result);
            }

            $this->_blocks = $result;
        }
        if(!$this->_blocks) {
            $this->_blocks = $this->defaultValue;
        }
        return $this->_blocks;
    }

    public function setBlocks($blocks)
    {
        $this->_blocks = $blocks;
    }

    public function getBlockOptions()
    {
        $blocks = Block::find()->selectOptions('id', 'name');
        return ArrayHelper::merge($this->defaultOptions, $blocks);
    }

    public function getTopBlocks()
    {
        $result = [];
        foreach($this->getBlocks() as $block) {
            if($block === $this->contentBlock)
                break;
            $result[] = $block;
        }
        return $result;
    }

    public function getBottomBlocks()
    {
        $result = [];
        $belowContent = false;
        foreach($this->getBlocks() as $block) {
            if($belowContent)
                $result[] = $block;
            if($block === $this->contentBlock)
                $belowContent = true;
        }
        return $result;
    }

    /**
     * @return ActiveQuery
     */
    protected function getRelation()
    {
        return $this->owner->hasMany(BlockLink::class, ['link_id' => $this->pkAttribute])
            ->andWhere(['link_class' => $this->ownerClass])
            ->orderBy('sort ASC');
    }

    public function canGetProperty($name, $checkVars = true)
    {
        if($this->relationName === $name) {
            return true;
        }

        return parent::canGetProperty($name, $checkVars);
    }

    public function __get($name)
    {
        if($this->relationName === $name) {
            return $this->getRelation()->findFor($this->relationName, $this->owner);
        }

        return parent::__get($name);
    }

    public function __call($name, $params)
    {
        if(strtolower('get'.$this->relationName) === strtolower($name)) {
            return $this->getRelation();
        }

        parent::__call($name, $params);
    }

    public function hasMethod($name)
    {
        if(strtolower('get'.$this->relationName) === strtolower($name)) {
            return true;
        }
        return parent::hasMethod($name);
    }
}
