<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 12/14/2016
 * Time: 12:51 PM
 */

namespace andrej2013\yiiboilerplate\modules\user\controllers;

use app\models\User;
use \dektrium\user\controllers\SecurityController as BaseSecurityController;
use dektrium\user\Finder;
use andrej2013\yiiboilerplate\modules\user\models\AbstractTwoWay;
use andrej2013\yiiboilerplate\modules\user\models\LoginForm;
use andrej2013\yiiboilerplate\modules\user\models\TwoWay;
use andrej2013\yiiboilerplate\modules\user\models\UserAuthCode;
use andrej2013\yiiboilerplate\modules\userParameter\models\UserParameter;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;
use andrej2013\yiiboilerplate\modules\user\Module;

class SecurityController extends BaseSecurityController
{
    /**
     * @var bool disable the layout for the views
     */
    public $layout = '@andrej2013-backend-views/layouts/main-login';

    /**
     * @var string login view
     */
    public $loginView = '@andrej2013-boilerplate/modules/user/views/security/login';

    /** @inheritdoc */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['login-username', 'login-selection', 'verify', 'check-code', 'refresh-code', 'code'],
                        'roles'   => ['?', '@'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Displays the login page.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (! Yii::$app->user->isGuest) {
            $this->goHome();
        }

        $this->setOrigin();

        /** @var LoginForm $model */
        $model = \Yii::createObject(LoginForm::className());
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);
        $this->trigger(self::EVENT_BEFORE_LOGIN, $event);

        if ($model->load(Yii::$app->getRequest()
                                  ->post())) {
            $twoWay = new TwoWay($model->login);
            if ($twoWay->isEnabled()) {
                if ($twoWay->validateUser()) {
                    return $this->render($this->loginView, [
                        'section' => $twoWay->view,
                        'model'   => $twoWay,
                        'module'  => $this->module,
                        'qr'      => $twoWay->getCode(),
                    ]);
                }
            } else if ($model->login()) {
                $this->trigger(self::EVENT_AFTER_LOGIN, $event);
                return $this->goBack();
            }
        }

        return $this->render($this->loginView, [
            'section' => 'login-regular',
            'model'   => $model,
            'module'  => $this->module,
        ]);
    }

    /**
     * Display login by username only page
     * @return string|\yii\web\Response
     * @throws HttpException
     */
    public function actionLoginUsername()
    {
        if ($this->module->enableUsernameOnlyLogin !== true) {
            throw new HttpException(404);
        }
        if (! Yii::$app->user->isGuest) {
            $this->goHome();
        }

        $this->setOrigin();

        /** @var LoginForm $model */
        $class = $this->module->modelMap['LoginForm'];
        $model = \Yii::createObject($class);
        $model->setScenario(LoginForm::LOGIN_USERNAME);
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);
        $this->trigger(self::EVENT_BEFORE_LOGIN, $event);
        if ($model->load(Yii::$app->getRequest()
                                  ->post()) && $model->login()) {
            $this->trigger(self::EVENT_AFTER_LOGIN, $event);
            return $this->goBack();
        }

        return $this->render($this->loginView, [
            'section' => 'login-username',
            'model'   => $model,
            'module'  => $this->module,
        ]);
    }

    /**
     * @return mixed
     * @throws HttpException
     */
    public function actionLoginSelection()
    {
        if ($this->module->enableSelectionLogin !== true) {
            throw new HttpException(404);
        }
        if (! Yii::$app->user->isGuest) {
            $this->goHome();
        }

        $this->setOrigin();

        if (Yii::$app->getRequest()->isAjax) {
            if ($this->module->enableSelectionLogin !== true) {
                return Json::encode(['success' => false, 'message' => \Yii::t('app', 'Login by selection only is not enabled')]);
            }
            $user = User::find()
                        ->andWhere(['id'         => Yii::$app->getRequest()
                                                             ->post('userId'),
                                    'blocked_at' => null,
                        ])
                        ->andWhere('confirmed_at IS NOT NULL')
                        ->one();
            if ($user) {
                Yii::$app->getUser()
                         ->login($user, 0);
                return Json::encode(['success' => true]);
            }
            return Json::encode(['success' => false, 'message' => \Yii::t('app', 'No user found in system')]);
        }
        if ($this->module->enableSelectionLogin !== true) {
            Yii::$app->session->setFlash('danger', \Yii::t('app', 'Login by selection is not enabled'));
            $dataProvider = false;
        } else {
            $query = User::find();
            $query->andWhere(['blocked_at' => null])
                  ->andWhere('confirmed_at IS NOT NULL');
            $dataProvider = new ActiveDataProvider([
                'query'      => $query,
                'pagination' => false,
            ]);
        }
        return $this->render($this->loginView, [
            'section'      => 'login-selection',
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Reset the origin site
     * @return Response
     */
    public function actionResetOrigin()
    {
        $cookieName = Yii::$app->getModule('user')->originCookieName;
        Yii::$app->getResponse()->cookies->remove($cookieName);
        return $this->goHome();
    }

    /**
     * Set in a cookie our origin
     * @param string $origin
     */
    protected function setOrigin($origin = null)
    {
        if ($this->module->enableRememberLoginPage) {
            if (empty($origin)) {
                $origin = Yii::$app->request->url;
            }
            $cookieName = $this->module->originCookieName;

            $options = [
                'name'   => $cookieName,
                'value'  => base64_encode($origin),
                'expire' => time() + 86400 * 365,
            ];
            $cookie = new \yii\web\Cookie($options);
            Yii::$app->getResponse()->cookies->add($cookie);
        }
    }

    /**
     * @param $code
     * @return bool
     */
    public function actionVerify($code)
    {
        $userAuthCode = UserAuthCode::find()
                                    ->andWhere(['code' => $code])
                                    ->one();
        if ($userAuthCode) {
            $userAuthCode->status = true;
            $userAuthCode->save();
        }
        return false;
    }

    /**
     * @return array|string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCheckCode()
    {
        $user_id = Yii::$app->request->post('TwoWay')['user_id'];
        $twoWay = new TwoWay($user_id);
        $twoWay->setScenario(TwoWay::SCENARIO_WITH_CODE);
        if ($twoWay->load(Yii::$app->request->post())) {
            $twoWay->checkCode();
            if ($twoWay->status === TwoWay::SUCCESS) {
                $model = \Yii::createObject(LoginForm::className());
                $user = User::findOne($user_id);
                $model->login = $user->username;
                $model->setScenario(LoginForm::TWO_WAY);
                if ($model->login()) {
                    $event = $this->getFormEvent($model);
                    $this->trigger(self::EVENT_AFTER_LOGIN, $event);
                    if (! Yii::$app->request->isAjax) {
                        return $this->redirect(Yii::$app->getUser()
                                                        ->getReturnUrl());
                    }
                }
            }
        }
        if (Yii::$app->request->isAjax) {
            return json_encode([
                'status'    => $twoWay->status,
                'message'   => $twoWay->message,
                'returnUrl' => Yii::$app->getUser()
                                        ->getReturnUrl(),
            ]);
        }
        return $this->render($this->loginView, [
            'section' => $twoWay->view,
            'model'   => $twoWay,
            'module'  => $this->module,
            'qr'      => $twoWay->getCode(),
        ]);
    }

    /**
     * @return bool|string
     */
    public function actionRefreshCode()
    {
        if (! Yii::$app->request->isAjax) {
            return false;
        }

        $twoWay = new TwoWay(Yii::$app->request->post('user_id'));
        $twoWay->code = Yii::$app->request->post('code');

        $code = $twoWay->refreshCode();

        return json_encode([
            'code'      => $code,
            'imageLink' => UserAuthCode::generateQr($code),
        ]);
    }
}
