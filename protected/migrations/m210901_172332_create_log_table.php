<?php namespace app\migrations;

use Yii;
use yii\db\Migration;

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
