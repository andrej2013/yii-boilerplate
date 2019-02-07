<?php

namespace andrej2013\yiiboilerplate\modules\user\models;

use andrej2013\yiiboilerplate\components\QrCode;
use Yii;
use \andrej2013\yiiboilerplate\modules\user\models\base\UserAuthCode as BaseUserAuthCode;

/**
 * This is the model class for table "user_auth_code".
 */
class UserAuthCode extends BaseUserAuthCode
{

    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';
    const STATUS_EXPIRED = 'expired';

//    /**
//     * List of additional rules to be applied to model, uncomment to use them
//     * @return array
//     */
//    public function rules()
//    {
//        return array_merge(parent::rules(), [
//            [['something'], 'safe'],
//        ]);
//    }

    /**
     * @param $code
     * @return string
     */
    public static function generateQr($code)
    {
        $url = self::getUrl($code);
        return QrCode::staticGet($url, 200);
    }

    /**
     * @param $code
     * @return string
     */
    public static function getUrl($code)
    {
        return \yii\helpers\Url::toRoute(['/user/security/verify', 'code' => $code], true);
    }
}
