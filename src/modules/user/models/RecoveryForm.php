<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 5/15/2017
 * Time: 1:49 PM
 */

namespace andrej2013\yiiboilerplate\modules\user\models;

use dektrium\user\models\Token;
use yii\helpers\Html;

class RecoveryForm extends \dektrium\user\models\RecoveryForm
{
    /**
     * Sends recovery message.
     *
     * @return bool
     */
    public function sendRecoveryMessage()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->finder->findUserByEmail($this->email);
        if ($user instanceof \dektrium\user\models\User) {
            /** @var Token $token */
            $token = \Yii::createObject([
                'class' => Token::className(),
                'user_id' => $user->id,
                'type' => Token::TYPE_RECOVERY,
            ]);

            if (!$token->save(false)) {
                return false;
            }

            if (!$this->mailer->sendRecoveryMessage($user, $token)) {
                return false;
            }
        } else {
            \Yii::$app->session->setFlash(
                'danger',
                \Yii::t(
                    'app',
                    'User with email address {email} not exist',
                    ['email' => Html::tag('strong', $this->email)]
                )
            );
            return false;
        }
        \Yii::$app->session->setFlash(
            'info',
            \Yii::t('app', 'An email has been sent with instructions for resetting your password')
        );
        return true;
    }
}