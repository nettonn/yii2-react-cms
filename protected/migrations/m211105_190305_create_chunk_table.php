<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `chunk`.
 */
class m211105_190305_create_chunk_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%chunk}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name'=>$this->string()->notNull(),
            'key'=>$this->string(),
            'type'=>$this->smallInteger()->notNull(),
            'content'=>$this->text(),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('idx-chunk-key', '{{%chunk}}', ['key']);
        $this->createIndex('idx-chunk-type', '{{%chunk}}', ['type']);
        $this->createIndex('idx-chunk-is_deleted', '{{%chunk}}', ['is_deleted']);
        $this->createIndex('idx-chunk-created_at', '{{%chunk}}', ['created_at']);
        $this->createIndex('idx-chunk-updated_at', '{{%chunk}}', ['updated_at']);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%chunk}}');
    }
}
