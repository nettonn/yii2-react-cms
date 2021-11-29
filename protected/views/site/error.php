<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

if(@$exception->statusCode === 404) {
    seo()->title = seo()->h1 = '404 - Страница не найдена.';
} else {
    seo()->title = seo()->h1 = @$exception->statusCode . ' - Произошла ошибка.';
}
?>
<?php if (@$exception->statusCode !== 404): ?>
    <?= nl2br(e($message)) ?>
<?php endif ?>
