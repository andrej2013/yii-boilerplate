<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m170510_104754_add_picture_profile extends Migration
{
    public function up()
    {
        $this->addColumn('{{%profile}}', 'picture', Schema::TYPE_STRING);
    }

    public function down()
    {
        echo "m170510_104754_add_picture_profile cannot be reverted.\n";

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
