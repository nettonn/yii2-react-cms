<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `seo`.
 */
class m211125_171645_create_seo_table extends Migration
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
        $this->createTable('{{%seo}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name'=>$this->string()->notNull(),
            'parent_id' => $this->integer()->unsigned(),
            'level' => $this->smallInteger()->unsigned()->notNull(),
            'url'=>$this->string(255)->notNull(),
            'title'=>$this->string(),
            'h1'=>$this->string(),
            'description'=>$this->string(500),
            'keywords'=>$this->string(500),
            'top_content'=>$this->text(),
            'bottom_content'=>$this->text(),
            'status' => $this->boolean()->notNull()->defaultValue(false),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('idx-seo-level', '{{%seo}}', ['level']);
        $this->createIndex('idx-seo-url', '{{%seo}}', ['url']);
        $this->createIndex('idx-seo-is_deleted', '{{%seo}}', ['is_deleted']);
        $this->createIndex('idx-seo-created_at', '{{%seo}}', ['created_at']);
        $this->createIndex('idx-seo-updated_at', '{{%seo}}', ['updated_at']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%seo}}');
    }
}
