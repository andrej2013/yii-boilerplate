<?php

use yii\helpers\Html;
use yii\helpers\Url;
use andrej2013\yiiboilerplate\grid\GridView;
use andrej2013\yiiboilerplate\widget\export\ExportMenu;
use yii\web\View;
use \bookin\aws\checkbox\AwesomeCheckbox;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View                $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\Faq       $searchModel
 */

$this->title = Yii::t('app', 'Frequently Asked Questions Manage');
$this->params['breadcrumbs'][] = $this->title;


/* ------- Multiple-Delete Batch Action ------ */
$inlineScript = 'var gridViewKey = "faq";var gridSelectedRow="' . GridView::TYPE_SUCCESS . '";';
$this->registerJs($inlineScript, View::POS_HEAD, 'my-inline-js');

$gridDropdownItems = ArrayHelper::merge([
    ($searchModel->deletable() && Yii::$app->getUser()
                                           ->can(Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_delete-multiple') ? [
        'url'     => [
            false,
        ],
        'options' => [
            'onclick'  => 'deleteMultiple(this);',
            'data-url' => Url::toRoute('delete-multiple'),
        ],
        'label'   => '<i class="fa fa-trash" aria-hidden="true"></i>&nbsp;' . Yii::t('app', 'Delete'),
    ] : ''),
], Yii::$app->controller->gridDropdownActions);

$gridColumns = [
    /*Generated by andrej2013\yiiboilerplate\templates\crud\providers\InputProvider::columnFormat*/
    [
        'class'               => \kartik\grid\DataColumn::class,
        'attribute'           => 'title',
        'format'              => 'html',
        'filterType'          => GridView::FILTER_TYPEAHEAD,
        'filterWidgetOptions' => [
            'pluginOptions' => [
                'highlight' => true,
            ],
            'options'       => [
                'placeholder'  => Yii::t('app', 'Filter as you type') . ' ...',
                'autocomplete' => 'off',
            ],
            'dataset'       => [
                [
                    'datumTokenizer' => 'Bloodhound.tokenizers.obj.whitespace("value")',
                    'display'        => 'value',
                    'remote'         => [
                        'url'      => Url::to(['default/typehead', 'attribute' => 'title']) . '&q=%QUERY',
                        'wildcard' => '%QUERY',
                    ],
                    'templates'      => [
                        'notFound' => '<div class="text-danger" style="padding:0 8px">' . Yii::t('app', 'No results') . '</div>',
                    ],
                ],
            ],
        ],
    ],
    /*Generated by andrej2013\yiiboilerplate\templates\crud\providers\InputProvider::columnFormat*/
    [
        'class'               => \kartik\grid\DataColumn::class,
        'attribute'           => 'content',
        'format'              => 'html',
    ],
    /*Generated by andrej2013\yiiboilerplate\templates\crud\providers\Select2Provider::columnFormat*/
    [
        'class'               => \kartik\grid\DataColumn::class,
        'attribute'           => 'language_id',
        'format'              => 'html',
        'content'             => function ($model) {
            return $model->language->toString;
        },
        'filterType'          => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'data'          => \yii\helpers\ArrayHelper::map(\andrej2013\yiiboilerplate\models\Language::find()
                                                                                                       ->all(), 'language_id', 'toString'),
            'options'       => [
                'placeholder' => '',
                'multiple'    => true,
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ],
    ],
    [
        'class'               => \kartik\grid\DataColumn::class,
        'attribute'           => 'level',
        'format'              => 'html',
        'content'             => function ($model) {
            return $model->levelName;
        },
    ],
    'order',
    [
        'class'            => \kartik\grid\ActionColumn::class,
        'header'           => Yii::t('app', 'Actions'),
        'contentOptions'   => [
            'nowrap' => 'nowrap',
        ],
        'buttons'          => Yii::$app->controller->gridLinkActions,
        'updateOptions'    => [
            'icon' => '<i class="fa fa-pencil"></i>',
        ],
        'deleteOptions'    => [
            'icon' => '<i class="fa fa-trash"></i>',
        ],
        'viewOptions'      => [
            'icon' => '<i class="fa fa-eye"></i>',
        ],
        'visibleButtons'   => [
            'view'   => function ($model, $key, $index) {
                return $model->readable() && Yii::$app->user->can(Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_view');
            },
            'update' => function ($model, $key, $index) {
                return $model->editable() && Yii::$app->user->can(Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_update');
            },
            'delete' => function ($model, $key, $index) {
                return $model->deletable() && Yii::$app->user->can(Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_delete');
            },
        ],
        'hiddenFromExport' => true,
    ],
    [
        'header'           => AwesomeCheckbox::widget([
            'name'    => 'select-on-check-all',
            'type'    => AwesomeCheckbox::TYPE_CHECKBOX,
            'style'   => AwesomeCheckbox::STYLE_PRIMARY,
            'options' => [
                'class' => 'select-on-check-all',
                'label' => '&nbsp',
            ],
        ]),
        'contentOptions'   => ['class' => 'kv-row-select'],
        'content'          => function ($model, $key) {
            return AwesomeCheckbox::widget([
                'name'    => 'selection[]',
                'type'    => AwesomeCheckbox::TYPE_CHECKBOX,
                'style'   => AwesomeCheckbox::STYLE_PRIMARY,
                'options' => [
                    'value' => $key,
                    'class' => 'kv-row-checkbox',
                    'label' => '&nbsp',
                ],
            ]);
        },
        'hAlign'           => 'center',
        'vAlign'           => 'middle',
        'hiddenFromExport' => true,
        'mergeHeader'      => true,
    ],
];
$exportColumns = $gridColumns;
$gridConfig = Yii::createObject([
    'class'   => \andrej2013\yiiboilerplate\modules\backend\models\GridConfig::class,
    'columns' => $gridColumns,
    'model'   => $searchModel,
    'grid_id' => 'faq-grid',
]);
$slicedGridColumns = $gridConfig->sliceColumns();
foreach ($exportColumns as $column => $value) {
    // Remove checkbox and action columns from Excel export
    if (isset($value['hiddenFromExport']) && $value['hiddenFromExport']) {
        unset($exportColumns[$column]);
    }
}
?>

    <div class="box box-<?php echo \Yii::$app->params['style']['primary_color']; ?>">
        <div class="giiant-crud box-body faq-index">
            <div class="table-responsive">
                <?php echo GridView::widget([
                    'dataProvider'     => $dataProvider,
                    'filterModel'      => $searchModel,
                    'filterSelector'   => 'select[name="pageSize"]',
                    'options'          => [
                        'id' => 'faq-grid',
                    ],
                    'columns'          => $slicedGridColumns,
                    'responsive'       => false,
                    'containerOptions' => [
                        'style' => 'overflow: auto',
                    ], // only set when $responsive = false
                    'headerRowOptions' => [
                        'class' => 'kartik-sheet-style',
                    ],
                    'filterRowOptions' => [
                        'class' => 'kartik-sheet-style',
                    ],
                    'toolbar'          => [
                        [
                            'content' => Html::a('<i class="fa fa-ban"></i>', ['index', 'reset' => 1], [
                                'class' => 'btn',
                                'color' => Html::TYPE_DEFAULT,
                                'title' => Yii::t('app', 'Reset Grid'),
                            ]),
                        ],
                        [
                            'content' => \andrej2013\yiiboilerplate\widget\PageSize::widget([
                                'pjaxContainerId' => 'faq-pjax-container',
                                'model'           => $searchModel,
                            ]),
                        ],
                        [
                            'content' => \yii\bootstrap\ButtonDropdown::widget([
                                'id'          => 'actions',
                                'encodeLabel' => false,
                                'label'       => Yii::t('app', 'Actions'),
                                'dropdown'    => [
                                    'options'      => [
                                        'class' => 'dropdown-menu-right',
                                    ],
                                    'encodeLabels' => false,
                                    'items'        => $gridDropdownItems,
                                ],
                                'options'     => [
                                    'class' => 'btn',
                                    'color' => Html::TYPE_DEFAULT,
                                    'style' => (empty($gridDropdownItems) ? 'display: none' : null),
                                ],
                            ]),
                        ],
                    ],
                    'panel'            => [
                        'heading' => '<h4 class="panel-title"><i class="fa fa-list-o"></i> ' . Yii::t('app', 'Frequently Asked Questions Manage') . '</h4>',
                        'type'    => Yii::$app->params['style']['primary_color'],
                        'before'  => ($searchModel->editable() && Yii::$app->getUser()
                                                                           ->can(Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_create') ? Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'New'), ['create'], [
                            'class'     => 'btn',
                            'preset'    => Html::PRESET_PRIMARY,
                            'data-pjax' => 0,
                        ]) : ''),
                        'after'   => '{pager}',
                        'footer'  => false,
                    ],
                    'striped'          => true,
                    'pjax'             => true,
                    'pjaxSettings'     => [
                        'options'       => [
                            'id' => 'faq-pjax-container',
                        ],
                        'clientOptions' => [
                            'method' => 'POST',
                        ],
                    ],
                    'hover'            => true,
                    'pager'            => [
                        'class'          => yii\widgets\LinkPager::className(),
                        'firstPageLabel' => Yii::t('app', 'First'),
                        'lastPageLabel'  => Yii::t('app', 'Last'),
                    ],
                ])
                ?>
            </div>
        </div>
    </div>
<?php
\andrej2013\yiiboilerplate\widget\Modal::begin([
    'header'       => Html::tag('h4', Yii::t('app', 'Information')),
    'toggleButton' => false,
    'id'           => 'info_modal',
    'footer'       => Html::a('<span class="fa fa-ban"></span>&nbsp;' . Yii::t('app', 'Close'), ['#'], [
        'class'        => 'btn btn-danger pull-left',
        'data-dismiss' => 'modal',
    ]),

]); ?><?= $this->render('@andrej2013-boilerplate/views/_info_modal') ?><?php \andrej2013\yiiboilerplate\widget\Modal::end();
?>