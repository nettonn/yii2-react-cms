<?php namespace app\migrations;

use yii\db\Migration;

class m210902_153857_create_page_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%page}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string()->notNull(),
            'alias' => $this->string()->notNull(),
            '_url' => $this->string()->notNull(),
            'parent_id' => $this->integer()->unsigned(),
            'description'=>$this->text(),
            'content'=>$this->text(),

            'layout' => $this->string(),
            'status' => $this->boolean()->notNull()->defaultValue(false),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),

            'seo_title' => $this->string(),
            'seo_h1' => $this->string(),
            'seo_keywords' => $this->string(500),
            'seo_description' => $this->string(500),
        ], $tableOptions);

        $this->createIndex('idx-page-parent', '{{%page}}', ['parent_id']);
        $this->createIndex('idx-page-url', '{{%page}}', ['_url']);
        $this->createIndex('idx-page-status', '{{%page}}', ['status']);
        $this->createIndex('idx-page-is_deleted', '{{%page}}', ['is_deleted']);
        $this->createIndex('idx-page-created_at', '{{%page}}', ['created_at']);
        $this->createIndex('idx-page-updated_at', '{{%page}}', ['updated_at']);

        $this->insert('{{%page}}', [
                'id'=>1,
                'name'=>'Главная',
                'alias'=>'main',
                '_url'=>'/',
                'parent_id'=>0,
                'content'=> '',
                'layout'=> 'mainpage',
                'status'=> true,
                'created_at' => time(),
                'updated_at' => time(),
            ]
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%page}}');
    }
}
