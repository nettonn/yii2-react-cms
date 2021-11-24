<html>
<head>
    <meta charset="<?= app()->charset ?>"/>
    <title><?= e(remove_nbsp(seo('title'))) ?></title>
    <link rel="stylesheet" href="/media/css/print-t<?= filemtime(DOCROOT.'/media/css/print.css') ?>.css"/>
</head>
<body>

<? if ($this->showH1): ?>
    <h1><?= e(seo('h1')) ?></h1>
<? endif ?>

<?= $content ?>

<?= chunk_get(37) ?>

<script>
    window.onload = function () {
        window.print();
    }
</script>
</body>
</html>