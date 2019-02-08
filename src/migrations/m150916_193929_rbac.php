<?php

use yii\db\Migration;

class m150916_193929_rbac extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        if ($auth instanceof \yii\rbac\DbManager) {
            $guest = $auth->getRole('Public');
            if ($guest === null) {
                $guest = $auth->createRole('Public');
                $guest->description = 'Unauthenticated User';
                $auth->add($guest);
            }
            $authenticated = $auth->getRole('Authenticated');
            if ($authenticated === null) {
                $authenticated = $auth->createRole('Authenticated');
                $authenticated->description = 'Authenticated User';
                $auth->add($authenticated);
            }
            $permission = $auth->getPermission('backend_default');
            if ($permission === null) {
                $permission = $auth->createPermission('backend_default');
                $permission->description = 'Backend Dashboard';
                $auth->add($permission);
            }
            $permission = $auth->getPermission('app_site');
            if ($permission === null) {
                $permission = $auth->createPermission('app_site');
                $permission->description = 'Main Site Controller';
                $auth->add($permission);
            }
            $auth->addChild($authenticated, $auth->getPermission('backend_default'));
            $auth->addChild($authenticated, $auth->getPermission('app_site'));

        } else {
            throw new \yii\base\Exception('Application authManager must be an instance of \yii\rbac\DbManager');
        }
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        if ($auth instanceof \yii\rbac\DbManager) {
            $auth->remove($auth->getPermission('backend_default'));
            $auth->remove($auth->getPermission('app_site'));
            $auth->remove($auth->getRole('Authenticated'));
            $auth->remove($auth->getRole('Public'));
        } else {
            throw new \yii\base\Exception('Application authManager must be an instance of \yii\rbac\DbManager');
        }
    }
}
