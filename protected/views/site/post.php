<?php
/* @var $this app\components\View */
/* @var $post app\models\Post */
/* @var $postSection app\models\PostSection */

seo()->model = $post;
$this->breadcrumbs = [
    ['label'=>$postSection->name, 'url'=>$postSection->getUrl()],
    ['label'=>$post->name, 'url'=>$post->getUrl()],
];
?>

<?= $post->content ?>
