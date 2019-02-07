<?php

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m170202_092308_file_manager_permission extends Migration
{
    public function up()
    {
        $auth = $this->getAuth();
        $permission = $auth->createPermission('app_file');
        $permission->description = 'File Manager Controller';
        $auth->add($permission);

        $role = $auth->getRole('Public');
        $auth->addChild($role, $auth->getPermission('app_file'));
    }

    public function down()
    {
        echo "m170202_092308_file_manager_permission cannot be reverted.\n";

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
