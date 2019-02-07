<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m170616_102118_error_page_acl extends Migration
{
    public function up()
    {
        $auth = $this->getAuth();
        if (!$auth->getPermission('app_site_error')) {
            $perm = $auth->createPermission('app_site_error');
            $perm->description = 'Error page';
            $auth->add($perm);
            $user = $auth->getRole('Public');
            $auth->addChild($user, $auth->getPermission('app_site_error'));
        }

    }

    public function down()
    {
        echo "m170616_102118_error_page_acl cannot be reverted.\n";

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
