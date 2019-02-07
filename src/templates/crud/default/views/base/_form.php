<?php

use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Inflector;

/**
 * @var yii\web\View $this
 * @var \andrej2013\yiiboilerplate\templates\crud\Generator $generator
 */

/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass();
$model->setScenario('crud');
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->getTableSchema()->columnNames;
}
$urlParams = $generator->generateUrlParams();
echo "<?php\n";
?>

use yii\helpers\ArrayHelper;
use andrej2013\yiiboilerplate\widget\Select2;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\Inflector;
use yii\helpers\Url;
use kartik\helpers\Html;
use andrej2013\yiiboilerplate\widget\DepDrop;
use andrej2013\yiiboilerplate\modules\backend\widgets\RelatedForms;

/**
 * @var yii\web\View $this
 * @var <?= ltrim($generator->modelClass, '\\') ?> $model
 * @var yii\widgets\ActiveForm $form
 * @var boolean $useModal
 * @var boolean $multiple
 * @var array $pk
 * @var array $show
 * @var string $action
 * @var string $owner
 * @var array $languageCodes
 * @var boolean $relatedForm to know if this view is called from ajax related-form action. Since this is passing from
 * select2 (owner) it is always inherit from main form
 * @var string $relatedType type of related form, like above always passing from main form
 * @var string $owner string that representing id of select2 from which related-form action been called. Since we in
 * each new form appending "_related" for next opened form, it is always unique. In main form it is always ID without
 * anything appended
 */

$owner = $relatedForm ? '_' . $owner . '_related' : '';
$relatedTypeForm = Yii::$app->request->get('relatedType')?:$relatedTypeForm;
?>
<div class="<?= \yii\helpers\Inflector::camel2id(
     StringHelper::basename($generator->modelClass),
     '-',
     true
) ?>-form">
    <?php
    $multipart = false;
    // Check for uploaded fields to know which form enctype to generate
    if (!empty($generator->getUploadFields())) {
        $multipart = true;
    }
    // Check in related translation models
    if (method_exists(ltrim($generator->modelClass, '\\'), 'getLanguages')) {
        $translationModelName = '\\' . ltrim(get_class($model), '\\') . 'Translation';
        if (!empty($generator->getUploadFields($translationModelName))) {
            $multipart = true;
        }
    }
    ?>
<?= '<?php ' ?>$form = ActiveForm::begin([
        'fieldClass' => '\andrej2013\yiiboilerplate\widget\ActiveField',
        'id' => '<?= $model->formName() ?>' . ($ajax || $useModal ? '_ajax_' . $owner : ''),
        'layout' => '<?= !$generator->twoColumnsForm ? $generator->formLayout : 'default' ?>',
        'enableClientValidation' => true,
        'errorSummaryCssClass' => 'error-summary alert alert-error',
        'action' => $useModal ? $action : '',
        'options' => [
            'name' => '<?= $model->formName() ?>',
        <?php echo ($multipart === true) ?
            "'enctype' => 'multipart/form-data'
        \n" : ''?>
        ],
    ]);
    ?>

    <div class="">
        <?php echo "<?php \$this->beginBlock('main'); ?>\n"; ?>

        <p>
            <?="<?php if (\$multiple) : ?>\n"?>
                <?="<?=Html::hiddenInput('update-multiple', true)?>\n"?>
                <?="<?php foreach (\$pk as \$id) :?>\n"?>
                    <?="<?=Html::hiddenInput('pk[]', \$id)?>\n"?>
                <?="<?php endforeach;?>\n"?>
            <?="<?php endif;?>\n"?>
            <?php
            foreach ($safeAttributes as $attribute) {
                $column = ArrayHelper::getValue($generator->getTableSchema()->columns, $attribute);
                if (in_array(strtolower($attribute), ['created_at', 'updated_at', 'created_by', 'updated_by'])) {
                    continue;
                }
                if ($column === null) {
                    continue;
                }

                $field = $generator->activeField($attribute, $model);

                // Find foreign-key columns for dropdown-Fields
                // check if column is no primarykey and column->name ends with "_id"
                $isForeignColumn =
//                    !$column->isPrimaryKey &&
                    (($temp = strlen($column->name) - strlen('_id')) >= 0 &&
                        strpos($column->name, '_id', $temp) !== false
                    );

                if ($isForeignColumn) {
                    $field = $generator->activeFieldDropDown($column, $model);
                }

                $prepend = $generator->prependActiveField($attribute, $model);
                $append = $generator->appendActiveField($attribute, $model);

                if ($prepend) {
                    echo "\n" . str_repeat(" ", 12) . $prepend;
                }
                if ($field) {
                    echo $generator->twoColumnsForm ? "\n" . str_repeat(" ", 8) . "<div class=\"col-md-6\">" : '';
                    echo "\n" . str_repeat(" ", 12) .
                        "<?php if (!\$multiple || (\$multiple && isset(\$show['$attribute']))) :?>";
                    echo "\n" . str_repeat(" ", 12) . "<?= " . $field . "\n" . str_repeat(" ", 12) . "?>";
                    echo "\n" . str_repeat(" ", 12) . "<?php endif; ?>";
                    echo $generator->twoColumnsForm ? "\n" . str_repeat(" ", 8) . "</div>" : '';
                }
                if ($append) {
                    echo "\n" . str_repeat(" ", 12) . $append;
                }
                ?>
            <?php
            }
            ?>
<?php if (method_exists(ltrim($generator->modelClass, '\\'), 'getLanguages')) { ?>
            <?php echo "\n<?php" ?> if (isset($translations)) {
<?php
    $translationModelName = '\\' . ltrim(get_class($model), '\\') . 'Translation';
    $translationModel = new $translationModelName();
    $translationAttributes = $translationModel->attributes;
    $mainClass = $generator->modelClass;
    $modelName = get_class($translationModel);
    $reflect = new ReflectionClass($translationModel);
    $modelName = $reflect->getShortName();
    $reflect = new ReflectionClass($mainClass);
    $mainModelName = strtolower(Inflector::slug(Inflector::camel2words($reflect->getShortName()), '_'));
    $generator->modelClass = ltrim($translationModelName, '\\');
?>
    foreach ($translations as $key => $translation) {
        echo '<filedset><legend style="background-color: #fafafa"><div  class="col-sm-offset-2">' .
            $languageCodes[$key] .
            ' ' .
            Yii::t('app', 'Translation') .
            '</div></legend></filedset>';
?>
<?php
foreach ($translationAttributes as $attribute => $value) {
    if (in_array(
        strtolower($attribute),
        ['created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by']
    )) {
            continue;
    }
    $hidden = false;
    $name = $modelName . '[$key][' . $modelName . '][' . $attribute . ']';
    if (!substr_compare($attribute, 'id', -2) || !substr_compare($attribute, '_id', -3)) {
        $hidden = true;
    }
    $field = $generator->generateActiveField($attribute, $name, $hidden, true, true, 2);
//    $field = $generator->generateActiveField($attribute, $name, $hidden, null);

    if ($field) {
        if ($attribute === $mainModelName . '_id') {
            echo "\n" . str_repeat(' ', 20) . "<?php\n";
        ?>
                    if (!$model->isNewRecord) { ?><?php
        }
                        echo "\n" . str_repeat(' ', 20) . "<?= " . $field . ' ?>';
        if ($attribute === $mainModelName . '_id') {
                            echo "\n" . str_repeat(' ', 20) . "<?php
                    }
                    ?>";
        }
    }
}
                ?>
                <?php
                echo "\n<?php\n";
                ?>
    }
}
?>
<?php
$generator->modelClass = $mainClass;
}
            ?>
        </p>
        <?php echo '<?php $this->endBlock(); ?>'; ?>

        <?php
        $label = substr(strrchr($model::className(), '\\'), 1);
        $items = "[
                    'label' => Yii::t('app', Inflector::camel2words('$label')),
                    'content' => \$this->blocks['main'],
                    'active' => true,
                ],";?>

        <?=
        "<?= (\$relatedType != RelatedForms::TYPE_TAB) ?
            Tabs::widget([
                'encodeLabels' => false,
                'items' => [
                    $items
                ]
            ])
            : \$this->blocks['main']
        ?>";
        ?>
        <?=$generator->twoColumnsForm ? "\n" . str_repeat(" ", 8) . "<div class=\"col-md-12\">\n" : '' ?>
        <hr/>
        <?=$generator->twoColumnsForm ? "\n" . str_repeat(" ", 8) . "</div>\n" : '' ?>
        <?=$generator->twoColumnsForm ? "\n" . str_repeat(" ", 8) . "<div class=\"clearfix\"></div>\n" : '' ?>
        <?= '<?php ' ?>echo $form->errorSummary($model); ?>
        <?= $generator->twoColumnsForm ? "<div class=\"col-md-6\"<?=!\$relatedForm ? ' id=\"main-submit-buttons\"' : ''?>>\n" : '' ?>
        <?= "<?= " ?>Html::submitButton(
            '<span class="glyphicon glyphicon-check"></span> ' .
            ($model->isNewRecord && !$multiple ?
                <?= $generator->generateString(
                    'Create'
                ) ?> :
                <?= $generator->generateString('Save') ?>),
            [
                'id' => 'save-' . $model->formName(),
                'class' => 'btn btn-success',
                'name' => 'submit-default'
            ]
        );
        ?>

        <?= "<?php " ?>if ((!$relatedForm && !$useModal) && !$multiple) { ?>
            <?= "<?= " ?>Html::submitButton(
                '<span class="glyphicon glyphicon-check"></span> ' .
                ($model->isNewRecord && !$multiple ?
                    <?= $generator->generateString(
                        'Create & New'
                    ) ?> :
                    <?= $generator->generateString('Save & New') ?>),
                [
                    'id' => 'save-' . $model->formName(),
                    'class' => 'btn btn-default',
                    'name' => 'submit-new'
                ]
            );
            ?>
            <?= "<?= " ?>Html::submitButton(
                '<span class="glyphicon glyphicon-check"></span> ' .
                ($model->isNewRecord && !$multiple ?
                    <?= $generator->generateString(
                        'Create & Close'
                    ) ?> :
                    <?= $generator->generateString('Save & Close') ?>),
                [
                    'id' => 'save-' . $model->formName(),
                    'class' => 'btn btn-default',
                    'name' => 'submit-close'
                ]
            );
            ?>

            <?= "<?php " ?>if (!$model->isNewRecord && Yii::$app->getUser()->can(Yii::$app->controller->module->id .
                    '_' . Yii::$app->controller->id . '_delete') && $model->deletable()) { ?>
                <?= "<?= " ?>Html::a(
                    '<span class="glyphicon glyphicon-trash"></span> ' .
                    <?= $generator->generateString('Delete') ?>,
                    ['delete', <?= $urlParams ?>],
                    [
                        'class' => 'btn btn-danger',
                        'data-confirm' => '' . <?= $generator->generateString(
                            'Are you sure to delete this item?'
                        ) ?> . '',
                        'data-method' => 'post',
                    ]
                );
                ?>
            <?= "<?php " ?>} ?>
        <?= "<?php " ?>} elseif ($multiple) { ?>
                <?= "<?= " ?>Html::a(
                    '<span class="glyphicon glyphicon-exit"></span> ' .
                    <?= $generator->generateString('Close') ?>,
                    $useModal ? false : [],
                    [
                        'id' => 'save-' . $model->formName(),
                        'class' => 'btn btn-danger' . ($useModal ? ' closeMultiple' : ''),
                        'name' => 'close'
                    ]
                );
                ?>
        <?= "<?php " ?>} else { ?>
            <?= "<?= " ?>Html::a(
                '<span class="glyphicon glyphicon-exit"></span> ' .
                <?= $generator->generateString('Close') ?>,
                ['#'],
                [
                    'class' => 'btn btn-danger',
                    'data-dismiss' => 'modal',
                    'name' => 'close'
                ]
            );
            ?>
        <?= "<?php " ?>} ?>
        <?= $generator->twoColumnsForm ? "</div>\n" : '' ?>
        <?= "<?php " ?>ActiveForm::end(); ?>
        <?= "<?="?> ($relatedForm && $relatedType == \andrej2013\yiiboilerplate\modules\backend\widgets\RelatedForms::TYPE_MODAL) ?
            '<div class="clearfix"></div>' :
            ''
        ?>
        <div class="clearfix"></div>
    </div>
</div>
<?= "<?php\n" ?>
if ($useModal) {
    \andrej2013\yiiboilerplate\modules\backend\assets\ModalFormAsset::register($this);
}
?>