<?php

namespace andrej2013\yiiboilerplate\models;

use andrej2013\yiiboilerplate\models\UserAuthLog;

class User extends \andrej2013\yiiboilerplate\modules\user\models\User
{

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authLog'] = [
            'class'              => \yii2tech\authlog\AuthLogIdentityBehavior::class,
            'authLogRelation'    => 'authLogs',
            'defaultAuthLogData' => function ($model) {
                return [
                    'ip'        => \Yii::$app->request->getUserIP(),
                    'host'      => @gethostbyaddr(\Yii::$app->request->getUserIP()),
                    'url'       => \Yii::$app->request->getAbsoluteUrl(),
                    'userAgent' => \Yii::$app->request->getUserAgent(),
                ];
            },
        ];
        return $behaviors;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthLogs()
    {
        return $this->hasMany(UserAuthLog::class, ['user_id' => 'id']);
    }

}
