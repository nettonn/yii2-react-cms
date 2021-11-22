<?php namespace app\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m150115_092828_create_session_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%session}}', [
                'id' => 'CHAR(40) NOT NULL PRIMARY KEY',
                'expire' => Schema::TYPE_INTEGER,
                'data' => Schema::TYPE_BINARY,
            ], $tableOptions);

        $this->createIndex('ix_expire', '{{%session}}', 'expire');
    }

    public function down()
    {
        $this->dropTable('{{%session}}');
    }
}
