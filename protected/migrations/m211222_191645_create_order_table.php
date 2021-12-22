<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `order`.
 */
class m211222_191645_create_order_table extends Migration
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
        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey()->unsigned(),
            'subject'=>$this->string()->notNull(),
            'name'=>$this->string(),
            'phone'=>$this->string(),
            'email'=>$this->string(),
            'message'=>$this->text(),
            'info'=>$this->text(),

            'url' => $this->string(500),
            'referrer' => $this->string(500),
            'entrance_page' => $this->string(500),

            'ip' => $this->string(),
            'user_agent' => $this->string(),

            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('idx-order-subject', '{{%order}}', ['subject']);
        $this->createIndex('idx-order-name', '{{%order}}', ['name']);
        $this->createIndex('idx-order-phone', '{{%order}}', ['phone']);
        $this->createIndex('idx-order-email', '{{%order}}', ['email']);
        $this->createIndex('idx-order-url', '{{%order}}', ['url']);
        $this->createIndex('idx-order-referrer', '{{%order}}', ['referrer']);
        $this->createIndex('idx-order-entrance_page', '{{%order}}', ['entrance_page']);
        $this->createIndex('idx-order-is_deleted', '{{%order}}', ['is_deleted']);
        $this->createIndex('idx-order-created_at', '{{%order}}', ['created_at']);
        $this->createIndex('idx-order-updated_at', '{{%order}}', ['updated_at']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%order}}');
    }
}
