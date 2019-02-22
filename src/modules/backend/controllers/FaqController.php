<?php
/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 6/23/2017
 * Time: 12:19 PM
 */

namespace andrej2013\yiiboilerplate\modules\backend\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

class FaqController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['@']
                    ]
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Lists all Club models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
