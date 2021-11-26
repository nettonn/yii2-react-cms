<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;

//AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= app()->charset ?>">
    <title><?= e(remove_nbsp(seo('title'))) ?></title>
    <meta name="description" content="<?= e(seo('desc')) ?>">
    <meta name="keywords" content="<?= e(seo('key')) ?>">
    <link rel="canonical" href="<?= e(seo()->getCanonicalUrl()) ?>">
    <meta name="robots" content="noyaca">
    <?php if (seo()->noindex): ?>
        <meta name="robots" content="noindex">
    <?php endif ?>
    <?php if (!seo()->noindex && seo()->noindexGoogle): ?>
        <meta name="googlebot" content="noindex">
    <?php endif ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="<?= $this->bodyClass ?>
    <?= DEV ? 'dev' : '' ?>
    <?= $this->isMainPage ? 'main-page' : 'inner-page' ?>
">
<?php $this->beginBody() ?>

<?= $this->render('//layouts/part/header') ?>

<main role="main">
    <?= $content ?>
</main>

<?= $this->render('//layouts/part/footer') ?>

<?= chunk_get('counters') ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
