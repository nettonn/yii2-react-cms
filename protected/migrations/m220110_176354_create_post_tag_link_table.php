<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post_tag_link}}`.
 */
class m220110_176354_create_post_tag_link_table extends Migration
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

        $this->createTable('{{%post_tag_link}}', [
            'post_id' => $this->integer()->unsigned()->notNull(),
            'tag_id' => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('post_tag_link-pk', '{{%post_tag_link}}', ['post_id', 'tag_id']);

        if ($this->db->driverName !== 'sqlite') {

            $this->addForeignKey(
                'fk-post_tag_link-post_id',
                '{{%post_tag_link}}',
                'post_id',
                '{{%post}}',
                'id',
                'CASCADE'
            );

            $this->addForeignKey(
                'fk-post_tag_link-tag_id',
                '{{%post_tag_link}}',
                'tag_id',
                '{{%post_tag}}',
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
                'fk-post_tag_link-post_id',
                '{{%post_tag_link}}'
            );
            $this->dropForeignKey(
                'fk-post_tag_link-tag_id',
                '{{%post_tag_link}}'
            );
        }

        $this->dropTable('{{%post_tag_link}}');
    }
}
