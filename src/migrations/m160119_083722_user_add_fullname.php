<?php

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m160119_083722_user_add_fullname extends Migration
{
    public function up()
    {
        $table = Yii::$app->db->schema->getTableSchema('{{%user}}');
        if(!isset($table->columns['fullname'])) {
            $this->addColumn('{{%user}}', 'fullname', 'string(100)');
        }
    }

    public function down()
    {
        echo "m160119_083722_user_add_fullname cannot be reverted.\n";

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
