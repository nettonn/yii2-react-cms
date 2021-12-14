<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_refresh_token}}`.
 */
class m210901_174344_create_user_refresh_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%user_refresh_token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'token' => $this->string(1000)->notNull(),
            'ip' => $this->string(50)->notNUll(),
            'user_agent' => $this->string(1000)->notNull(),
            'created_at' => $this->integer()->notNull()->unsigned(),
            'updated_at' => $this->integer()->notNull()->unsigned(),
        ], $tableOptions);

        $this->createIndex('idx-user_refresh_token-token', '{{%user_refresh_token}}', 'token');
        $this->createIndex('idx-user_refresh_token-created_at', '{{%user_refresh_token}}', 'created_at');
        $this->createIndex('idx-user_refresh_token-updated_at', '{{%user_refresh_token}}', 'updated_at');

        if ($this->db->driverName !== 'sqlite') {

            $this->addForeignKey(
                'fk-user_refresh_token-user_id',
                '{{%user_refresh_token}}',
                'user_id',
                '{{%user}}',
                'id',
                'CASCADE'
            );
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->db->driverName !== 'sqlite') {
            $this->dropForeignKey(
                'fk-user_refresh_token-user_id',
                '{{%user_refresh_token}}'
            );
        }

        $this->dropTable('{{%user_refresh_token}}');
    }
}
