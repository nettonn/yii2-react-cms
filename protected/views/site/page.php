<?php
/* @var $this app\components\View */
/* @var $model app\models\Page */

seo()->model = $model;
$this->breadcrumbs = $model->treeGetBreadcrumbs();

$model->name = 'test test test';

dd($model->getOldAttributes());

dd($model->getAttributes());

?>

<?= $model->content ?>


