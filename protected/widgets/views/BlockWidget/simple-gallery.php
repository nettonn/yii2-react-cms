<?php
/** @var \app\models\Block $block */

$images = $block->filesThumbsGet('images', ['thumb', 'normal']);
if(!$images) return;
?>
<div class="block-widget block-widget-simple-gallery">
    <div class="block-widget__title"><?= e($block->title) ?></div>
    <?php foreach($images as $image): ?>
        <div class="block-widget-simple-gallery__item">
            <a href="<?= e($image['normal']) ?>">
                <img src="<?= e($image['thumb']) ?>" alt="<?= e($block->title) ?>">
            </a>
        </div>
    <?php endforeach ?>
</div>

