<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%todo}}`.
 */
class m210901_177352_create_todo_table extends Migration
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

        $this->createTable('{{%todo}}', [
            'id' => $this->primaryKey()->unsigned(),
            'title' => $this->string()->notNull(),
            'content' => $this->text(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'sort' => $this->integer(),
            'checked' => $this->boolean()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-todo-title', '{{%todo}}', 'title');
        $this->createIndex('idx-todo-created_at', '{{%todo}}', 'created_at');
        $this->createIndex('idx-todo-updated_at', '{{%todo}}', 'updated_at');
        $this->createIndex('idx-todo-sort', '{{%todo}}', 'sort');
        $this->createIndex('idx-todo-checked', '{{%todo}}', 'checked');

        if ($this->db->driverName !== 'sqlite') {

            $this->addForeignKey(
                'fk-todo-user_id',
                '{{%todo}}',
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
                'fk-todo-user_id',
                '{{%todo}}'
            );
        }

        $this->dropIndex(
            'idx-todo-title',
            '{{%todo}}'
        );
        $this->dropIndex(
            'idx-todo-created_at',
            '{{%todo}}'
        );
        $this->dropIndex(
            'idx-todo-updated_at',
            '{{%todo}}'
        );
        $this->dropIndex(
            'idx-todo-sort',
            '{{%todo}}'
        );
        $this->dropIndex(
            'idx-todo-checked',
            '{{%todo}}'
        );

        $this->dropTable('{{%todo}}');
    }
}
