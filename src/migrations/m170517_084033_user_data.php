<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m170517_084033_user_data extends Migration
{
    public function up()
    {
        if (Yii::$app->db->getTableSchema('user_data', true) === null) {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            $this->createTable('{{%user_data}}', [
                    'id'      => Schema::TYPE_PK . "",
                    'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                ], $tableOptions);

            $this->createIndex('user_idx', '{{%user_data}}', 'user_id');
            $this->addForeignKey('user_data_fk_user_user_id', '{{%user_data}}', 'user_id', '{{%user}}', 'id');
        }
    }

    public function down()
    {
        echo "m170517_084033_user_tw_data cannot be reverted.\n";

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
