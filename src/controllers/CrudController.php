<?php

namespace andrej2013\yiiboilerplate\controllers;

use andrej2013\yiiboilerplate\models\ActiveRecord;
use andrej2013\yiiboilerplate\helpers\CrudHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;
use yii\helpers\Inflector;
use andrej2013\yiiboilerplate\traits\DependingTrait;
use andrej2013\yiiboilerplate\modules\backend\actions\RelatedFormAction;
use andrej2013\yiiboilerplate\modules\backend\widgets\RelatedForms;
use Exception;
use ReflectionClass;


abstract class CrudController extends Controller
{
    /**
     * Each controller extending this must override the model to the namespaced model name
     * @var string
     */
    public $model = '';

    /**
     * The Search model namespace, also required
     * @var string
     */
    public $searchModel = '';

    /**
     * Base model name
     * @var string
     */
    private $baseModel = '';

    /**
     * @var string
     */
    private $translationForeignKey = '';

    /**
     * Extra grid dropdown actions
     * Add grid dropdown actions in the controller's init() function.
     * $this->gridDropdownActions[
     *    'custom' => [
     *      'label' => '<i class="fa fa-globe" aria-hidden="true"></i> ' . Yii::t('app', 'Custom Label'),
     *      'url' => url('route/custom'),
     *      'visible' => Yii::app->getUser()->can('ACL')
     *    ]
     * ];
     * @var array
     */
    public $gridDropdownActions = [];

    /**
     * Extra grid link actions
     * Add grid link actions in the controller's init() function.
     * $this->gridLinkActions = [
     *    'tw' => function ($url, $modal, $key) {
     *       if (!Yii::$app->getUser()->can('ACL')) {
     *          return false;
     *       }
     *       return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
     *          'title' => Yii::t('app', 'Tw'),
     *          'data-toggle' => 'modal',
     *          'data-url' => $url,
     *          'data-pjax' => 1,
     *       ]);
     *    },
     * ];
     * @var array
     */
    public $gridLinkActions = [];

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
     * @var string Which type of related form should show, modal or tab
     */
    protected $relatedTypeForm = RelatedForms::TYPE_MODAL;

    /**
     * @return \andrej2013\yiiboilerplate\models\ActiveRecord
     */
    public function getModel()
    {
        return Yii::createObject($this->model);
    }

    /**
     * @return mixed
     */
    public function getSearchModel()
    {
        return Yii::createObject($this->searchModel);
    }

    /**
     * Initialize the variables used for getting the models
     * @throws Exception
     */
    public function init()
    {
        $reflect = null;
        if (empty($this->model)) {
            $reflect = new ReflectionClass($this);
            // Todo: handle if in modules to automate more
            $this->model = 'app\models\\' . str_replace('Controller', '', $reflect->getShortName());
            //throw new Exception(Yii::t('app', 'Please provide a Model in your controller config.'));
        }
        if (empty($this->searchModel)) {
            if (empty($reflect)) {
                $reflect = new ReflectionClass($this);
            }
            // Todo: handle if in modules to automate more
            $this->searchModel = 'app\models\search\\' . str_replace('Controller', '', $reflect->getShortName());
            //throw new Exception(Yii::t('app', 'Please provide a Search Model in your controller config.'));
        }

        if (empty($this->baseModel)) {
            $this->baseModel = StringHelper::basename($this->model);
        }

        $this->translationForeignKey = strtolower(Inflector::slug(Inflector::camel2words($this->baseModel), '_'));
        return parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => [
                            'index',
                            'view',
                            'create',
                            'update',
                            'delete',
                            'delete-multiple',
                            'related-form',
                            'update-multiple',
                            'entry-details',
                            'list',
                            'depend',
                        ],
                        'roles'   => ['@'],
                    ],
                ],
            ],
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
     * Lists all models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = $this->getSearchModel();
        $dataProvider = $searchModel->search($_GET);

        Tabs::clearLocalStorage();

        Url::remember();
        Yii::$app->session['__crudReturnUrl'] = null;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * Displays a single model.
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id, $viewFull = false)
    {
        // Hook before creating
        $this->beforeView();

        $resolved = Yii::$app->request->resolve();
        $resolved[1]['_pjax'] = null;
        $url = Url::to(array_merge(['/' . $resolved[0]], $resolved[1]));
        Yii::$app->session['__crudReturnUrl'] = Url::previous();
        Url::remember($url);
        Tabs::rememberActiveState();
        return $this->render('view', [
            'model'    => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = $this->getModel();
        $translations = $languageCodes = [];

        // Hook before creating
        $model = $this->beforeCreate($model);

        try {
            // Save actual model
            if ($model->load($_POST) && $model->save()) {
                // Handle uploaded files
                if (! empty($uploadFields = CrudHelper::getUploadFields($this->getModel()))) {
                    foreach ($uploadFields as $uploadField) {
                        $model->uploadFile($uploadField, $_FILES[$this->baseModel]['tmp_name'], $_FILES[$this->baseModel]['name']);
                    }
                }

                // Save translations
                \Yii::$app->session->setFlash('success', \Yii::t('app', 'Saved {model}', ['model' => $model->toString]));
                if (isset($_POST['submit-default'])) {
                    return $this->redirect(['update', 'id' => $model->id]);
                } else if (isset($_POST['submit-new'])) {
                    return $this->redirect(['create']);
                } else {
                    return $this->redirect(['index']);
                }
            } else if (! Yii::$app->request->isPost) {
                $model->load($_GET);
            }
        } catch (Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $translations = [];
        $languageCodes = [];

        // Hook before updating
        $model = $this->beforeUpdate($model);

        if (! $model->editable()) {
            throw new HttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $pass = true;
        $langCheck = method_exists($model, 'getLanguages') ? $pass : true;
        if ($model->load($_POST) && $model->save()) {
            // Handle uploaded files
            if (! empty($uploadFields = CrudHelper::getUploadFields($this->getModel()))) {
                foreach ($uploadFields as $uploadField) {
                    $model->uploadFile($uploadField, $_FILES[$this->baseModel]['tmp_name'], $_FILES[$this->baseModel]['name']);
                }
            }

            // Save translations
            \Yii::$app->session->setFlash('success', \Yii::t('app', 'Saved {model}', ['model' => $model->toString]));
            if (isset($_POST['submit-default'])) {
                return $this->redirect(['update', 'id' => $model->id]);
            } else if (isset($_POST['submit-new'])) {
                return $this->redirect(['create']);
            } else {
                return $this->redirect(['index']);
            }
        } else {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('update', [
                    'model'           => $model,
                ]);
            }
            return $this->render('update', [
                'model'           => $model,
            ]);
        }
    }

    /**
     * Deletes an existing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $model = $this->findModel($id);
            if ($model->deletable()) {
                $model->delete();
            } else {
                throw new HttpException(404, Yii::t('app', 'The requested page does not exist.'));
            }
        } catch (Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            Yii::$app->getSession()
                     ->setFlash('error', $msg);
            return $this->redirect(Url::previous());
        }
        \Yii::$app->session->setFlash('success', \Yii::t('app', 'Deleted {model}', ['model' => $model->toString]));
        if (Yii::$app->request->isAjax) {
            return $this->actionIndex();
        }

        // TODO: improve detection
        $isPivot = strstr('$id', ',');
        if ($isPivot == true) {
            return $this->redirect(Url::previous());
        } else if (isset(Yii::$app->session['__crudReturnUrl']) && Yii::$app->session['__crudReturnUrl'] != '/') {
            Url::remember(null);
            $url = Yii::$app->session['__crudReturnUrl'];
            Yii::$app->session['__crudReturnUrl'] = null;

            // Don't redirect to the view of the model that was just deleted
            if (strpos($url, '/backend') !== false || strpos($url, 'view/' . $id) !== false) {
                return $this->redirect(['index']);
            }

            return $this->redirect($url);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * @return mixed
     */
    public function actionDeleteMultiple()
    {
        $pk = Yii::$app->request->post('pk'); // Array or selected records primary keys
        // Preventing extra unnecessary query
        if (! empty($pk)) {
            foreach ($pk as $id) {
                $model = $this->findModel($id);
                if ($model->deletable()) {
                    $model->delete();
                } else {
                    throw new HttpException(404, Yii::t('app', 'The requested page does not exist.'));
                }
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->actionIndex();
        }
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ActiveRecord the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $modelClass = $this->getModel();
        if (($model = $modelClass->findOne($id)) !== null) {
            return $model;
        } else {
            throw new HttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * Call actions
     */
    public function actions()
    {
        $translations = $languageCodes = [];
        $model = $this->getModel();
        $actions = [
            'related-form' => [
                'class'           => RelatedFormAction::className(),
                'model'           => isset($_GET['id']) ? $this->findModel($_GET['id']) : $this->getModel(),
                'depend'          => isset($_GET['depend']) ? true : false,
                'dependOn'        => isset($_GET['dependOn']) ? true : false,
                'relation'        => isset($_GET['relation']) ? $_GET['relation'] : '',
                'relationId'      => isset($_GET['relationId']) ? $_GET['relationId'] : '',
                'relationIdValue' => isset($_GET['relationIdValue']) ? $_GET['relationIdValue'] : '',
            ],
        ];
        return $actions;
    }

    /**
     * Call a function dynamically
     * @param      $m
     * @param null $q
     * @param null $id
     * @return mixed
     */
    public function actionList($m, $q = null, $id = null)
    {
        $function = lcfirst($m) . 'List';
        return $this->getModel()
                    ->$function($q, $id);
    }

    /**
     * Hook for controllers before a view
     */
    public function beforeView()
    {
        // Do something fun!
    }

    /**
     * Hook for controllers before creating a model
     * @param \andrej2013\yiiboilerplate\models\ActiveRecord $model
     * @return \andrej2013\yiiboilerplate\models\ActiveRecord
     */
    public function beforeCreate(\andrej2013\yiiboilerplate\models\ActiveRecord $model)
    {
        // Change the model
        return $model;
    }

    /**
     * Hook for controllers before updating a model
     * @param andrej2013\yiiboilerplate\models\ActiveRecord $model
     * @return andrej2013\yiiboilerplate\models\ActiveRecord
     */
    public function beforeUpdate(\andrej2013\yiiboilerplate\models\ActiveRecord $model)
    {
        // Change the model
        return $model;
    }
}
