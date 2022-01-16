<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post_section}}`.
 */
class m220110_176350_create_post_section_table extends Migration
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

        $this->createTable('{{%post_section}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string()->notNull(),
            'alias' => $this->string()->notNull(),
            'description' => $this->text(),
            'content' => $this->text(),
            'type' => $this->string(),
            'data'=>$this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'status' => $this->boolean()->notNull()->defaultValue(false),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),

            'seo_title' => $this->string(),
            'seo_h1' => $this->string(),
            'seo_keywords' => $this->string(500),
            'seo_description' => $this->string(500),
        ], $tableOptions);

        $this->createIndex('idx-post_section-name', '{{%post_section}}', 'name');
        $this->createIndex('idx-post_section-alias', '{{%post_section}}', 'alias');
        $this->createIndex('idx-post_section-type', '{{%post_section}}', 'type');
        $this->createIndex('idx-post_section-created_at', '{{%post_section}}', 'created_at');
        $this->createIndex('idx-post_section-updated_at', '{{%post_section}}', 'updated_at');
        $this->createIndex('idx-post_section-status', '{{%post_section}}', 'status');
        $this->createIndex('idx-post_section-is_deleted', '{{%post_section}}', 'is_deleted');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%post_section}}');
    }
}
