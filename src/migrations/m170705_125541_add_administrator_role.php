<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m170705_125541_add_administrator_role extends Migration
{
    public function up()
    {
        $auth = $this->getAuth();
        $role = $auth->getRole('Administrator');
        if ($role === null) {
            $administrator = $auth->createRole('Administrator');
            $administrator->description = 'Administrator User';
            $auth->add($administrator);
        }
        $authenticated = $auth->getRole('Authenticated');
        $auth->addChild($administrator, $authenticated);
    }

    public function down()
    {
        echo "m170706_125541_add_administrator_role cannot be reverted.\n";

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
