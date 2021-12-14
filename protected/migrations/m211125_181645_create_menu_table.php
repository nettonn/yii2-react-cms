<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m211125_181645_create_menu_table extends Migration
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
        $this->createTable('{{%menu}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name'=>$this->string()->notNull(),
            'key'=>$this->string()->notNull(),
            'status' => $this->boolean()->notNull(),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('idx-menu-key', '{{%menu}}', ['key']);
        $this->createIndex('idx-menu-status', '{{%menu}}', ['status']);
        $this->createIndex('idx-menu-is_deleted', '{{%menu}}', ['is_deleted']);
        $this->createIndex('idx-menu-created_at', '{{%menu}}', ['created_at']);
        $this->createIndex('idx-menu-updated_at', '{{%menu}}', ['updated_at']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%menu}}');
    }
}
