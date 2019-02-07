<?php

namespace andrej2013\yiiboilerplate\modules\adminer\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

class AdminerController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = false;
    public $defaultAction = 'adminer';

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'adminer',
                        ],
                        'roles' => ['Authority']
                    ]
                ]
            ]
        ];
    }

    public function actionAdminer()
    {
        \Yii::$app->response->headers->set('test', 'test');
        return $this->render('index');
    }
}
