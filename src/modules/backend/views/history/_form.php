<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * /srv/www/nassi-v2/src/../runtime/giiant/04f0b2ff7bd97130b071fc9ab4e68ec0
 *
 * @package default
 */


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
 *
 * @var yii\web\View                              $this
 * @var \andrej2013\yiiboilerplate\models\ArHistory $model
 * @var yii\widgets\ActiveForm                    $form
 * @var boolean                                   $useModal
 * @var boolean                                   $multiple
 * @var array                                     $pk
 * @var array                                     $show
 * @var string                                    $action
 * @var string                                    $owner
 * @var array                                     $languageCodes
 * @var boolean                                   $relatedForm to know if this view is called from ajax related-form
 *      action. Since this is passing from select2 (owner) it is always inherit from main form
 * @var string                                    $relatedType type of related form, like above always passing from
 *      main form
 * @var string                                    $owner       string that representing id of select2 from which
 *      related-form action been called. Since we in each new form appending "_related" for next opened form, it is
 *      always unique. In main form it is always ID without anything appended
 */
$owner = $relatedForm ? '_' . $owner . '_related' : '';
$relatedTypeForm = Yii::$app->request->get('relatedType') ?: $relatedTypeForm;
?>
<div class="address-form">
    <?php $form = ActiveForm::begin([
        'fieldClass' => '\andrej2013\yiiboilerplate\widget\ActiveField',
        'id' => 'Address' . ($ajax || $useModal ? '_ajax_' . $owner : ''),
        'layout' => 'default',
        'enableClientValidation' => true,
        'errorSummaryCssClass' => 'error-summary alert alert-error',
        'action' => $useModal ? $action : '',
        'options' => [
            'name' => 'Address',
        ],
    ]);
    ?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>
            <?php if ($multiple) : ?>
                <?php echo Html::hiddenInput('update-multiple', true) ?>
                <?php foreach ($pk as $id) : ?>
                    <?php echo Html::hiddenInput('pk[]', $id) ?>
                <?php endforeach; ?>
            <?php endif; ?>

        <div class="col-md-6">
            <?php if (!$multiple || ($multiple && isset($show['table_name']))) : ?>
                <?php echo $form->field(
                    $model,
                    'table_name',
                    [
                        'selectors' => [
                            'input' => '#' .
                                Html::getInputId($model, 'table_name') . $owner
                        ]
                    ]
                )
                    ->textInput(
                        [
                            'maxlength' => true,
                            'id' => Html::getInputId($model, 'table_name') . $owner
                        ]
                    )
                ?>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?php if (!$multiple || ($multiple && isset($show['field_name']))) : ?>
                <?php echo $form->field(
                    $model,
                    'field_name',
                    [
                        'selectors' => [
                            'input' => '#' .
                                Html::getInputId($model, 'field_name') . $owner
                        ]
                    ]
                )
                    ->textInput(
                        [
                            'maxlength' => true,
                            'id' => Html::getInputId($model, 'field_name') . $owner
                        ]
                    )
                ?>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?php if (!$multiple || ($multiple && isset($show['row_id']))) : ?>
                <?php echo $form->field(
                    $model,
                    'row_id',
                    [
                        'selectors' => [
                            'input' => '#' .
                                Html::getInputId($model, 'row_id') . $owner
                        ]
                    ]
                )
                    ->textInput(
                        [
                            'id' => Html::getInputId($model, 'row_id') . $owner,
                            'type' => 'number',
                            'step' => '1',
                        ]
                    )
                ?>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?php if (!$multiple || ($multiple && isset($show['event']))) : ?>
                <?php echo $form->field(
                    $model,
                    'event',
                    [
                        'selectors' => [
                            'input' => '#' .
                                Html::getInputId($model, 'event') . $owner
                        ]
                    ]
                )
                    ->textInput(
                        [
                            'type' => 'number',
                            'step' => '1',
                            'id' => Html::getInputId($model, 'event') . $owner
                        ]
                    )
                ?>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?php if (!$multiple || ($multiple && isset($show['old_value']))) : ?>
                <?php echo $form->field(
                    $model,
                    'old_value',
                    [
                        'selectors' => [
                            'input' => '#' .
                                Html::getInputId($model, 'old_value') . $owner
                        ]
                    ]
                )
                    ->textInput(
                        [
                            'maxlength' => true,
                            'id' => Html::getInputId($model, 'old_value') . $owner
                        ]
                    )
                ?>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?php if (!$multiple || ($multiple && isset($show['new_value']))) : ?>
                <?php echo $form->field(
                    $model,
                    'new_value',
                    [
                        'selectors' => [
                            'input' => '#' .
                                Html::getInputId($model, 'new_value') . $owner
                        ]
                    ]
                )
                    ->textInput(
                        [
                            'maxlength' => true,
                            'id' => Html::getInputId($model, 'new_value') . $owner
                        ]
                    )
                ?>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?php if (!$multiple || ($multiple && isset($show['created_by']))) : ?>
                <?php echo $form->field(
                    $model,
                    'created_by'
                )
                    ->widget(
                        Select2::classname(),
                        [
                            'data' => ArrayHelper::map(app\models\User::find()->all(), 'id', 'toString'),
                            'options' => [
                                'placeholder' => Yii::t('app', 'Select a value...'),
                                'id' => 'created_by' . ($ajax || $useModal ? '_ajax_' . $owner : ''),
                            ],
                            'pluginOptions' => [
                                'allowClear' => false,
                                (count(app\models\User::find()->all()) > 50 ? 'minimumInputLength' : '') => 3,
                                (count(app\models\User::find()->all()) > 50 ? 'ajax' : '') => [
                                    'url' => \yii\helpers\Url::to(['list']),
                                    'dataType' => 'json',
                                    'data' => new \yii\web\JsExpression('function(params) {
                                        return {
                                            q:params.term, m: \'User\'
                                        };
                                    }')
                                ],
                            ],
                        ]
                    )
                ?>
                <?php
                ?>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?php if (!$multiple || ($multiple && isset($show['created_at']))) : ?>
                <?php echo $form->field($model, 'created_at')->widget(\kartik\datecontrol\DateControl::classname(), [
                    'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                    'ajaxConversion' => true,
                    'displayFormat' => Yii::$app->formatter->datetimeFormat,
                    'saveFormat' => 'php:U',
                    'options' => [
                        'type' => \kartik\datetime\DateTimePicker::TYPE_COMPONENT_APPEND,
                        'pickerButton' => ['icon' => 'time'],
                        'pluginOptions' => [
                            'todayHighlight' => true,
                            'autoclose' => true,
                            'class' => 'form-control'
                        ]
                    ],
                ])
                ?>
            <?php endif; ?>
        </div>
        </p>
        <?php $this->endBlock(); ?>

        <?php echo ($relatedType != RelatedForms::TYPE_TAB) ?
            Tabs::widget([
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Yii::t('app', Inflector::camel2words('Address')),
                        'content' => $this->blocks['main'],
                        'active' => true,
                    ],
                ]
            ])
            : $this->blocks['main']
        ?>
        <div class="col-md-12">
            <hr/>

        </div>

        <div class="clearfix"></div>
        <?php echo $form->errorSummary($model); ?>
        <div class="col-md-6"<?php echo !$relatedForm ? ' id="main-submit-buttons"' : '' ?>>
            <?php echo Html::submitButton(
                '<span class="glyphicon glyphicon-check"></span> ' .
                ($model->isNewRecord && !$multiple ?
                    Yii::t('app', 'Create') :
                    Yii::t('app', 'Save')),
                [
                    'id' => 'save-' . $model->formName(),
                    'class' => 'btn btn-success',
                    'name' => 'submit-default'
                ]
            );
            ?>

            <?php if ((!$relatedForm && !$useModal) && !$multiple) { ?>
                <?php echo Html::submitButton(
                    '<span class="glyphicon glyphicon-check"></span> ' .
                    ($model->isNewRecord && !$multiple ?
                        Yii::t('app', 'Create & New') :
                        Yii::t('app', 'Save & New')),
                    [
                        'id' => 'save-' . $model->formName(),
                        'class' => 'btn btn-default',
                        'name' => 'submit-new'
                    ]
                );
                ?>
                <?php echo Html::submitButton(
                    '<span class="glyphicon glyphicon-check"></span> ' .
                    ($model->isNewRecord && !$multiple ?
                        Yii::t('app', 'Create & Close') :
                        Yii::t('app', 'Save & Close')),
                    [
                        'id' => 'save-' . $model->formName(),
                        'class' => 'btn btn-default',
                        'name' => 'submit-close'
                    ]
                );
                ?>

                <?php if (!$model->isNewRecord) { ?>
                    <?php echo Html::a(
                        '<span class="glyphicon glyphicon-trash"></span> ' .
                        Yii::t('app', 'Delete'),
                        ['delete', 'id' => $model->id],
                        [
                            'class' => 'btn btn-danger',
                            'data-confirm' => '' . Yii::t('app', 'Are you sure to delete this item?') . '',
                            'data-method' => 'post',
                        ]
                    );
                    ?>
                <?php } ?>
            <?php } elseif ($multiple) { ?>
                <?php echo Html::a(
                    '<span class="glyphicon glyphicon-exit"></span> ' .
                    Yii::t('app', 'Close'),
                    $useModal ? false : [],
                    [
                        'id' => 'save-' . $model->formName(),
                        'class' => 'btn btn-danger' . ($useModal ? ' closeMultiple' : ''),
                        'name' => 'close'
                    ]
                );
                ?>
            <?php } else { ?>
                <?php echo Html::a(
                    '<span class="glyphicon glyphicon-exit"></span> ' .
                    Yii::t('app', 'Close'),
                    ['#'],
                    [
                        'class' => 'btn btn-danger',
                        'data-dismiss' => 'modal',
                        'name' => 'close'
                    ]
                );
                ?>
            <?php } ?>
        </div>
        <?php ActiveForm::end(); ?>
        <?php echo ($relatedForm && $relatedType == \andrej2013\yiiboilerplate\modules\backend\widgets\RelatedForms::TYPE_MODAL) ?
            '<div class="clearfix"></div>' :
            ''
        ?>
        <div class="clearfix"></div>
    </div>
</div>
<?php
if ($useModal) {
    \andrej2013\yiiboilerplate\modules\backend\assets\ModalFormAsset::register($this);
}
?>
