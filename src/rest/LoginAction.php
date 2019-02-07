<?php
/**
 * Created by PhpStorm.
 * User: ben-g
 * Date: 05.02.2016
 * Time: 09:50
 */

namespace andrej2013\yiiboilerplate\rest;

use andrej2013\yiiboilerplate\models\User;
use andrej2013\yiiboilerplate\models\UserQuery;
use dektrium\user\Finder;
use dektrium\user\helpers\Password;
use dektrium\user\models\LoginForm;
use dektrium\user\models\UserSearch;
use yii\rest\Action;
use Yii;

class LoginAction extends Action
{
    /**
     *
     */
    public function run()
    {
        /** @var LoginForm $model */
        $username = Yii::$app->request->post()['username'];
        $password = Yii::$app->request->post()['password'];

        $user = User::find()->where(['username'=>$username])->one();

        if(Password::validate($password, $user->password_hash)) {


            /** @var Token $token */
            $token = Token::find()->where(['user_id'=>$user->id])->one();

            if($token != null && !$token->isExpired)
                return ["access_token" => $token->code];

            $token = new Token();
            $token->user_id = $user->id;
            $token->type = Token::TYPE_RECOVERY;
            if(!$token->save()){
                die("ERROR: " . print_r($token->getErrors(),true));
            }

            Yii::$app->getResponse()->setStatusCode(200);

            return ["access_token" => $token->code];

        }
        else {
            // Bad request
            Yii::$app->getResponse()->setStatusCode(400);
        }
    }
}