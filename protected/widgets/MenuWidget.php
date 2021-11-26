<?php namespace app\widgets;

use app\components\Widget;
use app\models\MenuItem;
use Yii;
use yii\helpers\Html;
use yii\web\ServerErrorHttpException;

class MenuWidget extends Widget
{
    public $items;
    public $menuId;
    public $level;

    public function run()
    {
        if($this->items)
            return $this->renderMenu($this->items);

        if(!$this->menuId)
            throw new ServerErrorHttpException();

        $cacheKey = self::class.'-menuId'.$this->menuId.'-level'.$this->level;

        if(false === $items = Yii::$app->cache->get($cacheKey)) {
            $items = $this->getItems();

            Yii::$app->cache->set($cacheKey, $items);
        }
        return $this->renderMenu($items);
    }

    protected function renderMenu($items)
    {
        return \yii\widgets\Menu::widget(['items' => $items]);
    }

    protected function getItems($parent = 'root', $currentLevel = 1)
    {
        $data = $this->getChildrenData($parent);
        if(!$data)
            return false;

        $items = [];
        foreach($data as $one) {
            $item = [
                'label' => $one['name'],
                'url' => $one['url'],
            ];

            if((!$this->level || $this->level <= $currentLevel)
                && $childrenItems = $this->getItems($one['id'], $currentLevel + 1)) {
                $item['items'] = $childrenItems;
            }

            $options = [];
            if($one['title'])
                $options['title'] = $one['title'];
            if($one['rel'])
                $options['rel'] = $one['rel'];

            if($options) {
                $item['template'] = Html::a('{label}', '{url}', $options);
            }

            $items[] = $item;
        }
        return $items;
    }

    protected $_childrenData;

    protected function getChildrenData($parent = 'root')
    {
        if(null === $this->_childrenData) {
            $menuItems = MenuItem::find()
                ->andWhere(['menu_id' => $this->menuId])
                ->active()
                ->orderBy('sort ASC')
                ->asArray()
                ->all();

            $this->_childrenData = [];
            foreach($menuItems as $menuItem) {
                $key = $menuItem['parent_id'] ? $menuItem['parent_id'] : 'root';
                $this->_childrenData[$key][] = $menuItem;
            }
        }

        return isset($this->_childrenData[$parent])
            ? $this->_childrenData[$parent]
            : false;
    }
}
