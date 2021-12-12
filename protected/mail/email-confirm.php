<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$confirmLink = url_abs(get_param(ADMIN_URL_PREFIX)."/auth/email-confirm/{$user->email_confirm_token}");
?>

Здравствуйте!

Для подтверждения E-Mail пройдите по ссылке:

<?= Html::a(e($confirmLink), $confirmLink) ?>

Если Вы не регистрировались на нашем сайте, то просто удалите это письмо.
