<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post_tag}}`.
 */
class m220110_176353_create_post_tag_table extends Migration
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

        $this->createTable('{{%post_tag}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('idx-post_tag-name', '{{%post_tag}}', 'name');
        $this->createIndex('idx-post_tag-created_at', '{{%post_tag}}', 'created_at');
        $this->createIndex('idx-post_tag-updated_at', '{{%post_tag}}', 'updated_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%post_tag}}');
    }
}
