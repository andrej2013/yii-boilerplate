<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m170717_073725_user_auth_code extends Migration
{
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable(
            '{{%user_auth_code}}',
            [
                'id' => Schema::TYPE_UPK . "",
                'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'code' => Schema::TYPE_STRING . '(128) NOT NULL',
                'status' => 'tinyint(1)',
            ],
            $tableOptions
        );

        $this->createIndex('user_idx', '{{%user_auth_code}}', 'user_id');

        $this->addForeignKey('user_auth_code_fk_user_id', '{{%user_auth_code}}', 'user_id', '{{%user}}', 'id');


        $auth = $this->getAuth();
        $perm = $auth->createPermission('user_security_verify');
        $perm->description = 'Action for 2 way authenticate visit URL';
        $auth->add($perm);

        $auth = $this->getAuth();
        $perm = $auth->createPermission('user_security_check-code');
        $perm->description = 'Action for 2 way check AJAX';
        $auth->add($perm);

        $auth = $this->getAuth();
        $perm = $auth->createPermission('user_security_code');
        $perm->description = 'Action for 2 way authenticate';
        $auth->add($perm);

        $user = $auth->getRole('Public');
        $auth->addChild($user, $auth->getPermission('user_security_verify'));
        $auth->addChild($user, $auth->getPermission('user_security_check-code'));
        $auth->addChild($user, $auth->getPermission('user_security_code'));
    }

    public function down()
    {
        $this->dropForeignKey('user_auth_code_fk_user_id', '{{%user_auth_code}}');
        $this->dropTable('{{%user_auth_code}}');
        $auth = $this->getAuth();
        $auth->remove($auth->getPermission('user_security_verify'));
        $auth->remove($auth->getPermission('user_security_check-code'));
        $auth->remove($auth->getPermission('user_security_code'));
        echo "m170717_073725_user_auth_code cannot be reverted.\n";

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
