<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post}}`.
 */
class m220110_176352_create_post_table extends Migration
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
            'path' => $this->string()->notNull(),
            'description' => $this->text(),
            'content' => $this->text(),
            'data'=>$this->text(),
            'section_id' => $this->integer()->unsigned()->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'status' => $this->boolean()->notNull()->defaultValue(false),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),

            'seo_title' => $this->string(),
            'seo_h1' => $this->string(),
            'seo_keywords' => $this->string(500),
            'seo_description' => $this->string(500),
        ], $tableOptions);

        $this->createIndex('idx-post-name', '{{%post}}', 'name');
        $this->createIndex('idx-post-alias', '{{%post}}', 'alias');
        $this->createIndex('idx-post-path', '{{%post}}', 'path');
        $this->createIndex('idx-post-created_at', '{{%post}}', 'created_at');
        $this->createIndex('idx-post-updated_at', '{{%post}}', 'updated_at');
        $this->createIndex('idx-post-status', '{{%post}}', 'status');
        $this->createIndex('idx-post-is_deleted', '{{%post}}', 'is_deleted');

        if ($this->db->driverName !== 'sqlite') {

            $this->addForeignKey(
                'fk-post-section_id',
                '{{%post}}',
                'section_id',
                '{{%post_section}}',
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
                'fk-post-section_id',
                '{{%post}}'
            );
        }

        $this->dropTable('{{%post}}');
    }
}
