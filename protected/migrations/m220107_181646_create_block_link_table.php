<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `block_link`.
 */
class m220107_181646_create_block_link_table extends Migration
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
        $this->createTable('{{%block_link}}', [
            'link_class' => $this->string(128)->notNull(),
            'link_id' => $this->integer()->unsigned()->notNull(),
            'value' => $this->string(50)->notNull(),
            'sort'=>$this->integer()->unsigned(),
        ], $tableOptions);

        $this->addPrimaryKey('block_link-pk', '{{%block_link}}', ['link_class', 'link_id', 'value']);

        $this->createIndex('idx-block_link-link_class', '{{%block_link}}', ['link_class']);
        $this->createIndex('idx-block_link-link_id', '{{%block_link}}', ['link_id']);
        $this->createIndex('idx-block_link-value', '{{%block_link}}', ['value']);
        $this->createIndex('idx-block_link-sort', '{{%block_link}}', ['sort']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%block_link}}');
    }
}
