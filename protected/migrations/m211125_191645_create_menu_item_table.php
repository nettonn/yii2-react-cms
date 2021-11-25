<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `menu_item`.
 */
class m211125_191645_create_menu_item_table extends Migration
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
        $this->createTable('{{%menu_item}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name'=>$this->string()->notNull(),
            'menu_id' => $this->integer()->unsigned()->notNull(),
            'parent_id' => $this->integer()->unsigned(),
            'url'=>$this->string(255)->notNull(),
            'rel'=>$this->string(),
            'title'=>$this->string(),
            'sort'=>$this->smallInteger()->unsigned(),
            'status' => $this->boolean()->notNull(),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('idx-menu_item-url', '{{%menu_item}}', ['url']);
        $this->createIndex('idx-menu_item-sort', '{{%menu_item}}', ['sort']);
        $this->createIndex('idx-menu_item-status', '{{%menu_item}}', ['status']);
        $this->createIndex('idx-menu_item-is_deleted', '{{%menu_item}}', ['is_deleted']);
        $this->createIndex('idx-menu_item-created_at', '{{%menu_item}}', ['created_at']);
        $this->createIndex('idx-menu_item-updated_at', '{{%menu_item}}', ['updated_at']);

        if ($this->db->driverName !== 'sqlite') {

            $this->addForeignKey(
                'fk-menu_item-menu_id',
                '{{%menu_item}}',
                'menu_id',
                '{{%menu}}',
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
                'fk-menu_item-menu_id',
                '{{%menu_item}}'
            );
        }

        $this->dropTable('{{%menu_item}}');
    }
}
