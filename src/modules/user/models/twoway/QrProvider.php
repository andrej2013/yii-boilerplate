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

class QrProvider extends BaseProvider
{

    public $view = 'two-way/qr';

    public function getCode()
    {
        $userAuthCode = new UserAuthCode();
        $userAuthCode->user_id = $this->component->user->id;
        $userAuthCode->code = hash(
            $this->module->twoWay['algorithm'],
            $this->component->user->id . $this->component->user->username . time()
        );
        $userAuthCode->save();
        $this->component->code = $userAuthCode->code;
        return $userAuthCode->code;
    }

    public function checkCode()
    {
        $mysqlNow = (new Query())->select(new Expression('NOW()'))->column();
        $prevTime = date(
            'Y-m-d h:i:s',
            strtotime($mysqlNow[0] . '-' . $this->module->twoWay['expired_time'] . ' seconds')
        );
        $userAuthCode = UserAuthCode::find()->andWhere([
            'code' => $this->component->code,
            'user_id' => $this->component->user->id,
            'status' => 1,
        ])
            ->one();
        if ($userAuthCode) {
            if ($userAuthCode->created_at < $prevTime) {
                //Expired
                $this->component->status = TwoWay::EXPIRED;
                $this->component->message = Yii::t('app', 'Your link expired. Click below to refresh QR code');
            } else {
                $this->component->status = TwoWay::SUCCESS;
            }
        } else {
            $this->component->status = TwoWay::ERROR;
        }
    }

}