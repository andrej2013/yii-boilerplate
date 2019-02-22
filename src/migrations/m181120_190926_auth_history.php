<?php

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m181120_190926_auth_history extends Migration
{
    public function up()
    {
        if ($this->db->getTableSchema('{{%user_auth_log}}', true) === null) {
            $this->createTable('{{%user_auth_log}}', [
                'id'          => $this->primaryKey(),
                'user_id'      => $this->integer(),
                'date'        => $this->integer(),
                'cookie_based' => $this->boolean(),
                'duration'    => $this->integer(),
                'error'       => $this->string(),
                'ip'          => $this->string(),
                'host'        => $this->string(),
                'url'         => $this->string(),
                'user_agent'   => $this->string(),
            ]);
        }
    }

    public function down()
    {
        echo "m181120_190926_auth_history cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
