<?php namespace app\migrations;

use Yii;
use yii\db\Migration;
use yii\base\InvalidConfigException;
use yii\log\DbTarget;

/**
 * Copy of
 * yiisoft/yii2/log/migrations/m141106_185632_log_init.php
 *
 * Initializes log table.
 *
 * The indexes declared are not required. They are mainly used to improve the performance
 * of some queries about message levels and categories. Depending on your actual needs, you may
 * want to create additional indexes (e.g. index on `log_time`).
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @since 2.0.1
 */
class m210901_172332_create_log_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%log}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name'=>$this->string()->notNull(),
            'url'=>$this->string(),
            'messages' => $this->text(),
            'created_at' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('idx_log_name', '{{%log}}', 'name');
        $this->createIndex('idx_log_url', '{{%log}}', 'url');
        $this->createIndex('idx_log_created_at', '{{%log}}', 'created_at');
    }

    public function down()
    {
        $this->dropTable('{{%log}}');
    }
}
