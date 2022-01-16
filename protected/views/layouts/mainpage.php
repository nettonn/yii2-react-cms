<?php
/* @var $this app\components\View */
/* @var $content string */

$this->isMainPage = true;
?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>

<?php if(isset($this->blocks['top-blocks'])): ?>
    <?= $this->blocks['top-blocks'] ?>
<?php endif ?>

<?= $content ?>

<?php if(isset($this->blocks['bottom-blocks'])): ?>
    <?= $this->blocks['bottom-blocks'] ?>
<?php endif ?>

<?php $this->endContent() ?>
