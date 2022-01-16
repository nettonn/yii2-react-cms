<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `block`.
 */
class m211230_181645_create_block_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%block}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name'=>$this->string()->notNull(),
            'key'=>$this->string()->notNull(),
            'type'=>$this->string()->notNull(),
            'data'=>$this->text(),
            'status' => $this->boolean()->notNull(),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('idx-block-name', '{{%block}}', ['name']);
        $this->createIndex('idx-block-key', '{{%block}}', ['key']);
        $this->createIndex('idx-block-type', '{{%block}}', ['type']);
        $this->createIndex('idx-block-status', '{{%block}}', ['status']);
        $this->createIndex('idx-block-is_deleted', '{{%block}}', ['is_deleted']);
        $this->createIndex('idx-block-created_at', '{{%block}}', ['created_at']);
        $this->createIndex('idx-block-updated_at', '{{%block}}', ['updated_at']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%block}}');
    }
}
