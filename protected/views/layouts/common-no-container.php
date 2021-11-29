<?php
/* @var $this app\components\View */
/* @var $content string */
?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>

<?= $this->render('part/breadcrumbs') ?>

    <?= $content ?>

<?php $this->endContent() ?>




