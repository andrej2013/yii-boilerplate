<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 5/15/2017
 * Time: 1:05 PM
 */

namespace andrej2013\yiiboilerplate\modules\user\controllers;

use dektrium\user\models\Token;
use andrej2013\yiiboilerplate\modules\user\models\RecoveryForm;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class RecoveryController extends \dektrium\user\controllers\RecoveryController
{

    /**
     * Shows page where user can request password recovery.
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRequest()
    {
        if (!$this->module->enablePasswordRecovery) {
            throw new NotFoundHttpException();
        }
        Url::remember();

        /** @var RecoveryForm $model */
        $model = \Yii::createObject([
            'class'    => RecoveryForm::className(),
            'scenario' => RecoveryForm::SCENARIO_REQUEST,
        ]);
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);
        $this->trigger(self::EVENT_BEFORE_REQUEST, $event);
        if ($model->load(\Yii::$app->request->post())) {
            if ($model->sendRecoveryMessage()) {
                $this->trigger(self::EVENT_AFTER_REQUEST, $event);
                return $this->redirect(Url::toRoute('/user/login'));
            } else {
                return $this->redirect(Url::previous());
            }
        }
        return $this->render('request', [
            'model' => $model,
        ]);
    }

    /**
     * Displays page where user can reset password.
     *
     * @param int    $id
     * @param string $code
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionReset($id, $code)
    {
        if (!$this->module->enablePasswordRecovery) {
            throw new NotFoundHttpException();
        }

        /** @var Token $token */
        $token = $this->finder->findToken(['user_id' => $id, 'code' => $code, 'type' => Token::TYPE_RECOVERY])->one();
        $event = $this->getResetPasswordEvent($token);

        $this->trigger(self::EVENT_BEFORE_TOKEN_VALIDATE, $event);

        if ($token === null || $token->isExpired || $token->user === null) {
            $this->trigger(self::EVENT_AFTER_TOKEN_VALIDATE, $event);
            \Yii::$app->session->setFlash(
                'danger',
                \Yii::t('app', 'Recovery link is invalid or expired. Please try requesting a new one.')
            );
            return $this->render('/message', [
                'title'  => \Yii::t('app', 'Invalid or expired link'),
                'module' => $this->module,
                'layout' => '@andrej2013-backend-views/layouts/main-login',
            ]);
        }

        /** @var RecoveryForm $model */
        $model = \Yii::createObject([
            'class'    => RecoveryForm::className(),
            'scenario' => RecoveryForm::SCENARIO_RESET,
        ]);
        $event->setForm($model);

        $this->performAjaxValidation($model);
        $this->trigger(self::EVENT_BEFORE_RESET, $event);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->resetPassword($token)) {
            $this->trigger(self::EVENT_AFTER_RESET, $event);
            return $this->redirect(Url::toRoute('/user/login'));
        }

        return $this->render('reset', [
            'model' => $model,
        ]);
    }
}