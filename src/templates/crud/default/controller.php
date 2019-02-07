<?php

use yii\helpers\StringHelper;
use yii\helpers\Inflector;

/**
 * This is the template for generating a CRUD controller class file.
 *
 * @var yii\web\View $this
 * @var andrej2013\yiiboilerplate\templates\crud\Generator $generator
 */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$translationForeignKey = strtolower(Inflector::slug(Inflector::camel2words($modelClass), '_'));
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass.'Search';
}

$pks = $generator->getTableSchema()->primaryKey;
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

$primaryKeys = $generator->getPrimaryKeys();
$primaryKeysList = '';
foreach ($primaryKeys as $primaryKey) {
    $primaryKeysList .= "'$primaryKey' => " . '$model->' . $primaryKey . ', ';
}
$primaryKeysList = substr($primaryKeysList, 0, strlen($primaryKeysList)-2);

$hasMultiParams = count($primaryKeys) > 1;


echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>\base;

use <?= ltrim($generator->baseControllerClass, '\\') ?>;
<?php if ($hasMultiParams) : ?>
use <?= ltrim($generator->modelClass, '\\') ?>;
use <?= ltrim($generator->searchModelClass, '\\') ?><?= isset($searchModelAlias) ? ' as ' . $searchModelAlias : ''?>;
use yii\web\HttpException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use dmstr\bootstrap\Tabs;
use andrej2013\yiiboilerplate\traits\DependingTrait;
use andrej2013\yiiboilerplate\modules\backend\actions\RelatedFormAction;
use andrej2013\yiiboilerplate\modules\backend\widgets\RelatedForms;
<?php endif; ?>

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{

<?php if ($hasMultiParams): ?>
    /**
     * Displays a single <?= $modelClass ?> model.
     * <?= implode("\n\t * ", $actionParamComments) . "\n" ?>
     *
     * @return mixed
     */
    public function actionView(<?= $actionParams ?>, $viewFull = false)
    {
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
                'model' => $this->findModel(<?= $actionParams ?>),
                'useModal' => $this->useModal,
            ]);
        }
    }

    /**
     * Creates a new <?= $modelClass ?> model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new <?= $modelClass ?>;
<?php
$modelClassPk = new $generator->modelClass;
if ($modelClassPk->tableSchema->primaryKey == null || count($modelClassPk->tableSchema->primaryKey) == 0) {
        throw new \yii\base\Exception("The Model " . $modelClass . " has no PrimaryKey from its tableSchema!");
}
$pk = $modelClassPk->tableSchema->primaryKey[0];
if (method_exists($modelClassPk, 'getLanguages')) { ?>
        $pass = true;
        $languages = $model->getLanguages();
        $translations = [];
        $languageCodes = [];
        foreach ($languages as $language) {
            $translation = new \<?= ltrim($generator->modelClass, '\\') ?>Translation();
            $translation->language_id = $language->language_id;
            $languageCodes[$language->language_id] = $language->name_ascii;
            $translations[$language->language_id] = $translation;
        }
<?php } ?>
        try {
<?php if (method_exists($modelClassPk, 'getLanguages')) { ?>
            if (\Yii::$app->request->isPost) {
                foreach ($_POST['<?=$modelClass ?>Translation'] as $related) {
                    if (!is_array($related)) {
                        continue;
                    }
                    $translation = new \<?= ltrim($generator->modelClass, '\\') ?>Translation();
                    $translation->load($related);
                    $attributes = $translation->attributes;
                    unset($attributes['<?=$translationForeignKey ?>_id']);
                    foreach ($attributes as $attribute => $value) {
                        $validateAttributes[] = $attribute;
                    }
                    if (!$translation->validate($validateAttributes)) {
                        $pass = false;
                        break;
                    }
                }
            }
<?php } ?>
            if (<?=method_exists($modelClassPk, 'getLanguage')?'$pass && ':''?>$model->load($_POST) && $model->save()) {
<?php if (method_exists($modelClassPk, 'getLanguages')) { ?>
                foreach ($_POST['<?=$modelClass ?>Translation'] as $related) {
                    if (!is_array($related)) {
                        continue;
                    }
                    $translation = new \<?= ltrim($generator->modelClass, '\\') ?>Translation();
                    $translation->load($related);
                    $translation-><?=$translationForeignKey ?>_id = $model->id;
                    $translation->save();
                }
<?php } ?>
<?php if (!empty($uploadFields = $generator->getUploadFields())) {
foreach ($uploadFields as $uploadField) { ?>
                $model->uploadFile('<?= $uploadField ?>', $_FILES['<?= $modelClass ?>']['tmp_name'], $_FILES['<?= $modelClass ?>']['name']);
<?php }
} ?>
                if ($this->useModal) {
                    return $this->actionIndex();
                }
                if (isset($_POST['submit-default'])) {
<?php if (count($pks) == 1) : ?>
                    return $this->redirect(['update', '<?=$pk?>' => $model-><?=$pk?>]);
<?php else : ?>
                    return $this->redirect(['update', <?=$primaryKeysList?>]);
<?php endif; ?>
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
                <?php if (method_exists($modelClassPk, 'getLanguages')) {
                ?>'translations' => $translations,
                'languageCodes' => $languageCodes,
                <?php } ?>]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'inlineForm' => $this->inlineForm,
                'relatedTypeForm' => $this->relatedTypeForm,
                <?php if (method_exists($modelClassPk, 'getLanguages')) {
                ?>'translations' => $translations,
                'languageCodes' => $languageCodes,
            <?php } ?>]);
        }
    }

    /**
     * Updates an existing <?= $modelClass ?> model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * <?= implode("\n\t * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);
<?php if (method_exists($modelClassPk, 'getLanguages')) { ?>
        $pass = true;
        $languages = $model->getLanguages();
        $translations = [];
        $languageCodes = [];
        foreach ($languages as $language) {
            $translation = \<?= ltrim($generator->modelClass, '\\')
    ?>Translation::findOne([
                'language_id' => $language->language_id,
                '<?= $translationForeignKey ?>_id' => $model->id
            ]);
            if (!$translation) {
                $translation = new \<?= ltrim($generator->modelClass, '\\') ?>Translation();
                $translation->language_id = $language->language_id;
                $translation-><?= $translationForeignKey ?>_id = $model->id;
            }
            $languageCodes[$language->language_id] = $language->name_ascii;
            $translations[$language->language_id] = $translation;
        }
        if (\Yii::$app->request->isPost) {
            foreach ($_POST['<?=$modelClass ?>Translation'] as $related) {
                if (!is_array($related)) {
                    continue;
                }
                $translation = new \<?= ltrim($generator->modelClass, '\\') ?>Translation();
                $translation->load($related);
                $translation-><?= $translationForeignKey ?>_id = $id;
                if (!$translation->validate()) {
                    $pass = false;
                    break;
                }
            }
        }
<?php } ?>
        if (<?= method_exists($modelClassPk, 'getLanguages') ? '$pass && ':''?>$model->load($_POST) && $model->save()) {
<?php if (method_exists($modelClassPk, 'getLanguages')) { ?>
            foreach ($_POST['<?=$modelClass ?>Translation'] as $related) {
                if (!is_array($related)) {
                    continue;
                }
                $translation = \<?= ltrim($generator->modelClass, '\\')
    ?>Translation::findOne([
                    'language_id' => $related['<?=$modelClass
    ?>Translation']['language_id'],
                    '<?=$translationForeignKey ?>_id'=>$model->id
                ]);
                if (!$translation) {
                    $translation = new \<?= ltrim($generator->modelClass, '\\') ?>Translation();
                }
                $translation->load($related);
                $translation->save();
            }
<?php } ?>
            if ($this->useModal) {
                return $this->actionIndex();
            }
            if (isset($_POST['submit-default'])) {
<?php if (count($pks) == 1) : ?>
                return $this->redirect(['update', '<?=$pk?>' => $model-><?=$pk?>]);
<?php else : ?>
                return $this->redirect(['update', <?=$primaryKeysList?>]);
<?php endif; ?>
            } elseif (isset($_POST['submit-new'])) {
                return $this->redirect(['create']);
            } else {
                return $this->redirect(['index']);
            }
        } else {
            if ($this->useModal) {
                return $this->renderAjax('_form', [
                    'model' => $model,
                    'action' => Url::toRoute(['update', <?=count($pks) == 1 ?
                    "'$pk' => \$model->$pk" :
                    $primaryKeysList ?>]),
                    'useModal' => $this->useModal,
                    'inlineForm' => $this->inlineForm,
                    'relatedTypeForm' => $this->relatedTypeForm,
                    <?php if (method_exists($modelClassPk, 'getLanguages')) {
                    ?>'translations' => $translations,
                    'languageCodes' => $languageCodes,
                <?php } ?>]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'inlineForm' => $this->inlineForm,
                    'relatedTypeForm' => $this->relatedTypeForm,
                    <?php if (method_exists($modelClassPk, 'getLanguages')) {
                    ?>'translations' => $translations,
                    'languageCodes' => $languageCodes,
                <?php } ?>]);
            }
        }
    }

    /**
     * Deletes an existing <?= $modelClass ?> model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * <?= implode("\n\t * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionDelete(<?= $actionParams ?>)
    {
        try {
            $this->findModel(<?= $actionParams ?>)->delete();
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            \Yii::$app->getSession()->setFlash('error', $msg);
            return $this->redirect(Url::previous());
        }

        if (\yii::$app->request->isAjax) {
            return $this->actionIndex();
        }

        // TODO: improve detection
        $isPivot = strstr('<?= $actionParams ?>', ',');
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
     * Finds the <?= $modelClass ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n\t * ", $actionParamComments) . "\n" ?>
     * @return <?= $modelClass ?> the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel(<?= $actionParams ?>)
    {
<?php
if (count($pks) === 1) {
    $condition = '$'.$pks[0];
} else {
    $condition = [];
    foreach ($pks as $pk) {
        $condition[] = "'$pk' => \$$pk";
    }
    $condition = '[' . implode(', ', $condition) . ']';
}
?>
        if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        } else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }
<?php endif; ?>
}
