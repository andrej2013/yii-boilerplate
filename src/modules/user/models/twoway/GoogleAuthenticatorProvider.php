<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 7/26/2017
 * Time: 8:57 AM
 */

namespace andrej2013\yiiboilerplate\modules\user\models\twoway;

use andrej2013\yiiboilerplate\modules\user\models\TwoWay;
use andrej2013\yiiboilerplate\modules\user\models\twoway\lib\GoogleAuthenticator;
use andrej2013\yiiboilerplate\modules\user\models\twoway\ProviderInterface;
use andrej2013\yiiboilerplate\modules\user\models\UserAuthCode;

class GoogleAuthenticatorProvider extends BaseProvider
{

    public $view = 'two-way/ga';

    public function getCode()
    {
        $ga = new GoogleAuthenticator();
        $code = $ga->createSecret();
        $userAuthCode = new UserAuthCode();
        $userAuthCode->user_id = $this->component->user->id;
        $userAuthCode->code = $code;
        $userAuthCode->save();
        return $code;
    }

    public function checkCode()
    {
        // Get last issued secret code for user
        $userAuthCode = UserAuthCode::find()->andWhere([
            'user_id' => $this->component->user->id,
        ])->orderBy(['created_at' => SORT_DESC])->one();
        $ga = new GoogleAuthenticator();
        $result = $ga->verifyCode($userAuthCode->code, $this->component->code, 20);    // 2 = 2*30sec clock tolerance
        if ($result) {
            $this->component->status = TwoWay::SUCCESS;
        } else {
            $this->component->status = TwoWay::ERROR;
            $this->component->message = \Yii::t('app', 'Wrong code provided');
        }
    }
}