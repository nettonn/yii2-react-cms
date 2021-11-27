<?php
seo()->model = $model;
$this->breadcrumbs = $model->treeGetBreadcrumbs();

echo \app\widgets\MenuWidget::widget(['menuId' => 2, 'maxLevel' => 3])

?>

<?= $model->content ?>


