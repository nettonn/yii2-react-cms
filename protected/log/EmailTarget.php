<?php namespace app\log;

use yii\helpers\VarDumper;

class EmailTarget extends \yii\log\EmailTarget
{
    /**
     * @inheritdoc
     */
    public function export()
    {
        // moved initialization of subject here because of the following issue
        // https://github.com/yiisoft/yii2/issues/1446
        if (empty($this->message['subject'])) {
            $this->message['subject'] = 'Application Log';
        }
        $messages = array_map([$this, 'formatMessage'], $this->messages);
//        $body = wordwrap(implode("\n", $messages), 70);
        $body = implode("\n", $messages);
        $this->composeMessage($body)->send($this->mailer);
    }

}
