<?php namespace app\migrations;

use yii\db\Migration;

class m210920_120001_create_file_model_table extends Migration
{
    protected $tableName = '{{%file_model}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(128)->notNull(),
            'ext' => $this->string(16)->notNull(),
            'mime' => $this->string(128)->notNull(),
            'size' => $this->integer()->notNull()->unsigned()->notNull(),
            'is_image' => $this->boolean()->notNull()->defaultValue(false),
            'is_file_exists' => $this->boolean()->notNull()->defaultValue(true),
            'link_class' => $this->string(128),
            'link_id' => $this->integer()->unsigned(),
            'link_attribute' => $this->string(),
            'sort' => $this->smallInteger()->unsigned(),
            'model_path_cache' => $this->string(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('idx-file_model-name', $this->tableName, 'name');
        $this->createIndex('idx-file_model-is_image', $this->tableName, 'is_image');
        $this->createIndex('idx-file_model-status', $this->tableName, 'status');
        $this->createIndex('idx-file_model-link_class', $this->tableName, 'link_class');
        $this->createIndex('idx-file_model-link_id', $this->tableName, 'link_id');
        $this->createIndex('idx-file_model-link_attribute', $this->tableName, 'link_attribute');
        $this->createIndex('idx-file_model-sort', $this->tableName, 'sort');
        $this->createIndex('idx-file_model-created_at', $this->tableName, 'created_at');
        $this->createIndex('idx-file_model-updated_at', $this->tableName, 'updated_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-file_model-name',
            $this->tableName
        );
        $this->dropIndex(
            'idx-file_model-created_at',
            $this->tableName
        );
        $this->dropIndex(
            'idx-file_model-updated_at',
            $this->tableName
        );
        $this->dropIndex(
            'idx-file_model-is_image',
            $this->tableName
        );
        $this->dropIndex(
            'idx-file_model-link_class',
            $this->tableName
        );
        $this->dropIndex(
            'idx-file_model-link_id',
            $this->tableName
        );
        $this->dropIndex(
            'idx-file_model-link_attribute',
            $this->tableName
        );
        $this->dropIndex(
            'idx-file_model-sort',
            $this->tableName
        );

        $this->dropTable($this->tableName);
    }
}
