<?php namespace app\widgets;

use app\components\Widget;
use app\models\Block;

class BlockWidget extends Widget
{
    /**
     * @var string|integer id or key attribute of Block model
     */
    public $key;

    protected static $cache = true;

    public function run()
    {
        if(!$this->key) return '';

        /** @var Block $block */
        $block = Block::find()->where(['or',
                ['id' => $this->key],
                ['key' => $this->key],
            ])->active()->one();

        if(!$block) return '';

        switch($block->type) {
            case Block::TYPE_SLIDER: return $this->renderSlider($block);
            case Block::TYPE_SIMPLE_GALLERY: return $this->renderSimpleGallery($block);
        }

        return '';
    }

    protected function renderSlider(Block $block)
    {
        $blockItems = $block->getBlockItems()
            ->orderSort()
            ->with('image')
            ->active()
            ->all();

        if(!$blockItems) return '';

        return $this->render('BlockWidget/slider', [
            'block' => $block,
            'blockItems' => $blockItems,
        ]);
    }

    protected function renderSimpleGallery(Block $block)
    {
        return $this->render('BlockWidget/simple-gallery', [
            'block' => $block,
        ]);
    }
}
