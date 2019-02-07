<?php

namespace andrej2013\yiiboilerplate\controllers\user;

use dektrium\user\controllers\AdminController as BaseController;
use dektrium\user\filters\AccessRule;
use yii\filters\AccessControl;

/**
 * Class AdminController
 * @package andrej2013\yiiboilerplate\controllers\user
 */
class AdminController extends BaseController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin', 'Administrator'],
                    ],
                ],
            ],
        ]);
    }
}
