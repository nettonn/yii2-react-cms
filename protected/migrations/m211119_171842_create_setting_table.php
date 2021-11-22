<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `setting`.
 */
class m211119_171842_create_setting_table extends Migration
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
        $this->createTable('{{%setting}}', [
            'id' => $this->primaryKey(),
            'name'=>$this->string()->notNull(),
            'key'=>$this->string()->notNull(),
            'type'=>$this->smallInteger()->notNull(),
            'value_bool' => $this->boolean(),
            'value_int' => $this->integer(),
            'value_string' => $this->string(),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('idx-setting-name', '{{%setting}}', ['name']);
        $this->createIndex('idx-setting-key', '{{%setting}}', ['key']);
        $this->createIndex('idx-setting-type', '{{%setting}}', ['type']);
        $this->createIndex('idx-setting-is_deleted', '{{%setting}}', ['is_deleted']);
        $this->createIndex('idx-setting-created_at', '{{%setting}}', ['created_at']);
        $this->createIndex('idx-setting-updated_at', '{{%setting}}', ['updated_at']);

        $this->insert('{{%setting}}', [
                'name'=>'ID главной страницы',
                'key'=>'main_page_id',
                'value_int'=>1,
                'type'=> 2,
                'created_at' => time(),
                'updated_at' => time(),
            ]
        );
        $this->insert('{{%setting}}', [
                'name'=>'Название сайта',
                'key'=>'site_name',
                'value_string'=>'',
                'type'=> 3,
                'created_at' => time(),
                'updated_at' => time(),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%setting}}');
    }
}
