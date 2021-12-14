<?php namespace app\log;

use yii\helpers\VarDumper;

class EmailQueueClearTarget extends EmailQueueTarget
{
    /**
     * @inheritDoc
     */
    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text = VarDumper::export($text);
            }
        }
        $traces = [];
        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                $traces[] = "in {$trace['file']}:{$trace['line']}";
            }
        }

//        $prefix = $this->getMessagePrefix($message);
        return $text
            . (empty($traces) ? '' : "\n    " . implode("\n    ", $traces));
    }

}
