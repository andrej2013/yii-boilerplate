<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 7/25/2017
 * Time: 9:53 AM
 */

namespace andrej2013\yiiboilerplate\modules\user\models;

use dektrium\user\Finder;
use andrej2013\yiiboilerplate\modules\user\models\twoway\ProviderInterface;
use andrej2013\yiiboilerplate\modules\userParameter\models\UserParameter;
use \Yii;
use yii\base\Exception;
use andrej2013\yiiboilerplate\modules\user\Module;
use yii\base\Model;

class TwoWay extends Model
{

    const SUCCESS = true;
    const ERROR = false;
    const EXPIRED = 'expired';

    const SCENARIO_WITHOUT_CODE = 'without-code';
    const SCENARIO_WITH_CODE = 'with-code';

    /**
     * @var ProviderInterface
     */
    public $provider;

    public $view;

    /**
     * @var User
     */
    public $user;

    public $code;

    public $user_id;

    public $secret;

    public $status = self::EXPIRED;
    public $message;

    /**
     * @var Module
     */
    protected $module;

    /**
     * TwoWay constructor.
     * @param $user
     * @throws Exception
     */
    public function __construct($user)
    {
        $this->module = Yii::$app->getModule('user');
        if (is_numeric($user)) {
            $method = 'findUserById';
        } else {
            $method = 'findUserByUsernameOrEmail';
        }
        $this->user = Yii::$container->get(Finder::className())->$method($user);
        if (!$this->user) {
            throw new Exception('Invalid user');
        }
        if (Yii::$app->getModule('user-parameter') && UserParameter::get('twoWayAuthenticationMethod', $this->user->id)
        ) {
            $provider = UserParameter::get('twoWayAuthenticationMethod', $this->user->id);
        } elseif ($this->module && $this->module->twoWayAuthenticationMethod) {
            $provider = $this->module->twoWayAuthenticationMethod;
        } else {
            throw new Exception("Two way authentication is enabled but method is not defined");
        }
        $this->user_id = $this->user->id;
        $this->provider = new $provider($this);
        $this->view = $this->provider->view;
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('app', 'Code'),
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        $rules = [
            [['code', 'user_id'], 'required', 'on' => [self::SCENARIO_WITH_CODE]],
        ];
        return $rules;
    }

    /**
     * @return mixed
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_WITH_CODE] = ['code', 'user_id'];
        return $scenarios;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->module->twoWay['enabled'] === Module::ENABLED ||
        // or it is by user and if user has it enabled in his settings
        ($this->module->twoWay['enabled'] == Module::USER);
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function validateUser()
    {
        $loginForm = Yii::createObject(LoginForm::className());
        $loginForm->login = $this->user->username;
        $loginForm->setScenario(LoginForm::TWO_WAY);
        return $loginForm->validate();
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->provider->getCode();
    }

    public function checkCode()
    {
        $this->provider->checkCode();
        $this->addError('code', $this->message);
        return [
            'status' => $this->status,
            'message' => $this->message,
            'returnUrl' => Yii::$app->getUser()->getReturnUrl()
        ];
    }

    public function refreshCode()
    {
        return $this->provider->resendCode();
    }
}