<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 7/25/2017
 * Time: 1:24 PM
 */

namespace andrej2013\yiiboilerplate\modules\user\models\twoway;

use andrej2013\yiiboilerplate\modules\user\models\TwoWay;
use andrej2013\yiiboilerplate\modules\user\models\User;
use andrej2013\yiiboilerplate\modules\user\models\UserAuthCode;
use andrej2013\yiiboilerplate\modules\user\Module;
use \Yii;

abstract class BaseProvider implements ProviderInterface
{

    public $status = TwoWay::ERROR;

    public $message;

    /**
     * @var TwoWay
     */
    protected $component;

    /**
     * @var Module
     */
    protected $module;

    public function __construct($component)
    {
        $this->component = $component;
        $this->module = Yii::$app->getModule('user');
    }

    public function resendCode()
    {
        $oldAuthCode = UserAuthCode::find()->andWhere([
            'user_id' => $this->component->user->id,
            'code' => $this->component->code,
        ])->one();
        if (!$oldAuthCode) {
            return false;
        }
        return $this->getCode();
    }
}