<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
// use yii\grid\GridView;
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\faq\models\FaqSearch $searchModel
 */

$this->title = Yii::t('app', 'Faqs');
$this->params['breadcrumbs'][] = $this->title;


/* ------- Multiple-Delete Batch Action ------ */
$inlineScript = 'var gridViewKey = "faq";';
$this->registerJs($inlineScript, View::POS_HEAD, 'my-inline-js');
andrej2013\yiiboilerplate\assets\TwAsset::register($this);

$gridColumns = [
    'title',
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'language_id',
        'format' => 'html',
        'content' => function ($model) {
            return Html::a($model->language->toString, ['language/view', 'language_id' => $model->language_id]);
        },
        'filter' => \yii\helpers\ArrayHelper::map(\andrej2013\yiiboilerplate\models\Language::find()->andWhere(['status' => 1])->all(), 'language_id', 'toString'),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'options' => [
                'placeholder' => ''
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ]
        ],
    ],
    [
        'attribute' => 'content',
        'format' => 'raw',
    ],
    [
        'attribute' => 'place',
        'content' => function ($model) {
            return \Yii::t('app', $model->place);
        },
        'filter' => [
            'backend' => Yii::t('app', 'Backend'),
            'frontend' => Yii::t('app', 'Frontend'),
        ],
        'filterType' => GridView::FILTER_SELECT2,
        'class' => '\kartik\grid\DataColumn',
        'filterWidgetOptions' => [
            'options' => [
                'placeholder' => ''
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ]
        ],
    ],
    [
        'attribute' => 'level',
        'class' => '\kartik\grid\DataColumn',
        'format' => 'html',
        'content' => function ($model) {
            return $model->levelName;
        },
        'filter' => \yii\helpers\ArrayHelper::map(\andrej2013\yiiboilerplate\modules\faq\models\Faq::find()->andWhere(['level' => \andrej2013\yiiboilerplate\modules\faq\models\Faq::ROOT_LEVEL])->all(), 'id', 'toString'),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'options' => ['placeholder' => ''],
            'pluginOptions' => [
                'allowClear' => true,
            ]
        ],
    ],
    'order',
    [
        'class' => 'yii\grid\ActionColumn',
        'urlCreator' => function ($action, $model, $key, $index) {
            // using the column name as key, not mapping to 'id' like the standard generator
            $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string)$key];
            $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;
            return Url::toRoute($params);
        },
        'contentOptions' => [
            'nowrap' => 'nowrap',
        ],
        'template' => (Yii::$app->getUser()->can(Yii::$app->controller->module->id . '_' . \Yii::$app->controller->id . '_view') || Yii::$app->getUser()->can(Yii::$app->controller->module->id) ? '{view}' : '') . ' ' .
            (Yii::$app->getUser()->can(Yii::$app->controller->module->id . '_' . \Yii::$app->controller->id . '_update') || Yii::$app->getUser()->can(Yii::$app->controller->module->id) ? '{update}' : '') . ' ' .
            (Yii::$app->getUser()->can(Yii::$app->controller->module->id . '_' . \Yii::$app->controller->id . '_delete') || Yii::$app->getUser()->can(Yii::$app->controller->module->id) ? '{delete}' : ''),
    ],
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'headerOptions' => [
            'class' => 'kartik-sheet-style'
        ]
    ],
];
?>
    <div class="box box-default">
        <div class="giiant-crud box-body faq-index">
            <?php
            // echo $this->render('_search', ['model' =>$searchModel]);
            ?>
            <h1>
                <?= Yii::t('app', 'Faqs') ?>
                <small>
                    <?= Yii::t('app', 'List') ?>
                </small>
            </h1>

            <div class="table-responsive" id="faq-pjax-container">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'options' => [
                        'id' => 'faq-grid'
                    ],
                    'columns' => $gridColumns,
                    'containerOptions' => [
                        'style' => 'overflow: auto'
                    ], // only set when $responsive = false
                    'headerRowOptions' => [
                        'class' => 'kartik-sheet-style'
                    ],
                    'filterRowOptions' => [
                        'class' => 'kartik-sheet-style'
                    ],
                    'toolbar' => [
                        [
                            'content' => Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['class' => 'btn btn-default', 'title' => Yii::t('kvgrid', 'Reset Grid')])
                        ],
                        [
                            'content' => ExportMenu::widget([
                                'dataProvider' => $dataProvider,
                                'columns' => $gridColumns,
                                'fontAwesome' => true,
                                'selectedColumns' => [0, 1], // Col seq 0 to 2\n
                                'columnSelector' => [
                                    // 3 => 'Customer', // Rename columns
                                ],
                                'columnSelectorOptions' => [
                                    'label' => \Yii::$app->getUser()->can('Administrator') ? Yii::t('app', 'Cols...') : '',
                                ],
                                'hiddenColumns' => [], // hide columns for example: [1,2]
                                'disabledColumns' => [], // ID & Name
                                'noExportColumns' => [], // mark columns as no-export columns
                                'dropdownOptions' => [
                                    'label' => Yii::t('app', 'Export'),
                                    'class' => 'btn btn-default'
                                ]
                            ])
                        ],
                        '{toggleData}',
                        [
                            'content' => \yii\bootstrap\ButtonDropdown::widget([
                                'id' => 'tw-actions',
                                'encodeLabel' => false,
                                'label' => '<span class="glyphicon glyphicon-flash"></span> ' . Yii::t('app', 'Selected'),
                                'dropdown' => [
                                    'options' => [
                                        'class' => 'dropdown-menu-right'
                                    ],
                                    'encodeLabels' => false,
                                    'items' => [
                                        (Yii::$app->getUser()->can(Yii::$app->controller->module->id . '_' . \Yii::$app->controller->id . '_delete-multiple') || Yii::$app->getUser()->can(Yii::$app->controller->module->id) ?
                                            [
                                                'url' => [
                                                    '#'
                                                ],
                                                'options' => ['onclick' => 'deleteMultiple(this);', 'data-url' => Url::toRoute('delete-multiple')],
                                                'label' => '<i class="fa fa-trash"></i>&nbsp;' . Yii::t('app', 'Remove'),
                                            ] : ''),
                                        (Yii::$app->getUser()->can(Yii::$app->controller->module->id . '_' . \Yii::$app->controller->id . '_update-multiple') || Yii::$app->getUser()->can(Yii::$app->controller->module->id) ?
                                            [
                                                'url' => [
                                                    '#'
                                                ],
                                                'linkOptions' => [
                                                    'data-pjax' => '0',
                                                    'id' => 'update-multiple-ahref',
                                                ],
                                                'options' => [
                                                    'onclick' => 'editMultiple();'
                                                ],
                                                'label' => '<i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;' . Yii::t('app', 'Update'),
                                            ] : ''),
                                    ]
                                ],
                                'options' => [
                                    'class' => 'btn-default'
                                ]
                            ])
                        ],
                    ],
                    'panel' => [
                        'heading' => "<h3 class=\"panel-title\"><i class=\"glyphicon glyphicon-list\"></i>  " . Yii::t('app', 'Faqs') . "</h3>",
                        'type' => 'default',
                        'before' => (\Yii::$app->getUser()->can(Yii::$app->controller->module->id . '_' . \Yii::$app->controller->id . '_create') || Yii::$app->getUser()->can(Yii::$app->controller->module->id) ? Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-success', 'data-pjax' => '0']) : '') . ' ' . (\Yii::$app->getUser()->can('Administrator') ?
                                \yii\bootstrap\ButtonDropdown::widget([
                                    'id' => 'giiant-relations',
                                    'encodeLabel' => false,
                                    'label' => '<span class="glyphicon glyphicon-paperclip"></span> ' . Yii::t('app', 'Relations'),
                                    'dropdown' => [
                                        'options' => [
                                            'class' => 'dropdown-menu-right'
                                        ],
                                        'encodeLabels' => false,
                                        'items' => [
                                        ]
                                    ],
                                    'options' => [
                                        'class' => 'btn-default'
                                    ]
                                ])
                                : '')
                        ,
                        'after' => '{pager}',
                        'footer' => false
                    ],
                    // set export properties
                    'export' => [
                        'fontAwesome' => true
                    ],
                    'striped' => true,
                    'pjax' => true,
                    'hover' => true,
                    'pager' => [
                        'class' => yii\widgets\LinkPager::className(),
                        'firstPageLabel' => Yii::t('app', 'First'),
                        'lastPageLabel' => Yii::t('app', 'Last')
                    ],
                ])
                ?>
            </div>
        </div>
    </div>
<?php \yii\bootstrap\Modal::begin([
    'size' => \yii\bootstrap\Modal::SIZE_DEFAULT,
    'header' => '<h4>' . Yii::t('app', 'Choose fields to edit') . ':</h4>',
    'id' => 'edit-multiple',
    'clientOptions' => [
        'backdrop' => 'static',
    ],
]);
?>
<?= Html::beginForm(['update-multiple'], 'POST'); ?>
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-1">
            <div class="checkbox">
                <label for="title">
                    <input type="checkbox" id="title" name="title" value="1">Titel
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-1">
            <div class="checkbox">
                <label for="content">
                    <input type="checkbox" id="content" name="content" value="1">Inhalt
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-1">
            <div class="checkbox">
                <label for="language_id">
                    <input type="checkbox" id="language_id" name="language_id" value="1">Sprache
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-1">
            <div class="checkbox">
                <label for="place">
                    <input type="checkbox" id="place" name="place" value="1">Ort
                </label>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <button type="submit" class="btn btn-success" id="submit-multiple">Update</button>
<?= Html::endForm(); ?>

<?php \yii\bootstrap\Modal::end();
?>

<?php
$js = <<<JS
jQuery.fn.addHidden = function (name, value) {
    return this.each(function () {
        var input = $("<input>").attr("type", "hidden").attr("name", name).val(value);
        $(this).append($(input));
    });
};
$(document).ready(function () {
    $('#update-multiple-ahref').click(function(event) {
        event.preventDefault();
    });
    $('#submit-multiple').on('click', function(e){
        e.preventDefault();
        var keys=$("#" + gridViewKey + "-grid").yiiGridView('getSelectedRows');
        var form = $(this).closest('form')
        form.addHidden('no-post', true);
        $.each(keys , function (key, value) {
            form.addHidden('id[]', value);
        });
        form.submit();
    });
});
JS;
$this->registerJs($js);
