<?php
/* @var $this app\components\View */
/* @var $page app\models\Page */

seo()->model = $page;
$this->breadcrumbs = $page->treeGetBreadcrumbs();

function renderBlock($block) {
    switch($block) {
        default:
            return \app\widgets\BlockWidget::widget(['key' => $block]);
    }
}
?>

<?php $this->beginBlock('top-blocks'); ?>

    <?php foreach($page->topBlocks as $topBlock): ?>

        <?= renderBlock($topBlock) ?>

    <?php endforeach ?>

<?php $this->endBlock(); ?>

<?= $page->content ?>

<?php $this->beginBlock('bottom-blocks'); ?>

    <?php foreach($page->bottomBlocks as $bottomBlock): ?>

        <?= renderBlock($bottomBlock) ?>

    <?php endforeach ?>

<?php $this->endBlock(); ?>
