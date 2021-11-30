<?php
/* @var $this app\components\View */
/* @var $content string */

\app\assets\PrintAsset::register($this);
?>
<html>
<head>
    <meta charset="<?= app()->charset ?>"/>
    <title><?= e(remove_nbsp(seo('title'))) ?></title>
</head>
<body>

<?php if ($this->showH1): ?>
    <h1><?= e(seo('h1')) ?></h1>
<?php endif ?>

<?= $content ?>

<script>
    window.onload = function () {
        window.print();
    }
</script>
</body>
</html>
