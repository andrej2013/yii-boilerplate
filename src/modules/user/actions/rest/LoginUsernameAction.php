<?php

namespace andrej2013\yiiboilerplate\modules\user\actions\rest;

use app\models\User;
use yii\rest\Action;
use Yii;
use andrej2013\yiiboilerplate\rest\Token;

/**
 * Class LoginUsernameAction
 * @package andrej2013\yiiboilerplate\modules\user\actions\rest
 */
class LoginUsernameAction extends Action
{
	/**
     * Log in a user using just his username
	 * @param string $pin
	 * @return mixed
	 */
    public function run($username)
    {
        $user = User::find()->andWhere(['username' => $username, 'blocked_at' => null])->andWhere('confirmed_at IS NOT NULL')->one();

        // User was found, send back token
        if ($user) {
            /** @var Token $token */
            $token = Token::find()->where(['user_id' => $user->id])->one();

            // User has already a valid token
            if ($token === null || $token->isExpired) {
                $token = new Token();
                $token->user_id = $user->id;
                $token->type = Token::TYPE_RECOVERY;
                $token->save();
            }

            Yii::$app->getResponse()->setStatusCode(200);

            return [
                'access_token' => $token->code,
                'user_id' => $user->id
            ];

        } else {
            Yii::$app->getResponse()->setStatusCode(400);

            return [
                'error' => 'invalid username'
            ];
        }
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [];
    }
}
