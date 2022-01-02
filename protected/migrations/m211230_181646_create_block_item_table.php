<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `block_item`.
 */
class m211230_181646_create_block_item_table extends Migration
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
        $this->createTable('{{%block_item}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name'=>$this->string()->notNull(),
            'type'=>$this->string()->notNull(),
            'block_id'=>$this->integer()->notNull()->unsigned(),
            'data'=>$this->text(),
            'sort'=>$this->integer()->unsigned(),
            'status' => $this->boolean()->notNull(),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('idx-block_item-name', '{{%block_item}}', ['name']);
        $this->createIndex('idx-block_item-type', '{{%block_item}}', ['type']);
        $this->createIndex('idx-block_item-sort', '{{%block_item}}', ['sort']);
        $this->createIndex('idx-block_item-status', '{{%block_item}}', ['status']);
        $this->createIndex('idx-block_item-is_deleted', '{{%block_item}}', ['is_deleted']);
        $this->createIndex('idx-block_item-created_at', '{{%block_item}}', ['created_at']);
        $this->createIndex('idx-block_item-updated_at', '{{%block_item}}', ['updated_at']);

        if ($this->db->driverName !== 'sqlite') {

            $this->addForeignKey(
                'fk-block_item-block_id',
                '{{%block_item}}',
                'block_id',
                '{{%block}}',
                'id',
                'CASCADE'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        if ($this->db->driverName !== 'sqlite') {
            $this->dropForeignKey(
                'fk-block_item-block_id',
                '{{%block_item}}'
            );
        }

        $this->dropTable('{{%block_item}}');
    }
}
