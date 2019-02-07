<?php

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m160119_083724_cache_table extends Migration
{
    public function up()
    {
        if ($this->db->getTableSchema('{{%cache}}', true) === null) {
            $tableOptions = 'ENGINE=InnoDB';

            $this->createTable(
                '{{%cache}}',
                [
                    'id' => 'char(128) NOT NULL PRIMARY KEY',
                    'expire' => 'int(11)',
                    'data' => 'BLOB',
                ],
                $tableOptions
            );
        }
    }

    public function down()
    {
        echo "m160119_083724_cache_table cannot be reverted.\n";

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
