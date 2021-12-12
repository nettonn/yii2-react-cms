<?php namespace app\log;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\Connection;
use yii\di\Instance;
use yii\helpers\StringHelper;
use yii\log\LogRuntimeException;
use yii\log\Target;
use yii\web\Response;

class DbLogTarget extends Target
{
    /**
     * @var Connection|array|string the DB connection object or the application component ID of the DB connection.
     * After the DbTarget object is created, if you want to change this property, you should only assign it
     * with a DB connection object.
     * from \yii\log\DbTarget
     */
    public $db = 'db';

    /**
     * @var string name of the DB table to store cache content. Defaults to "log".
     */
    public $logTable = '{{%log}}';

    /**
     * @var string helps separate different type of logs
     */
    public $name;

    /**
     * @var array of string patterns for \yii\helpers\BaseStringHelper::matchWildcard
     */
    public $exceptUrls = [];

    public $removeOld = true;

    public $oldTime = 2592000; // 3600 * 24 * 30

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::class);
    }

    /**
     * Save log messages to db
     * @throws LogRuntimeException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function export()
    {
        $url = CONSOLE_APP ? null : Yii::$app->getRequest()->getUrl();
        if($this->isExceptUrl($url)) {
            return;
        }

        if ($this->db->getTransaction()) {
            // create new database connection, if there is an open transaction
            // to ensure insert statement is not affected by a rollback
            $this->db = clone $this->db;
        }
        $name = $this->getLogName();
        $messages = implode("\n",array_map([$this, 'formatMessage'], $this->messages));

        $tableName = $this->db->quoteTableName($this->logTable);
        $sql = "INSERT INTO $tableName ([[name]], [[url]], [[messages]], [[created_at]])
                VALUES (:name, :url, :messages, :created_at)";
        $command = $this->db->createCommand($sql)->bindValues([
            ':name' => $name,
            ':url' => $url,
            ':messages' => $messages,
            ':created_at' => time(),
        ]);

        if (!$command->execute()) {
            throw new LogRuntimeException('Unable to export log through database!');
        }

        $this->removeOld();
    }

    protected function isExceptUrl($url): bool
    {
        if(!$url || !$this->exceptUrls)
            return false;

        foreach($this->exceptUrls as $pattern) {
            if(StringHelper::matchWildcard($pattern, $url))
                return true;
        }

        return false;
    }

    protected function getLogName(): string
    {
        if($this->name)
            return $this->name;

        if(CONSOLE_APP)
            return 'Console application log';

        $response = Yii::$app->getResponse();
        if(is_a($response, Response::class)) {
            if($response->isClientError || $response->isServerError) {
                return $response->statusCode .' error log';
            }
        }
        return 'Application log';
    }

    protected function removeOld()
    {
        if(!$this->removeOld || !$this->oldTime)
            return;

        $tableName = $this->db->quoteTableName($this->logTable);
        $sql = "DELETE FROM $tableName WHERE created_at < :created_at";

        $command = $this->db->createCommand($sql, [
            ':created_at' => time() - $this->oldTime,
        ]);

        $command->execute();
    }
}
