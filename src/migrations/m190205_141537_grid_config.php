<?php

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m190205_141537_grid_config extends Migration
{
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        
        $this->createTable('{{%grid_config}}', [
            'id'      => Schema::TYPE_PK . "",
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'grid'    => Schema::TYPE_STRING . '(255) NOT NULL',
            'column'  => Schema::TYPE_STRING . '(255) NOT NULL',
            'show'    => Schema::TYPE_TINYINT . '(1)',
        ], $tableOptions);
        
        $this->createIndex('user_idx', '{{%grid_config}}', ['user_id', 'grid', 'column'], true);
        $this->createIndex('show_idx', '{{%grid_config}}', 'show');
        $this->addForeignKey('grid_config_fk_user_id', '{{%grid_config}}', 'user_id', '{{%user}}', 'id');

    }

    public function down()
    {
        echo "m190205_141537_grid_config cannot be reverted.\n";

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
