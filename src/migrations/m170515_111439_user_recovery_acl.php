<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m170515_111439_user_recovery_acl extends Migration
{
    public function up()
    {
        $auth = $this->getAuth();
        if (!$auth->getPermission('user_recovery')) {
            $perm = $auth->createPermission('user_recovery');
            $perm->description = 'User Recovery controller';
            $auth->add($perm);
            $user = $auth->getRole('Public');
            $auth->addChild($user, $auth->getPermission('user_recovery'));
        }
    }

    public function down()
    {
        echo "m170515_111439_user_recovery_acl cannot be reverted.\n";

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
