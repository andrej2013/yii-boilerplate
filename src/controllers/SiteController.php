<?php

namespace andrej2013\yiiboilerplate\controllers;

use app\components\Helper;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Site controller.
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['error'],
                    ],
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->can(
                                $this->module->id.'_'.$this->id.'_'.$action->id,
                                ['route' => true]
                            );
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Renders the start page.
     *
     * @return string
     */
    public function actionIndex()
    {
        Helper::checkApplication();

        return $this->render('index');
    }
    
    /**
     * action to access images with options to process on-the-fly
     * @param $path
     * @param null $bucket filesystem component: fs, fs_assetsprod, ...
     * @param null $width if only one of width|height is set, the other will be calculated by original ratio
     * @param null $height
     * @param null $format
     * @param null $quality
     */
    public function actionImage($path, $bucket = null, $width = null, $height = null, $format = null, $quality = null)
    {
        Helper::processImage($path, compact('bucket', 'width', 'height', 'format', 'quality'), true);
    }

    /**
     * Update the application theme if needed
     * @param $action
     * @deprecated no longer in use, covered by the global as beforeAction in the config
     */
    public function beforeAction($action)
    {
        // Do we have themes?
        if (!empty(Yii::$app->params['themes'])) {
            // Get the theme from the cookie (vs session?')
            if (Yii::$app->getRequest()->cookies['applicationTheme']) {
                $theme = Yii::$app->getRequest()->cookies->getValue('applicationTheme');

                // Make sure it's a valid theme
                if (in_array($theme, Yii::$app->params['themes'])) {
                    Yii::$app->view->theme->pathMap['@app/views'] = [
                        '@app/themes/' . $theme,
                        '@andrej2013-backend-views',
                    ];
                } else {
                    // Delete the theme
                    Yii::$app->getResponse()->cookies->remove('applicationTheme');
                }
            }
        }

        return parent::beforeAction($action);
    }

    /**
     * Switch themes of the application by saving it in the cookie
     * @param $theme
     * @return mixed
     */
    public function actionThemeSwitch($theme)
    {
        if ($theme == 'reset') {
            Yii::$app->getResponse()->cookies->remove('applicationTheme');
        } else {
            $options = [
                'name' => 'applicationTheme',
                'value' => $theme, 'expire' => time() + 86400 * 365
            ];
            $cookie = new \yii\web\Cookie($options);
            Yii::$app->getResponse()->cookies->add($cookie);
        }

        return $this->redirect(['/']);
    }
}
