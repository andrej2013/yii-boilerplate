<?php

use yii\db\Migration;

class m170119_100133_authority_role extends Migration
{
    /**
     *
     */
    public function up()
    {
        $auth = Yii::$app->authManager;

        if ($auth instanceof \yii\rbac\DbManager) {
            // Add the Authority role if it doesn't exist
            $role = $auth->getRole('Authority');
            if ($role === null) {
                $authority = $auth->createRole('Authority');
                $authority->description = 'Authority User';
                $auth->add($authority);

                // If the role doesn't exist, the first user probably isn't one either.
                $auth->assign($authority, 1);
            }
        } else {
            throw new \yii\base\Exception('Application authManager must be an instance of \yii\rbac\DbManager');
        }
    }

    /**
     *
     */
    public function down()
    {
        $auth = Yii::$app->authManager;

        if ($auth instanceof \yii\rbac\DbManager) {
            $auth->remove($auth->getRole('Authority'));
        } else {
            throw new \yii\base\Exception('Application authManager must be an instance of \yii\rbac\DbManager');
        }
    }
}
