<?php namespace app\services;

use Yii;

class MailService
{
    public static function sendEmailConfirm($user)
    {
        Yii::$app->mailer->compose('email-confirm', ['user' => $user])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($user->email)
            ->setSubject('Подтвержение E-Mail для ' . Yii::$app->name)
            ->send();
    }
}