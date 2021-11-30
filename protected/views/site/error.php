<?php
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$statusCode = property_exists($exception, 'statusCode') ? $exception->statusCode : false;

if($statusCode === 404) {
    seo()->title = seo()->h1 = '404 - Страница не найдена.';
} else {
    seo()->title = seo()->h1 = $statusCode . ' - Произошла ошибка.';
}
?>
<?php if ($statusCode !== 404): ?>
    <?= nl2br(e($message)) ?>
<?php endif ?>
