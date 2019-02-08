<?php

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m190207_214706_grid_config_auth_rights extends Migration
{
    public function up()
    {
        $auth = $this->getAuth();
        $authenticated = $auth->getRole('Authenticated');
        $permission = $auth->createPermission('backend_grid-config');
        $auth->add($permission);
        $auth->addChild($authenticated, $permission);
    }

    public function down()
    {
        echo "m190207_214706_grid_config_auth_rights cannot be reverted.\n";

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
