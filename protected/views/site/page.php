<?php
/* @var $this app\components\View */
/* @var $page app\models\Page */

seo()->model = $page;
$this->breadcrumbs = $page->treeGetBreadcrumbs();
?>

<?php $this->beginBlock('top-blocks'); ?>

    <?php foreach($page->topBlocks as $topBlock): ?>

        <?= \app\widgets\BlockWidget::widget(['key' => $topBlock]) ?>

    <?php endforeach ?>

<?php $this->endBlock(); ?>

<?= $page->content ?>

<?php $this->beginBlock('bottom-blocks'); ?>

    <?php foreach($page->bottomBlocks as $bottomBlock): ?>

        <?= \app\widgets\BlockWidget::widget(['key' => $bottomBlock]) ?>

    <?php endforeach ?>

<?php $this->endBlock(); ?>
