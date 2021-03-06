<?php

namespace andrej2013\yiiboilerplate\modules\backend\controllers;

use app\models\User;
use dmstr\helpers\Metadata;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Default backend controller.
 *
 * Usually renders a customized dashboard for logged in users
 */
class DefaultController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['error'],
                    ],
                    [
                        'allow'         => true,
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->can($this->module->id . '_' . $this->id . '_' . $action->id, ['route' => true]);
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Actions defined in classes, eg. error page.
     *
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Application dashboard.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Application configuration.
     *
     * @return string
     */
    public function actionViewConfig()
    {
        $loadedModules = Metadata::getModules();
        $loadedModulesDataProvider = new ArrayDataProvider(['allModels' => $loadedModules]);

        return $this->render('view-config', [
            'params'                    => Yii::$app->params,
            'components'                => Yii::$app->getComponents(),
            'modules'                   => Yii::$app->getModules(),
            'loadedModulesDataProvider' => $loadedModulesDataProvider,
        ]);
    }

    public function actionSwitchUser()
    {
        if (Yii::$app->user->can('Authority') || Yii::$app->session->get('user.isAdmin')) {
            $user_id = Yii::$app->request->post('id');
            $user = User::findOne($user_id);
            Yii::$app->session->set('user.isAdmin', true);
            Yii::$app->user->switchIdentity($user);
            return true;
        }
        return false;
    }
}
