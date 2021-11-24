<?php
$this->isMainPage = true;
?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>

<?= $content ?>

<?php $this->endContent() ?>
