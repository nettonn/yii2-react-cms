<?php
seo()->model = $model;
$this->breadcrumbs = $model->treeGetBreadcrumbs();

echo \app\widgets\MenuWidget::widget(['key' => 'top_menu', 'maxLevel' => 3])

?>

<?= $model->content ?>


