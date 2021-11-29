<?php
/* @var $this app\components\View */
/* @var $content string */
?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>

<?= $this->render('part/_breadcrumbs') ?>

    <div class="container">
        <?php if ($this->showH1 && $h1 = seo('h1')): ?>
            <h1><?= $h1 ?></h1>
        <?php endif ?>
        <?= $content ?>
    </div>

<?php $this->endContent() ?>
