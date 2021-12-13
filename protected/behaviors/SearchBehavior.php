<?php namespace app\behaviors;

use app\models\SearchEntry;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\StringHelper;

/**
 * For russian and english only
 */
class SearchBehavior extends Behavior
{
    /**
     * @var string|callback attribute name of string or callback with string return
     */
    public $name = 'name';

    /**
     * @var string|callback attribute name of string or callback with string return
     */
    public $description = 'description';

    /**
     * @var array attributes to save in index
     */
    public $attributes = [];

    /**
     * @var int to sort by
     */
    public $value;

    /**
     * @var string|callback attribute name with bool value or callback with bool return
     */
    public $status = 'status';

    /**
     * @var string|callback attribute name with bool value or callback with bool return
     */
    public $isDeleted = 'is_deleted';

    /**
     * @var bool remove stop words in search content
     */
    public $removeStopWords = true;

    /**
     * @var array words to remove in content
     */
    public $additionalStopWords = [];

    /**
     * @var ActiveRecord
     */
    public $owner;

    /**
     * @var string name of \app\components\SearchComponent in Yii::$app
     */
    public $searchComponent = 'search';

    /**
     * @var string name of \app\components\PlaceholderComponent in Yii::$app
     */
    public $placeholdersComponent = 'placeholders';

    protected $ownerClass;

    protected $ownerPk;

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * @param ActiveRecord $owner
     * @throws InvalidConfigException
     */
    public function attach($owner)
    {
        if(!$this->name) {
            throw new InvalidConfigException('Attribute name and status are required');
        }

        $ownerClass = get_class($owner);
        if(!is_subclass_of($ownerClass, ActiveRecord::class)) {
            throw new InvalidConfigException('Attach allowed only for children of ActiveRecord');
        }
        $ownerPk = $ownerClass::primaryKey();

        if(count($ownerPk) > 1) {
            throw new InvalidConfigException('Composite primary keys not allowed');
        }
        $this->ownerClass = $ownerClass;
        $this->ownerPk = current($ownerPk);

        parent::attach($owner);
    }

    public function afterSave()
    {
        $this->searchIndex();
    }

    public function afterDelete()
    {
        $this->searchDeleteIndex();
    }

    public function searchIndex()
    {
        $owner = $this->owner;

        $isDeleted = false;
        if(is_callable($this->isDeleted))
            $isDeleted = call_user_func($this->isDeleted, $owner);
        elseif($owner->hasProperty($this->isDeleted))
            $isDeleted = $owner->{$this->isDeleted};

        $status = true;
        if(is_callable($this->status))
            $status = call_user_func($this->status, $owner);
        elseif($owner->hasProperty($this->status))
            $status = $owner->{$this->status};

        if(!$status || $isDeleted) {
            $this->searchDeleteIndex();
            return;
        }

        $content = '';
        foreach($this->attributes as $attribute) {
            $attributeContent = '';
            if(is_callable($attribute))
                $attributeContent = call_user_func($attribute, $owner);
            elseif($owner->hasProperty($attribute))
                $attributeContent = $owner->{$attribute};

            if(!$attributeContent)
                continue;

            if($this->placeholdersComponent && $placeholders = Yii::$app->{$this->placeholdersComponent}) {
                $attributeContent = $placeholders->removeAll($attributeContent);
            }
            $content .= trim($this->prepareContent($attributeContent)) . ' ';
        }
        $content = trim($content);

        if(!$content) {
            $this->searchDeleteIndex();
            return;
        }

        $name = $this->name;
        if(is_callable($this->name))
            $name = call_user_func($this->name, $owner);
        elseif($owner->hasProperty($this->name))
            $name = $owner->{$this->name};

        $description = $this->name;
        if(is_callable($this->description))
            $description = call_user_func($this->description, $owner);
        elseif($owner->hasProperty($this->description))
            $description = $owner->{$this->description};

        if($description && $this->placeholdersComponent && $placeholders = Yii::$app->{$this->placeholdersComponent}) {
            $description = $placeholders->removeAll($description);
        }

        $description = StringHelper::truncate($description, 255);

        $value = $this->value;
        if(is_callable($this->value))
            $value = call_user_func($this->value, $owner);

        $model = SearchEntry::find()->where([
            'link_class' => $this->ownerClass,
            'link_id' => $owner->{$this->ownerPk},
        ])->one();
        if(!$model) {
            $model = new SearchEntry();
            $model->link_class = $this->ownerClass;
            $model->link_id = $owner->{$this->ownerPk};
        }

        $model->name = $name;
        $model->description = $description;
        $model->content = $content;
        $model->value = $value;

        $model->save();
    }

    public function searchDeleteIndex()
    {
        $owner = $this->owner;
        SearchEntry::deleteAll(['link_class' => $this->ownerClass, 'link_id' => $owner->{$this->ownerPk}]);
    }

    protected function prepareContent($content)
    {
        if(!$content)
            return '';

        $content = preg_replace('~<[^>]+?>~ui', ' ', $content);
        $content = preg_replace('~ё~ui', 'е', $content);
        $content = preg_replace('~(\n|\t|\r)~ui', ' ', $content);
        $content = preg_replace('~\[\*.+?\*\]~ui', ' ', $content);
        $content = preg_replace('~&[^;\s]+?;~ui', ' ', $content);
        $content = preg_replace('~[^a-zа-я\d]~ui', ' ', $content);
        $content = preg_replace('~\s[a-zа-я\d]{1,2}\s~ui', ' ', $content);
        $content = preg_replace('~\s[a-zа-я\d]{1,2}\s~ui', ' ', $content);
        $content = preg_replace('~^[a-zа-я\d]{1,2}\s~ui', ' ', $content);
        $content = preg_replace('~\s[a-zа-я\d]{1,2}$~ui', ' ', $content);
        if($this->removeStopWords) {
            $stopWords = $this->additionalStopWords;
            if($this->searchComponent && $search = Yii::$app->{$this->searchComponent}) {
                $stopWords = array_merge($search->getStopWords(), $stopWords);
            }
            if($stopWords) {
                $stopWords = implode('|', $stopWords);
                $content = preg_replace('~\s('.$stopWords.')\s~ui', ' ', $content);
                $content = preg_replace('~^('.$stopWords.')\s~ui', ' ', $content);
                $content = preg_replace('~\s('.$stopWords.')$~ui', ' ', $content);
            }
        }
        $content = preg_replace('~\s+~u', ' ', $content);
        $content = mb_strtolower($content);
        return $content;
    }
}
