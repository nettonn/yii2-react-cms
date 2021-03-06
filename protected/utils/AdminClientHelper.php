<?php namespace app\utils;

use Yii;
use yii\db\ActiveQuery;

class AdminClientHelper
{
    /**
     * From:
     * [
     *      1 => 'Title 1',
     *      2 => 'Title 2'
     *      ...
     * ];
     *
     * To:
     * [
     *      ['value' => 1, 'text' => 'Title 1'],
     *      ['value' => 2, 'text' => 'Title 2'],
     *      ...
     * ];
     *
     * @param $array
     * @param string $returnValueParam
     * @param string $returnTextParam
     * @return array
     */
    public static function getOptionsFromKeyValue($array, string $returnValueParam = 'value', string $returnTextParam = 'text'): array
    {
        $options = [];
        foreach($array as $value => $text) {
            $options[] = [$returnValueParam => $value, $returnTextParam => $text];
        }
        return $options;
    }

    public static function getOptionsFromModelQuery(ActiveQuery $query, $valueParam = 'id', $titleParam = 'name', $parentParam = 'parent_id'): array
    {
        $cacheKey = self::class.'-sql:'.$query->createCommand()->getRawSql().'-valueParam:'.$valueParam.'-titleParam:'.$titleParam.'-parentParam:'.$parentParam;
        if(false === $options = Yii::$app->cache->get($cacheKey)) {
            $treeModelHelper = new TreeModelHelper([
                'query' => $query,
                'parentAttribute' => $parentParam,
                'itemFunction' => function(TreeModelHelper $helper, array $data, int $currentLevel) use ($valueParam, $titleParam) {
                    $item = [
                        'key' => intval($data[$valueParam]),
                        'title' => $data[$titleParam],
                        'value' => intval($data[$valueParam]),
                    ];
                    if($childrenItems = $helper->buildTree($data['id'], $currentLevel + 1)) {
                        $item['children'] = $childrenItems;
                    }
                    return $item;
            }]);

            $options = $treeModelHelper->buildTree();


            Yii::$app->cache->set($cacheKey, $options);
        }
        return $options;
    }
}
