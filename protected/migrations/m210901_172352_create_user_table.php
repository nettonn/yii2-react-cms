<?php namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m210901_172352_create_user_table extends Migration
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

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey()->unsigned(),
            'username' => $this->string(),
            'auth_key' => $this->string(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string(),
            'email' => $this->string()->notNull(),
            'email_confirm_token' => $this->string(),
            'role' => $this->string()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull()->unsigned(),
            'updated_at' => $this->integer()->notNull()->unsigned(),
        ], $tableOptions);

//        $this->createIndex('idx-user-username', '{{%user}}', 'username', true);
        $this->createIndex('idx-user-email', '{{%user}}', 'email', true);
        $this->createIndex('idx-user-created_at', '{{%user}}', 'created_at');
        $this->createIndex('idx-user-updated_at', '{{%user}}', 'updated_at');

        $this->insert('{{%user}}', [
                'username'=>'admin',
                'email'=>'dev.nettonn@gmail.com',
                'password_hash'=>\Yii::$app->security->generatePasswordHash(97500009750000),
                'role'=>'admin',
                'status'=>1,
                'auth_key'=>'YasdZK1LtSMDN3-vD4sIG-OO2Nrzh9r4',
                'created_at'=>time(),
                'updated_at'=>time(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
//        $this->dropIndex(
//            'idx-user-username',
//            '{{%user}}'
//        );
        $this->dropIndex(
            'idx-user-email',
            '{{%user}}'
        );
        $this->dropIndex(
            'idx-user-created_at',
            '{{%user}}'
        );
        $this->dropIndex(
            'idx-user-updated_at',
            '{{%user}}'
        );

        $this->dropTable('{{%user}}');
    }
}
