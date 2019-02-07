<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 7/25/2017
 * Time: 9:34 AM
 */

namespace andrej2013\yiiboilerplate\modules\user\models\twoway;

use andrej2013\yiiboilerplate\modules\user\models\TwoWay;
use andrej2013\yiiboilerplate\modules\user\models\User;
use andrej2013\yiiboilerplate\modules\user\models\UserAuthCode;
use andrej2013\yiiboilerplate\modules\user\models\twoway\ProviderInterface;
use \Yii;
use yii\db\Expression;
use yii\db\Query;

class EmailProvider extends QrProvider
{

    /** @var string */
    public $viewPath = '@andrej2013-boilerplate/modules/user/views/security/two-way/mail';

    /**
     * @var string
     */
    public $view = 'two-way/email';

    /**
     * @return string
     */
    public function getCode()
    {
        $code = parent::getCode();
        $this->sendEmail($code);
        return $code;
    }

    /**
     * @param $code
     * @return bool
     */
    protected function sendEmail($code)
    {
        /** @var \yii\mail\BaseMailer $mailer */
        $mailer = Yii::$app->mailer;
        $mailer->viewPath = $this->viewPath;
        $mailer->getView()->theme = Yii::$app->view->theme;
        $params = [
            'name' => $this->component->user->profile->name,
            'code' => $code,
        ];
        return $mailer->compose(['html' => 'confirm', 'text' => 'text/confirm'], $params)
//            ->setTo('nikolatesic@gmail.com')
            ->setTo($this->component->user->email)
            ->setFrom(getenv('APP_ADMIN_EMAIL'))
            ->setSubject(Yii::t('app', 'Login link'))
            ->send();
    }
}