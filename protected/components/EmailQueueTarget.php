<?php namespace app\components;

use app\modules\error\models\NotFound;
use app\jobs\MailJob;

class EmailQueueTarget extends \yii\log\EmailTarget
{
    /**
     * @inheritdoc
     */
    public function export()
    {
        if(!CONSOLE_APP && !NotFound::isLoggable()) {
            return;
        }

        // moved initialization of subject here because of the following issue
        // https://github.com/yiisoft/yii2/issues/1446
        if (empty($this->message['subject'])) {
            $this->message['subject'] = 'Application Log';
        }
        $messages = array_map([$this, 'formatMessage'], $this->messages);
//        $body = wordwrap(implode("\n", $messages), 70);
        $body = implode("\n", $messages);

        $mail = $this->composeMessage($body);

        \Yii::$app->queue->push(new MailJob([
            'emailTo' => $mail->getTo(),
            'subject'=>$mail->getSubject(),
            'textBody'=>$body,
        ]));
    }
}
