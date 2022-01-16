<?php
/* @var $this app\components\View */
/* @var $postSection app\models\PostSection */

seo()->model = $postSection;
$this->breadcrumbs = [
    ['label'=>$postSection->name, 'url'=>$postSection->getUrl()],
];
?>

<?= $postSection->content ?>
