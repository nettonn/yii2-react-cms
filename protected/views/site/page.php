<?php
/* @var $this app\components\View */
/* @var $model app\models\Page */

seo()->model = $model;
$this->breadcrumbs = $model->treeGetBreadcrumbs();
?>

<?= $model->content ?>


