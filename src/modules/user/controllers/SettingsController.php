<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 1/11/2017
 * Time: 2:21 PM
 */

namespace andrej2013\yiiboilerplate\modules\user\controllers;

use andrej2013\yiiboilerplate\modules\user\models\Profile;
use andrej2013\yiiboilerplate\modules\userParameter\models\UserParameterForm;
use dektrium\user\models\SettingsForm;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class SettingsController extends \dektrium\user\controllers\SettingsController
{

    /**
     * Additional actions for controllers, uncomment to use them
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => [
                            'parameters',
                        ],
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Shows profile settings form.
     *
     * @return string|\yii\web\Response
     */
    public function actionProfile()
    {
        /**
         * @var $model Profile
         */
        $model = Profile::find()
                        ->andWhere(['user_id' => \Yii::$app->user->identity->getId()])
                        ->one();
        if ($model == null) {
            $model = \Yii::createObject(Profile::className());
            $model->link('user', \Yii::$app->user->identity);
        }

        $event = $this->getProfileEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_PROFILE_UPDATE, $event);
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $model->uploadFile('picture', $_FILES['Profile']['tmp_name'], $_FILES['Profile']['name']);
            \Yii::$app->getSession()
                      ->setFlash('success', \Yii::t('user', 'Your profile has been updated'));
            $this->trigger(self::EVENT_AFTER_PROFILE_UPDATE, $event);
            return $this->refresh();
        }

        return $this->render('profile_layout', [
            'section' => 'profile',
            'model'   => $model,
        ]);
        return $this->render('profile', [
            'model' => $model,
        ]);
    }

    public function actionParameters()
    {
        $model = new UserParameterForm();
        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            $model->save();
        }
        return $this->render('paramaters', ['model' => $model]);
    }

    /**
     * Displays page where user can update account settings (username, email or password).
     *
     * @return string|\yii\web\Response
     */
    public function actionAccount()
    {
        /** @var SettingsForm $model */
        $model = \Yii::createObject(SettingsForm::className());
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_ACCOUNT_UPDATE, $event);
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', \Yii::t('app', 'Your account details have been updated'));
            $this->trigger(self::EVENT_AFTER_ACCOUNT_UPDATE, $event);
            return $this->refresh();
        }

        return $this->render('profile_layout', [
            'section' => 'account',
            'model'   => $model,
        ]);
    }

    /**
     * Displays list of connected network accounts.
     *
     * @return string
     */
    public function actionNetworks()
    {
        return $this->render('profile_layout', [
            'section'   => 'networks',
            'user' => \Yii::$app->user->identity,
            'model' => null,
        ]);
    }

}