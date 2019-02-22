<?php

namespace andrej2013\yiiboilerplate\modules\user\controllers;

use dektrium\user\controllers\AdminController as BaseController;
use dektrium\user\filters\AccessRule;
use andrej2013\yiiboilerplate\modules\user\models\Profile;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class AdminController extends BaseController
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin', 'Administrator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => YII_ENV == 'test' ? ['post', 'get'] : ['post'],
                ],
            ],
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws \Exception
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDelete($id)
    {
        if ($id == \Yii::$app->user->getId()) {
            \Yii::$app->getSession()->setFlash('danger', \Yii::t('app', 'You can not remove your own account'));
        } else {
            $model = $this->findModel($id);
            $event = $this->getUserEvent($model);
            $this->trigger(self::EVENT_BEFORE_DELETE, $event);
            try {
                $model->delete();
            } catch (Exception $e) {
                \Yii::$app->getSession()->setFlash('danger', \Yii::t('app', 'Couldn\'t delete the user. Check foreign keys'));
                return $this->redirect(['index']);
            }
            $this->trigger(self::EVENT_AFTER_DELETE, $event);
            \Yii::$app->getSession()->setFlash('success', \Yii::t('app', 'User has been deleted'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Updates an existing profile.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdateProfile($id)
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);
        $profile = $user->profile;

        if ($profile == null) {
            $profile = \Yii::createObject(Profile::className());
            $profile->link('user', $user);
        }
        $event = $this->getProfileEvent($profile);

        $this->performAjaxValidation($profile);

        $this->trigger(self::EVENT_BEFORE_PROFILE_UPDATE, $event);

        if ($profile->load(\Yii::$app->request->post()) && $profile->save()) {
            $profile->uploadFile(
                'picture',
                $_FILES['Profile']['tmp_name'],
                $_FILES['Profile']['name']
            );
            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'Profile details have been updated'));
            $this->trigger(self::EVENT_AFTER_PROFILE_UPDATE, $event);
            return $this->refresh();
        }

        return $this->render('_profile', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

}