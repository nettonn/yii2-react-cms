<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `redirect`.
 */
class m211119_171741_create_redirect_table extends Migration
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
        $this->createTable('{{%redirect}}', [
            'id' => $this->primaryKey(),
            'from'=>$this->string(1000)->notNull(),
            'to'=>$this->string(1000)->notNull(),
            'code' => $this->smallInteger(4)->unsigned(),
            'status' => $this->boolean()->notNull()->defaultValue(false),
            'sort' => $this->integer()->unsigned(),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('status', '{{%redirect}}', ['status']);
        $this->createIndex('sort', '{{%redirect}}', ['sort']);
        $this->createIndex('idx-redirect-is_deleted', '{{%redirect}}', ['is_deleted']);
        $this->createIndex('idx-redirect-created_at', '{{%redirect}}', ['created_at']);
        $this->createIndex('idx-redirect-updated_at', '{{%redirect}}', ['updated_at']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%redirect}}');
    }
}
