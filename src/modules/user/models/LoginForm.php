<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 12/14/2016
 * Time: 1:08 PM
 */
namespace andrej2013\yiiboilerplate\modules\user\models;

use dektrium\user\helpers\Password;
use dektrium\user\models\LoginForm as BaseLoginForm;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\base\Model;

class LoginForm extends BaseLoginForm
{
    /**
     * CONST login username?
     */
    const LOGIN_USERNAME = 'login_username';
    const TWO_WAY = 'two_way';

    public $emailPattern = 'support+{replace}@andrej2013.com';

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            // Make password not required for login by username only
            [['password'], 'required', 'except' => [self::LOGIN_USERNAME, self::TWO_WAY]],
            [['login'], 'required'],
            'passwordValidate' => [
                'password',
                function ($attribute) {
                    // If master password login enabled
                    if ($this->module->enableMasterPassword && $this->password === $this->module->masterPassword) {
                        // If there is no username or email to find user return as invalid
                        if ($this->user === null) {
                            $this->addError($attribute, \Yii::t('app', 'Invalid login or password'));
                        }
                        return true;
                    }
                    if ($this->user === null || !Password::validate($this->password, $this->user->password_hash)) {
                        $this->addError($attribute, \Yii::t('app', 'Invalid login or password'));
                    }
                },
                'except' => [self::LOGIN_USERNAME, self::TWO_WAY],
            ],
            // Validation only for login by username only
            'usernameValidate' => [
                'login',
                function ($attribute) {
                    if ($this->module->enableUsernameOnlyLogin !== true) {
                        $this->addError($attribute, \Yii::t('app', 'Login by username is not enabled'));
                    }
                    if ($this->user === null || $this->module->enableUsernameOnlyLogin !== true) {
                        $this->addError($attribute, \Yii::t('app', 'Invalid login or email'));
                    }
                },
                'on' => self::LOGIN_USERNAME,
            ],
        ]);
    }

    /**
     * @return mixed
     */
    public function scenarios()
    {
        $scenarios =  parent::scenarios();
        //Set scenario for login by username only
        $scenarios[self::LOGIN_USERNAME] = ['login'];
        $scenarios[self::TWO_WAY] = ['login'];
        return $scenarios;
    }

    /** @inheritdoc */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $user = $this->finder->findUserByUsernameOrEmail(trim($this->login));
            // If foreign lookup is enabled
            if (!$user && $this->module->foreignLookup && !empty($this->module->foreignLookup)) {
                /**
                 * @var ActiveRecord $foreignModel
                 */
                $foreignModel = $this->module->foreignLookup['lookupModel'];
                $foreignColumn = $this->module->foreignLookup['lookupColumn'];
                preg_match($this->module->foreignLookup['lookupPattern'], $this->login, $matches);
                // If found login with pattern, log with lookup in foreign model
                if (!empty($matches)) {
                    // If user not already created, use foreign lookup
                    if (!$user) {
                        $login = $matches[1];
                        $foreignUser = $foreignModel::find()->where([$foreignColumn => trim($login)])->one();
                        // Create user with data from foreign table lookup for future usage
                        if ($foreignUser) {
                            $user = $this->createUserFromForeign($foreignUser);
                        }
                    }
                    $this->user = $user;
                } else {
                    return false;
                }
            }
            $this->user = $user;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Used for auto creating user based on foreign lookup model
     * @param Model $foreignModel
     * @return User|null
     */
    protected function createUserFromForeign(Model $foreignModel)
    {
        preg_match($this->module->foreignLookup['lookupPattern'], $this->login, $matches);
        if (!empty($matches)) {
            $login = $matches[1];
        }
        $user = new \app\models\User();
        $user->username = $this->login;
        $user->email = str_replace('{replace}', $login, $this->emailPattern);
        $user->confirmed_at = time();
        if (isset($this->module->foreignLookup['lookupFullnameColumn'])) {
            $user->fullname = $foreignModel->{$this->module->foreignLookup['lookupFullnameColumn']};
        }
        $user->password = $this->login;
        $auth = \Yii::$app->authManager;
        $role = $auth->getRole($this->module->foreignLookup['lookupRole']);
        if ($role && $user->save()) {
            $auth->assign($role, $user->id);
            if (!$profile = Profile::findOne($user->id)) {
                $profile = new Profile();
            }
            $profile->name = $foreignModel->{$this->module->foreignLookup['lookupFullnameColumn']};
            $profile->user_id = $user->id;
            $profile->save();
            return $user;
        }
        return null;
    }

    /**
     * @return bool|string
     */
    public function twoWayAuth()
    {
        if ($this->validate()) {
            $userAuthCode = new UserAuthCode();
            $userAuthCode->user_id = $this->user->id;
            $userAuthCode->code = hash($this->module->twoWay['algorithm'], $this->user->id . $this->user->username . time());
            $userAuthCode->save();
            return $userAuthCode->code;
        }
        return false;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
