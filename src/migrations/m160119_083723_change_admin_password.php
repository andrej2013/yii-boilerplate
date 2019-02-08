<?php

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m160119_083723_change_admin_password extends Migration
{
    public function up()
    {
        $password_hash = Yii::$app->security->generatePasswordHash('admin', '10');
        $this->update('{{%user}}', ['password_hash' => $password_hash], ['username' => 'admin']);
    }

    public function down()
    {
        echo "m160119_083723_change_admin_password cannot be reverted.\n";

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
