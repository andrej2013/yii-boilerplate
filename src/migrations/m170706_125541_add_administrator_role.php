<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m170706_125541_add_administrator_role extends Migration
{
    public function up()
    {
        $auth = $this->getAuth();
        $role = $auth->getRole('Administrator');
        if ($role === null) {
            $administrator = $auth->createRole('Administrator');
            $administrator->description = 'Administrator User';
            $auth->add($administrator);

            $editor = $auth->getRole('Editor');
            $auth->addChild($administrator, $editor);
        }
        $role = $auth->getRole('Viewer');
        if ($role === null) {
            $viewer = $auth->createRole('Viewer');
            $viewer->description = 'Viewer User';
            $auth->add($viewer);

            $editor = $auth->getRole('Editor');
            $public = $auth->getRole('Public');
            $auth->addChild($viewer, $public);
            $auth->addChild($editor, $viewer);
        }
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
