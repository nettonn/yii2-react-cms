<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `search_entry`.
 */
class m211212_131645_create_search_entry_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        }
        $this->createTable('{{%search_entry}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name'=>$this->string()->notNull(),
            'link_class' => $this->string(128)->notNull(),
            'link_id' => $this->integer()->unsigned()->notNull(),
            'description'=>$this->string(),
            'content'=>$this->text(),
            'value' => $this->smallInteger()->unsigned(),
            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('idx-search_entry-name', '{{%search_entry}}', ['name']);
        $this->createIndex('idx-search_entry-link_class', '{{%search_entry}}', ['link_class']);
        $this->createIndex('idx-search_entry-link_id', '{{%search_entry}}', ['link_id']);
        $this->createIndex('idx-search_entry-value', '{{%search_entry}}', ['value']);
        $this->createIndex('idx-search_entry-created_at', '{{%search_entry}}', ['created_at']);
        $this->createIndex('idx-search_entry-updated_at', '{{%search_entry}}', ['updated_at']);

        if ($this->db->driverName === 'mysql') {
            $this->execute("ALTER TABLE {{%search_entry}} ADD FULLTEXT INDEX `idx-search_entry-content` (`content` ASC)");
        }

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%search_entry}}');
    }
}
