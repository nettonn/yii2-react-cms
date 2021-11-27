<?php namespace app\widgets;

use app\components\Widget;
use app\models\MenuItem;
use app\utils\TreeModelHelper;
use Yii;
use yii\helpers\Html;
use yii\web\ServerErrorHttpException;

class MenuWidget extends Widget
{
    public $items;
    public $menuId;
    public $maxLevel;

    public function run()
    {
        if($this->items)
            return $this->renderMenu($this->items);

        if(!$this->menuId)
            throw new ServerErrorHttpException();

        $cacheKey = self::class.'-menuId'.$this->menuId.'-level'.$this->maxLevel;

        if(false === $items = Yii::$app->cache->get($cacheKey)) {
            $query = MenuItem::find()
                ->andWhere(['menu_id' => $this->menuId])
                ->active()
                ->orderBy('sort ASC')
                ->asArray();

            $treeModelHelper = new TreeModelHelper([
                'query' => $query,
                'maxLevel' => $this->maxLevel,
                'itemFunction' => function (array $data, TreeModelHelper $helper, $currentLevel) {
                    $item = [
                        'label' => $data['name'],
                        'url' => $data['url'],
                    ];

                    if($childrenItems = $helper->getItems($data['id'], $currentLevel + 1)) {
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

            $items = $treeModelHelper->getItems();

//            Yii::$app->cache->set($cacheKey, $items);
        }
        return $this->renderMenu($items);
    }

    protected function renderMenu($items)
    {
        return \yii\widgets\Menu::widget(['items' => $items]);
    }
}
