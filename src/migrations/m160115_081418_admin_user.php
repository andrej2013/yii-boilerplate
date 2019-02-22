<?php

use yii\db\Schema;
use yii\db\Migration;

class m160115_081418_admin_user extends Migration
{
    public function up()
    {
        $password_hash = Yii::$app->security->generatePasswordHash('admin', '10');
        $this->insert('{{%user}}', [
            'id'                => 1,
            'username'          => 'admin',
            'email'             => 'admin@admin.com',
            'password_hash'     => $password_hash,
            'auth_key'          => '18ejHbm8lkBwuG50knS2IXWNzqQRN7G8',
            'confirmed_at'      => time(),
            'unconfirmed_email' => null,
            'blocked_at'        => null,
            'registration_ip'   => null,
            'created_at'        => time(),
            'updated_at'        => time(),
            'flags'             => 0,
        ]);
    }

    public function down()
    {
        echo "m160115_081418_admin_user cannot be reverted.\n";

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
