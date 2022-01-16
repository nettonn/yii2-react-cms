<?php
/** @var \app\models\BlockItem[] $blockItems */
/** @var \app\models\Block $block */
?>
<div class="block-widget block-widget-slider">
    <div class="block-widget__title"><?= e($block->title) ?></div>
    <?php foreach($blockItems as $blockItem): ?>
        <?php
        $image = $blockItem->fileThumbsGet('image', ['thumb', 'normal']);
        if(!$image) continue;
        ?>
        <div class="block-widget-slider__item">
            <a href="<?= e($image['normal']) ?>">
                <img src="<?= e($image['thumb']) ?>" alt="<?= e($blockItem->name) ?>">
            </a>
        </div>
    <?php endforeach ?>
</div>
