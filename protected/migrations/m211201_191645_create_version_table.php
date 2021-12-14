<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `version`.
 */
class m211201_191645_create_version_table extends Migration
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
        $this->createTable('{{%version}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name'=>$this->string()->notNull(),
            'link_type' => $this->string(128)->notNull(),
            'link_id' => $this->integer()->unsigned()->notNull(),
            'action'=>$this->string()->notNull(),
            'version_attributes' => $this->text(),
            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('idx-version-name', '{{%version}}', ['name']);
        $this->createIndex('idx-version-link_type', '{{%version}}', ['link_type']);
        $this->createIndex('idx-version-link_id', '{{%version}}', ['link_id']);
        $this->createIndex('idx-version-action', '{{%version}}', ['action']);
        $this->createIndex('idx-version-created_at', '{{%version}}', ['created_at']);
        $this->createIndex('idx-version-updated_at', '{{%version}}', ['updated_at']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%version}}');
    }
}
