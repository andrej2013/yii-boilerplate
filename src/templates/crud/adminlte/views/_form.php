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
 * @var array $pk
 * @var array $show
 * @var string $action
 * @var boolean $is_popup value to know if this form is in popup
 * @var string $caller_id represent id of select2 input from which is popup opened. In main form it is always ID without anything appended
 */

$caller_id = $is_popup ? ('_from_' . $caller_id) : '';
?>
<div class="hide header_title"><?php echo '<?php'; ?> echo Html::tag('h4', Yii::t('app', '<?= Inflector::camel2words(
     StringHelper::basename($generator->modelClass)
)?>'));?></div>
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
    ?>
<?= '<?php ' ?>$form = ActiveForm::begin([
        'fieldClass' => '\andrej2013\yiiboilerplate\widget\ActiveField',
        'id' => '<?= $model->formName() ?>' . ($is_popup ? '_popup_' . $caller_id : ''),
        'layout' => '<?= !$generator->twoColumnsForm ? $generator->formLayout : 'default' ?>',
        'enableClientValidation' => true,
        'errorSummaryCssClass' => 'error-summary alert alert-error',
        'action' => $action,
        'options' => [
            'name' => '<?= $model->formName() ?>',
        <?php echo ($multipart === true) ?
            "'enctype' => 'multipart/form-data'
        \n" : ''?>
        ],
    ]);
    ?>
<?php echo "<?php \$this->beginBlock('main'); ?>\n"; ?>
<?php
            $lines = [];
            foreach ($safeAttributes as $attribute) {
                $column = ArrayHelper::getValue($generator->getTableSchema()->columns, $attribute);
                if (in_array(strtolower($attribute), $generator->hidden_attributes) || $column === null) {
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

                if ($field) {
                    if ($generator->twoColumnsForm) {
                        $lines[] = \yii\helpers\Html::beginTag('div', ['class' => 'col-sm-6']);
                    }
                    if ($prepend) {
                        $lines[] = $prepend;
                    }
                    $lines[] = "<?= " . $field . "?>";
                    if ($append) {
                        $lines[] = $append;
                    }
                    if ($generator->twoColumnsForm) {
                        $lines[] = \yii\helpers\Html::endTag('div');
                    }
                }
                ?>
<?php
            }
            echo implode("\n", $lines);
            ?>
        <?=$generator->twoColumnsForm ? \yii\helpers\Html::tag('div', null, ['class' => 'clearfix']) : ''?>
    
        <?php echo '<?php $this->endBlock(); ?>'; ?>

        <?php
        $label = substr(strrchr($model::className(), '\\'), 1);
        $items = "[
                    'label' => Yii::t('app', Inflector::camel2words('$label')),
                    'content' => \$this->blocks['main'],
                    'active' => true,
                ],";?>
    <div class="nav-tabs-custom">
        <?=
        "<?= Tabs::widget([
                'encodeLabels' => false,
                'items' => [
                    $items
                ]
            ])
        ?>";
        ?>
    </div>
        <?= '<?php ' ?>echo $form->errorSummary($model); ?>
    <div class="pull-right">
        <?= "<?= " ?>Html::submitButton(
            '<span class="fa fa-check"></span> ' .
            ($model->isNewRecord && !$multiple ?
                <?= $generator->generateString(
                    'Create'
                ) ?> :
                <?= $generator->generateString('Save') ?>),
            [
                'id' => 'save-' . $model->formName(),
                'class' => 'btn',
                'preset'    => Html::PRESET_PRIMARY,
                'name' => 'submit-default'
            ]
        );
        ?>
        <?= "<?php " ?>if (!$is_popup) { ?>
        <?php if ($generator->generateSaveAndNew) { ?>
            <?= "<?= " ?>Html::submitButton(
                '<span class="fa fa-check"></span> ' .
                ($model->isNewRecord && !$multiple ?
                    <?= $generator->generateString(
                        'Create & New'
                    ) ?> :
                    <?= $generator->generateString('Save & New') ?>),
                [
                    'id' => 'save-' . $model->formName(),
                    'class' => 'btn',
                    'preset'    => Html::PRESET_SECONDARY,
                    'name' => 'submit-new'
                ]
            );
            ?>
        <?php } ?>
        <?php if ($generator->generateSaveAndClose) { ?>
            <?= "<?= " ?>Html::submitButton(
                '<span class="fa fa-check"></span> ' .
                ($model->isNewRecord && !$multiple ?
                    <?= $generator->generateString(
                        'Create & Close'
                    ) ?> :
                    <?= $generator->generateString('Save & Close') ?>),
                [
                    'id' => 'save-' . $model->formName(),
                    'class' => 'btn',
                    'preset'    => Html::PRESET_SECONDARY,
                    'name' => 'submit-close'
                ]
            );
            ?>
        <?php } ?>
        <?= '<?php '; ?> } ?>
        </div>
            <?= "<?php " ?>if (!$model->isNewRecord && Yii::$app->getUser()->can(Yii::$app->controller->module->id .
                    '_' . Yii::$app->controller->id . '_delete') && $model->deletable() && !$is_popup) { ?>
                <?= "<?= " ?>Html::a(
                    '<span class="fa fa-trash"></span> ' .
                    <?= $generator->generateString('Delete') ?>,
                    ['delete', <?= $urlParams ?>],
                    [
                        'class' => 'btn btn-danger',
                        'preset'    => Html::PRESET_DANGER,
                        'data-confirm' => '' . <?= $generator->generateString(
                            'Are you sure to delete this item?'
                        ) ?> . '',
                        'data-method' => 'post',
                    ]
                );
                ?>
            <?= "<?php " ?>} ?>
        <?= "<?php " ?>if ($is_popup) { ?>
            <?= "<?= " ?>Html::a(
                '<span class="fa fa-ban"></span> ' .
                <?= $generator->generateString('Close') ?>,
                ['#'],
                [
                    'class' => 'btn btn-danger pull-left',
                    'preset'    => Html::PRESET_DANGER,
                    'data-dismiss' => 'modal',
                    'name' => 'close'
                ]
            );
            ?>
        <?= "<?php " ?>} ?>
        <?= "<?php " ?>ActiveForm::end(); ?>
</div>
<?=$generator->twoColumnsForm ? \yii\helpers\Html::tag('div', null, ['class' => 'clearfix']) : ''?>
<?= "<?php\n" ?>
