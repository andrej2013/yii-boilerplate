<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

namespace andrej2013\yiiboilerplate\modules\faq\controllers\base;

use andrej2013\yiiboilerplate\modules\faq\models\Faq;
use andrej2013\yiiboilerplate\modules\faq\models\FaqSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use dmstr\bootstrap\Tabs;
use andrej2013\yiiboilerplate\traits\DependingTrait;

/**
 * FaqController implements the CRUD actions for Faq model.
 */
class FaqController extends Controller
{

    /**
     * Depending Trait for Crud generation
     */
    use DependingTrait;

    /**
     * @var boolean whether to enable CSRF validation for the actions in this controller.
     * CSRF validation is enabled only when both this property and [[Request::enableCsrfValidation]] are true.
     */
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->accessRoles
                    ]
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

    /**
     * Lists all Faq models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FaqSearch;
        $dataProvider = $searchModel->search($_GET);

        Tabs::clearLocalStorage();

        Url::remember();
        \Yii::$app->session['__crudReturnUrl'] = null;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Faq model.
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $resolved = \Yii::$app->request->resolve();
        $resolved[1]['_pjax'] = null;
        $url = Url::to(array_merge(['/' . $resolved[0]], $resolved[1]));
        \Yii::$app->session['__crudReturnUrl'] = Url::previous();
        Url::remember($url);
        Tabs::rememberActiveState();

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Faq model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Faq;
        try {
            if ($model->load($_POST) && $model->save()) {
                if (isset($_POST['submit-default']))
                    return $this->redirect(['update', 'id' => $model->id]);
                else if (isset($_POST['submit-new']))
                    return $this->redirect(['create']);
                else
                    return $this->redirect(['index']);
            } elseif (!\Yii::$app->request->isPost) {
                $model->load($_GET);
            }
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Faq model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load($_POST) && $model->save()) {
            if (isset($_POST['submit-default']))
                return $this->redirect(['update', 'id' => $model->id]);
            else if (isset($_POST['submit-new']))
                return $this->redirect(['create']);
            else
                return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Faq model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            \Yii::$app->getSession()->setFlash('error', $msg);
            return $this->redirect(Url::previous());
        }

        // TODO: improve detection
        $isPivot = strstr('$id', ',');
        if ($isPivot == true) {
            return $this->redirect(Url::previous());
        } elseif (isset(\Yii::$app->session['__crudReturnUrl']) && \Yii::$app->session['__crudReturnUrl'] != '/') {
            Url::remember(null);
            $url = \Yii::$app->session['__crudReturnUrl'];
            \Yii::$app->session['__crudReturnUrl'] = null;

            return $this->redirect($url);
        } else {
            return $this->redirect(['index']);
        }
    }


    public function actionDeleteMultiple()
    {
        $pk = \Yii::$app->request->post('pk'); // Array or selected records primary keys
        // Preventing extra unnecessary query
        if (!$pk) {
            return;
        }
        foreach ($pk as $id) {
            $this->findModel($id)->delete();
        }
        //return Faq::deleteAll(['id' => $pk]);
    }

    /**
     * Finds the Faq model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Faq the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Faq::findOne($id)) !== null) {
            return $model;
        } else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }

    /**
     * Call actions
     */
    public function actions()
    {
        parent::actions();
        return [
            'ajax' => [
                'class' => '\andrej2013\yiiboilerplate\action\AjaxRelatedAction',
                'viewFile' => '_form',
                'model' => isset($_GET['id']) ? $this->findModel($_GET['id']) : new Faq(),
            ]
        ];
    }


    /**
     * Update multiple models at once
     */
    public function actionUpdateMultiple()
    {
        if (($_POST['no-post'])) {
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                return $this->redirect('index');
            }
            $model = new Faq();
            foreach ($_POST as $element => $value) {
                $show[$element] = $value;
            }
            return $this->render('update-multiple', [
                'model' => $model,
                'show' => $show,
                'pk' => $_POST['id'],
            ]);
        } else {
            if (!isset($_POST['pk']) || isset($_POST['close'])) {
                return $this->redirect('index');
            }
            foreach ($_POST['pk'] as $id) {
                $model = $this->findModel($id);
                $model->load($_POST);
                $model->save(false);
            }
            return $this->redirect('index');
        }
        return $this->redirect('index');
    }

    /**
     * Get details of one entry
     */
    public function actionEntryDetails($id)
    {
        $model = $this->findModel($id);
        if ($model) {
            $output = [
                'success' => true,
                'data' => $model->entryDetails,
            ];
        } else {
            $output = [
                'success' => false,
                'message' => 'Model does not exist',
            ];
        }
        echo json_encode($output);
    }

    /**
     * Call a function dynamically
     */
    public function actionList($q = null, $m)
    {
        $function = $m . 'List';
        return Faq::$function($q);
    }

    public static function getSubList($cat_id, $on, $onRelation)
    {
        $models = Faq::find()->select(['id', 'name' => 'title'])->where(['language_id' => $cat_id])->asArray()->all();
        $root[0] = ['id' => (string) \andrej2013\yiiboilerplate\modules\faq\models\Faq::ROOT_LEVEL, 'name' => \Yii::t('app','Root')];
        return ArrayHelper::merge($root,$models);
    }

}
