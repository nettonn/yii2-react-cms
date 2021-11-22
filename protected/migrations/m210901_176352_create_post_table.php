<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post}}`.
 */
class m210901_176352_create_post_table extends Migration
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

        $this->createTable('{{%post}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string()->notNull(),
            'alias' => $this->string()->notNull(),
            'introtext' => $this->string(),
            'content' => $this->text(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'blocks' => $this->text(),
            'status' => $this->boolean()->notNull()->defaultValue(false),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
        ], $tableOptions);

        $this->createIndex('idx-post-name', '{{%post}}', 'name');
        $this->createIndex('idx-post-alias', '{{%post}}', 'alias');
        $this->createIndex('idx-post-introtext', '{{%post}}', 'introtext');
        $this->createIndex('idx-post-created_at', '{{%post}}', 'created_at');
        $this->createIndex('idx-post-updated_at', '{{%post}}', 'updated_at');
        $this->createIndex('idx-post-status', '{{%post}}', 'status');
        $this->createIndex('idx-post-is_deleted', '{{%post}}', 'is_deleted');

        if ($this->db->driverName !== 'sqlite') {

            $this->addForeignKey(
                'fk-post-user_id',
                '{{%post}}',
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
                'fk-post-user_id',
                '{{%post}}'
            );
        }

        $this->dropIndex(
            'idx-post-name',
            '{{%post}}'
        );
        $this->dropIndex(
            'idx-post-alias',
            '{{%post}}'
        );
        $this->dropIndex(
            'idx-post-introtext',
            '{{%post}}'
        );
        $this->dropIndex(
            'idx-post-created_at',
            '{{%post}}'
        );
        $this->dropIndex(
            'idx-post-updated_at',
            '{{%post}}'
        );
        $this->dropIndex(
            'idx-post-status',
            '{{%post}}'
        );
        $this->dropIndex(
            'idx-post-is_deleted',
            '{{%post}}'
        );

        $this->dropTable('{{%post}}');
    }
}
