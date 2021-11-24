<?php
seo()->model = $model;
$this->breadcrumbs = $model->treeGetBreadcrumbs();
?>

<?= $model->content ?>


