<?php
/**
 * /srv/www/nassi-v2/src/../runtime/giiant/358b0e44f1c1670b558e36588c267e47
 *
 * @package default
 */


namespace andrej2013\yiiboilerplate\modules\backend\controllers;

use app\models\Address;
use app\models\search\Address as AddressSearch;
use andrej2013\yiiboilerplate\models\ArHistory;
use andrej2013\yiiboilerplate\models\search\ArHistorySearch;
use yii\web\Controller;
use yii\web\HttpException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use dmstr\bootstrap\Tabs;
use andrej2013\yiiboilerplate\traits\DependingTrait;
use andrej2013\yiiboilerplate\modules\backend\actions\RelatedFormAction;
use andrej2013\yiiboilerplate\modules\backend\widgets\RelatedForms;

/**
 * AddressController implements the CRUD actions for Address model.
 */
class HistoryController extends Controller
{

    /**
     * Depending Trait for Crud generation
     */
    use DependingTrait;

    /**
     *
     * @var boolean whether to enable CSRF validation for the actions in this controller.
     * CSRF validation is enabled only when both this property and [[Request::enableCsrfValidation]] are true.
     */
    public $enableCsrfValidation = false;

    /**
     *
     * @var boolean whether to enable inline sub-forms
     */
    protected $inlineForm = false;

    /**
     *
     * @var boolean whether to enable modal editing / viewing
     */
    protected $useModal = false;

    /**
     *
     * @var boolean show import option
     */
    protected $importer = false;

    /**
     *
     * @var string Which type of related form should show, modal or tab
     */
    protected $relatedTypeForm = RelatedForms::TYPE_TAB;

    /**
     *
     * @inheritdoc
     * @return unknown
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
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
                            'depend'
                        ],
                        'roles' => ['@']
                    ]
                ]
            ]
        ];
    }


    /**
     *
     * @inheritdoc
     * @param unknown $action
     * @return unknown
     */
    public function beforeAction($action) {
        return parent::beforeAction($action);
    }


    /**
     * Lists all Address models.
     *
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ArHistorySearch();
        $dataProvider = $searchModel->search($_GET);

        Tabs::clearLocalStorage();

        Url::remember();
        \Yii::$app->session['__crudReturnUrl'] = null;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'useModal' => $this->useModal,
            'importer' => $this->importer,
        ]);
    }


    /**
     * Displays a single Address model.
     *
     * @param integer $id
     * @param unknown $viewFull (optional)
     * @return mixed
     */
    public function actionView($id, $viewFull = false) {
        $resolved = \Yii::$app->request->resolve();
        $resolved[1]['_pjax'] = null;
        $url = Url::to(array_merge(['/' . $resolved[0]], $resolved[1]));
        \Yii::$app->session['__crudReturnUrl'] = Url::previous();
        Url::remember($url);
        Tabs::rememberActiveState();
        if ($this->useModal && !$viewFull) {
            return $this->renderAjax('view', [
                'model' => $this->findModel($id),
                'useModal' => $this->useModal,
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
                'useModal' => $this->useModal,
            ]);
        }
    }


    /**
     * Creates a new Address model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate() {
        $model = new ArHistory();
        try {
            if ($model->load($_POST) && $model->save()) {
                if ($this->useModal) {
                    return $this->actionIndex();
                }
                if (isset($_POST['submit-default'])) {
                    return $this->redirect(['update', 'id' => $model->id]);
                } elseif (isset($_POST['submit-new'])) {
                    return $this->redirect(['create']);
                } else {
                    return $this->redirect(['index']);
                }
            } elseif (!\Yii::$app->request->isPost) {
                $model->load($_GET);
            }
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
        }
        if ($this->useModal) {
            return $this->renderAjax('_form', [
                'model' => $model,
                'action' => Url::toRoute('create'),
                'useModal' => $this->useModal,
                'inlineForm' => $this->inlineForm,
                'relatedTypeForm' => $this->relatedTypeForm,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'inlineForm' => $this->inlineForm,
                'relatedTypeForm' => $this->relatedTypeForm,
            ]);
        }
    }


    /**
     * Updates an existing Address model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load($_POST) && $model->save()) {
            if ($this->useModal) {
                return $this->actionIndex();
            }
            if (isset($_POST['submit-default'])) {
                return $this->redirect(['update', 'id' => $model->id]);
            } elseif (isset($_POST['submit-new'])) {
                return $this->redirect(['create']);
            } else {
                return $this->redirect(['index']);
            }
        } else {
            if ($this->useModal) {
                return $this->renderAjax('_form', [
                    'model' => $model,
                    'action' => Url::toRoute(['update', 'id' => $model->id]),
                    'useModal' => $this->useModal,
                    'inlineForm' => $this->inlineForm,
                    'relatedTypeForm' => $this->relatedTypeForm,
                ]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'inlineForm' => $this->inlineForm,
                    'relatedTypeForm' => $this->relatedTypeForm,
                ]);
            }
        }
    }


    /**
     * Deletes an existing Address model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        try {
            $this->findModel($id)->delete();
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            \Yii::$app->getSession()->setFlash('error', $msg);
            return $this->redirect(Url::previous());
        }


        if (\yii::$app->request->isAjax) {
            return $this->actionIndex();
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


    /**
     *
     * @return unknown
     */
    public function actionDeleteMultiple() {
        $pk = \Yii::$app->request->post('pk'); // Array or selected records primary keys
        // Preventing extra unnecessary query
        if (!empty($pk)) {
            foreach ($pk as $id) {
                $this->findModel($id)->delete();
            }
        }
        if (\yii::$app->request->isAjax) {
            return $this->actionIndex();
        }
    }


    /**
     * Finds the Address model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @throws HttpException if the model cannot be found
     * @param integer $id
     * @return Address the loaded model
     */
    protected function findModel($id) {
        if (($model = ArHistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }


    /**
     * Update multiple models at once
     *
     * @return unknown
     */
    public function actionUpdateMultiple() {
        if (($_POST['no-post'])) {
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                if (\yii::$app->request->isAjax) {
                    return $this->actionIndex();
                }
                return $this->redirect('index');
            }
            $model = new Address();
            $show = [];
            foreach ($_POST as $element => $value) {
                $show[$element] = $value;
            }
            $method = 'render' . ($this->useModal ? 'Ajax' : '');
            return $this->$method('update-multiple', [
                'model' => $model,
                'show' => $show,
                'pk' => $_POST['id'],
                'useModal' => $this->useModal,
                'action' => Url::toRoute('update-multiple'),
            ]);
        } else {
            if (!isset($_POST['pk']) || isset($_POST['close'])) {
                if (\yii::$app->request->isAjax) {
                    return $this->actionIndex();
                }
                return $this->redirect('index');
            }
            foreach ($_POST['pk'] as $id) {
                $model = $this->findModel($id);
                $model->load($_POST);
                $model->save(false);
            }
            if (\yii::$app->request->isAjax) {
                return $this->actionIndex();
            }
            return $this->redirect('index');
        }
    }


    /**
     * Get details of one entry
     *
     * @param integer $id
     */
    public function actionEntryDetails($id) {
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
     *
     * @param unknown $m
     * @param null    $q  (optional)
     * @param null    $id (optional)
     * @return mixed
     */
    public function actionList($m, $q = null, $id = null) {
        $function = lcfirst($m) . 'List';
        return ArHistory::$function($q, $id);
    }


}
