<?php

use yii\helpers\ArrayHelper;
use andrej2013\yiiboilerplate\widget\Select2;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\Inflector;
use yii\helpers\Url;
use kartik\helpers\Html;
use kartik\depdrop\DepDrop;

/**
 * @var yii\web\View $this
 * @var andrej2013\yiiboilerplate\modules\faq\models\Faq $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="faq-form">
    <?php $form = ActiveForm::begin([
        'fieldClass' => '\andrej2013\yiiboilerplate\widget\ActiveField',
        'id' => 'Faq',
        'layout' => 'horizontal',
        'enableClientValidation' => true,
        'errorSummaryCssClass' => 'error-summary alert alert-error',
    ]);
    ?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>
            <?php if ($multiple): ?>
                <?= Html::hiddenInput('update-multiple', true) ?>
                <?php foreach ($pk as $id): ?>
                    <?= Html::hiddenInput('pk[]', $id) ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!$multiple || ($multiple && isset($show['title']))): ?>
                <?= $form->field($model, 'title')->textInput(['maxlength' => true,])
                ?>
            <?php endif; ?>
            <?php if (!$multiple || ($multiple && isset($show['content']))): ?>
                <?php echo $form->field($model, 'content')->widget(\andrej2013\yiiboilerplate\widget\CKEditor::classname(), [
                    'editorOptions' => \mihaildev\elfinder\ElFinder::ckeditorOptions('elfinder-backend', [
                        'height' => 300,
                        'preset' => 'standard',
                    ]),
                ])
                ?>
            <?php endif; ?>
            <?php if (!$multiple || ($multiple && isset($show['language_id']))): ?>
                <?= $form->field($model, 'language_id')->widget(Select2::classname(),
                    [
                        'data' => ArrayHelper::map(\andrej2013\yiiboilerplate\models\Language::find()->andWhere(['status' => 1])->all(), 'language_id', 'toString'),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Select a value...'),
                            'id' => 'language_id',
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            (count(\andrej2013\yiiboilerplate\models\Language::find()->andWhere(['status' => 1])->all()) > 50 ? 'minimumInputLength' : '') => 3,
                            (count(\andrej2013\yiiboilerplate\models\Language::find()->andWhere(['status' => 1])->all()) > 50 ? 'ajax' : '') => [
                                'url' => \yii\helpers\Url::to(['list']),
                                'dataType' => 'json',
                                'data' => new \yii\web\JsExpression('function(params) {
                                return {
                                    q:params.term, m: \'Language\'
                                };
                            }')
                            ],
                        ],
                    ])
                ?>
                <?php
                ?>
            <?php endif; ?>
            <?php if (!$multiple || ($multiple && isset($show['level']))): ?>
                <?= $form->field($model, 'level')->widget(DepDrop::classname(),
                    [
                        'type' => DepDrop::TYPE_SELECT2,
                        'data' => $model->language_id ? ArrayHelper::merge([\andrej2013\yiiboilerplate\modules\faq\models\Faq::ROOT_LEVEL => Yii::t('app', 'Root')], ArrayHelper::map(\andrej2013\yiiboilerplate\modules\faq\models\Faq::find()->andWhere(['language_id' => $model->language_id])->all(), 'id', 'toString')) : [],
                        'options' => [
                            'placeholder' => Yii::t('app', 'Select a value...'),
                            'id' => 'level',
                        ],
                        'select2Options' => [
                            'pluginOptions' => [
                                'allowClear' => false,
                            ]
                        ],
                        'pluginOptions' => [
                            'depends' => ['language_id'],
                            'url' => Url::to(['depend', 'on' => 'Language', 'onRelation' => 'language_id']),
                            'allowClear' => false,
                        ],
                    ])
                ?>
                <?php
                ?>
            <?php endif; ?>
            <?php if (!$multiple || ($multiple && isset($show['place']))): ?>
                <?= $form->field($model, 'place')->widget(Select2::classname(),
                    [
                        'data' => [
                            'backend' => Yii::t('app', 'Backend'),
                            'frontend' => Yii::t('app', 'Frontend'),
                        ],
                        'hideSearch' => 'true',
                        'pluginOptions' => [
                            'allowClear' => false
                        ],
                    ]
                )
                ?>
            <?php endif; ?>
            <?php if (!$multiple || ($multiple && isset($show['order']))): ?>
                <?= $form->field($model, 'order')->textInput(['maxlength' => true,])
                ?>
            <?php endif; ?>
        </p>
        <?php $this->endBlock(); ?>

        <?= Tabs::widget([
            'encodeLabels' => false,
            'items' => [
                [
                    'label' => Yii::t("app", Inflector::camel2words(Faq)),
                    'content' => $this->blocks['main'],
                    'active' => true,
                ],
            ]
        ]);
        ?>
        <hr/>

        <?php echo $form->errorSummary($model); ?>

        <?= Html::submitButton(
            '<span class="glyphicon glyphicon-check"></span> ' .
            ($model->isNewRecord && !$multiple ? Yii::t('app', 'Create') : Yii::t('app', 'Save')),
            [
                'id' => 'save-' . $model->formName(),
                'class' => 'btn btn-success',
                'name' => 'submit-default'
            ]
        );
        ?>

        <?php if (!$ajax && !$multiple) { ?>
            <?= Html::submitButton(
                '<span class="glyphicon glyphicon-check"></span> ' . ($model->isNewRecord && !$multiple ? Yii::t('app', 'Create & New') : Yii::t('app', 'Save & New')),
                [
                    'id' => 'save-' . $model->formName(),
                    'class' => 'btn btn-default',
                    'name' => 'submit-new'
                ]
            );
            ?>
            <?= Html::submitButton('<span class="glyphicon glyphicon-check"></span> ' . ($model->isNewRecord && !$multiple ? Yii::t('app', 'Create & Close') : Yii::t('app', 'Save & Close')),
                [
                    'id' => 'save-' . $model->formName(),
                    'class' => 'btn btn-default',
                    'name' => 'submit-close'
                ]
            );
            ?>

            <?php if (!$model->isNewRecord) { ?>
                <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id],
                    [
                        'class' => 'btn btn-danger',
                        'data-confirm' => '' . Yii::t('app', 'Are you sure to delete this item?') . '',
                        'data-method' => 'post',
                    ]
                );
                ?>
            <?php } ?>
        <?php } elseif ($multiple) { ?>
            <?= Html::a('<span class="glyphicon glyphicon-exit"></span> ' . Yii::t('app', 'Close'), [],
                [
                    'id' => 'save-' . $model->formName(),
                    'class' => 'btn btn-danger',
                    'name' => 'close'
                ]
            );
            ?>
        <?php } else { ?>
            <?= Html::a('<span class="glyphicon glyphicon-exit"></span> ' . Yii::t('app', 'Close'), [],
                [
                    'class' => 'btn btn-danger',
                    'data-dismiss' => 'modal'
                ]
            );
            ?>
        <?php } ?>
        <?php ActiveForm::end(); ?>

    </div>

</div>

