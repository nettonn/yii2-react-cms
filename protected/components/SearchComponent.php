<?php namespace app\components;

use app\models\SearchEntry;
use Wamania\Snowball\StemmerManager;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;

class SearchComponent extends Component
{
    /**
     * @var array of \yii\db\ActiveRecord models with \app\behaviors\SearchBehavior to reindex
     */
    public $indexModelClasses = [];

    protected static $stopWords = [
        'что', 'как', 'все', 'она', 'так', 'его', 'только', 'мне', 'было', 'вот',
        'меня', 'еще', 'нет', 'ему', 'теперь', 'когда', 'даже', 'вдруг', 'если',
        'уже', 'или', 'быть', 'был', 'него', 'вас', 'нибудь', 'опять', 'вам', 'ведь',
        'там', 'потом', 'себя', 'может', 'они', 'тут', 'где', 'есть', 'надо', 'ней',
        'для', 'тебя', 'чем', 'была', 'сам', 'чтоб', 'без', 'будто', 'чего', 'раз',
        'тоже', 'себе', 'под', 'будет', 'тогда', 'кто', 'этот', 'того', 'потому',
        'этого', 'какой', 'ним', 'этом', 'один', 'почти', 'мой', 'тем', 'чтобы',
        'нее', 'были', 'куда', 'зачем', 'всех', 'можно', 'при', 'два', 'другой',
        'хоть', 'после', 'над', 'больше', 'тот', 'через', 'эти', 'нас', 'про', 'них',
        'какая', 'много', 'разве', 'три', 'эту', 'моя', 'свою', 'этой', 'перед',
        'чуть', 'том', 'такой', 'более', 'всю'
    ];

    public function getStopWords()
    {
        return self::$stopWords;
    }

    public function getSearchQuery($search)
    {
        $search = trim($search);
        $search = preg_replace('~<[^>]+>~ui', ' ', $search);
        $search = preg_replace('~ё~ui', 'е', $search);
        $search = preg_replace('~(\n|\t|\r)~ui', ' ', $search);
        $search = preg_replace('~\[\*.+?\*\]~ui', ' ', $search);
        $search = preg_replace('~\&[^;\s]+?;~ui', ' ', $search);
        $search = preg_replace('~[^a-zа-яё\d]~ui', ' ', $search);
        $search = preg_replace('~\s+~u', ' ', $search);

        $words = [];
        foreach(StringHelper::explode($search, ' ', true, true) as $word) {
            $words[] = '+*'.$this->stemWord($word).'*';
        }

        $query = SearchEntry::find()->orderBy('value DESC');
        if($words) {
            return $query->where('MATCH(`content`) AGAINST (:query IN BOOLEAN MODE) = :word_count', [
                ':query' => mb_strtolower(implode(' ', $words)),
                ':word_count' => count($words),
            ]);
        }
        return false;
    }

    private $_stemmerManager;

    public function stemWord($word)
    {
        if(null === $this->_stemmerManager) {
            $this->_stemmerManager = new StemmerManager();
        }
        $result = $word;
        if(preg_match('~[а-яё]+~ui', $word)) {
            $result = $this->_stemmerManager->stem($word, 'ru');
        }

        if(preg_match('~[a-z]+~ui', $word)) {
            $result = $this->_stemmerManager->stem($word, 'en');
        }
        return $result;
    }

    public function reindex($modelClasses = [])
    {
        if(!$modelClasses) {
            $modelClasses = $this->indexModelClasses;
        }
        foreach($modelClasses as $modelClass) {
            if(!is_subclass_of($modelClass, ActiveRecord::class))
                throw new InvalidArgumentException('Model class must be subclass of \yii\db\ActiveRecord');

            foreach($modelClass::find()->all() as $model) {
                if(!$model->hasMethod('searchIndex'))
                    throw new InvalidArgumentException('Model must use \app\behaviors\SearchBehavior');

                $model->searchIndex();
            }
        }
    }

}
