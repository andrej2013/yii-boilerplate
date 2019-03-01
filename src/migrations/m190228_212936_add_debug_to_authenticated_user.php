<?php

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m190228_212936_add_debug_to_authenticated_user extends Migration
{
    public function up()
    {
        $auth = $this->getAuth();
        $permissions = [
            'debug_default',
            'debug_default_toolbar',
            'debug_default_view',
        ];
        $user = $auth->getRole('Authenticated');
        foreach ($permissions as $permission) {
            $p = $auth->createPermission($permission);
            $auth->add($p);
            $auth->addChild($user, $p);
        }
    }

    public function down()
    {
        echo "m190228_212936_add_debug_to_authenticated_user cannot be reverted.\n";

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
