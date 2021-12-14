<?php

namespace app\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class MailJob extends BaseObject implements JobInterface
{
    public $emailTo;
    public $subject;
    public $htmlBody;
    public $textBody;
    public $attachments;

    public function execute($queue)
    {
        $mail = Yii::$app->mailer
            ->compose()
            ->setSubject($this->subject)
            ->setHtmlBody($this->htmlBody)
            ->setTextBody($this->textBody);

        if($this->attachments) {
            foreach($this->attachments as $file) {
                $mail->attach($file);
            }
        }

        $mail->setTo($this->emailTo);
        $result = $mail->send();

        if(!$result)
            throw new \Exception();
    }
}
