<?php namespace app\widgets;

use app\components\Widget;
use app\models\Menu;
use app\models\MenuItem;
use app\utils\TreeModelHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

/**
 * use:
 * \app\widgets\MenuWidget::widget(['key' => 'top_menu', 'maxLevel' => 3])
 */
class MenuWidget extends Widget
{
    public $items;
    public $key;
    public $maxLevel;

    public function run()
    {
        if($this->items)
            return $this->renderMenu($this->items);

        if(!$this->key)
            throw new InvalidConfigException('Menu $key must be set if no items');

        $cacheKey = self::class.'-menuId'.$this->key.'-level'.$this->maxLevel;

        if(false === $items = Yii::$app->cache->get($cacheKey)) {
            $menuId = Menu::find()
                ->select('id')
                ->where(['or', ['id' => $this->key], ['key' => $this->key]])
                ->active()
                ->scalar();

            if(!$menuId)
                throw new InvalidConfigException('$key must be `id` or `key` of existed active Menu record');

            $query = MenuItem::find()
                ->andWhere(['menu_id' => $menuId])
                ->active()
                ->orderBy('sort ASC')
                ->asArray();

            $treeModelHelper = new TreeModelHelper([
                'query' => $query,
                'maxLevel' => $this->maxLevel,
                'itemFunction' => function (TreeModelHelper $helper, array $data, $currentLevel) {
                    $item = [
                        'label' => $data['name'],
                        'url' => $data['url'],
                    ];

                    if($childrenItems = $helper->buildTree($data['id'], $currentLevel + 1)) {
                        $item['items'] = $childrenItems;
                    }

                    $options = [];
                    if($data['title'])
                        $options['title'] = $data['title'];
                    if($data['rel'])
                        $options['rel'] = $data['rel'];

                    if($options) {
                        $item['template'] = Html::a('{label}', '{url}', $options);
                    }
                    return $item;

            }]);

            $items = $treeModelHelper->buildTree();

            Yii::$app->cache->set($cacheKey, $items);
        }
        return $this->renderMenu($items);
    }

    protected function renderMenu($items)
    {
        return \yii\widgets\Menu::widget(['items' => $items]);
    }
}
