<?php
/* @var $this app\components\View */
/* @var $model app\models\Page */

seo()->model = $model;
$this->breadcrumbs = $model->treeGetBreadcrumbs();
?>

<?php $this->beginBlock('top-blocks'); ?>

    <?php foreach($model->topBlocks as $topBlock): ?>

        <?= \app\widgets\BlockWidget::widget(['key' => $topBlock]) ?>

    <?php endforeach ?>

<?php $this->endBlock(); ?>

<?= $model->content ?>

<?php $this->beginBlock('bottom-blocks'); ?>

    <?php foreach($model->bottomBlocks as $bottomBlock): ?>

        <?= \app\widgets\BlockWidget::widget(['key' => $bottomBlock]) ?>

    <?php endforeach ?>

<?php $this->endBlock(); ?>
