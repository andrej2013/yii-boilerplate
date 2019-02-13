<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var andrej2013\yiiboilerplate\templates\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass();
$model->setScenario('crud');
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    /** @var \yii\db\ActiveRecord $model */
    $model = new $generator->modelClass();
    $safeAttributes = $model->safeAttributes();
    if (empty($safeAttributes)) {
        $safeAttributes = $model->getTableSchema()->columnNames;
    }
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\helpers\Url;
use andrej2013\yiiboilerplate\grid\GridView;
use andrej2013\yiiboilerplate\widget\export\ExportMenu;
use yii\web\View;
use \bookin\aws\checkbox\AwesomeCheckbox;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var boolean $useModal
 * @var boolean $importer
<?php if ($generator->searchModelClass !== '') : ?>
 * @var <?= ltrim($generator->searchModelClass, '\\') ?> $searchModel
<?php endif; ?>
 */

$this->title = <?= $generator->generateString(
Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))
) ?>;
$this->params['breadcrumbs'][] = $this->title;


/* ------- Multiple-Delete Batch Action ------ */
$inlineScript = 'var gridViewKey = "<?= Inflector::camel2id(
    StringHelper::basename($generator->modelClass),
    '-',
    true
) ?>";var gridSelectedRow="'.GridView::TYPE_SUCCESS.'";';
$this->registerJs($inlineScript, View::POS_HEAD, 'my-inline-js');

$gridDropdownItems = ArrayHelper::merge([
    (Yii::$app->getUser()->can(Yii::$app->controller->module->id .'_' .Yii::$app->controller->id .'_delete-multiple') ?
    [
        'url' => [
            false
        ],
        'options' => [
            'onclick' => 'deleteMultiple(this);',
            'data-url' => Url::toRoute('delete-multiple')
        ],
        'label' => '<i class="fa fa-trash" aria-hidden="true"></i>&nbsp;' . Yii::t('app', 'Delete'),
    ] : ''),
], Yii::$app->controller->gridDropdownActions);

$gridColumns = [
<?php
$actionButtonColumn = <<<PHP
    [
        'class'             => {$generator->actionButtonClass},
        'header'            => Yii::t('app', 'Actions'),
        'contentOptions'    => [
            'nowrap'        => 'nowrap',
        ],
        'buttons'           => Yii::\$app->controller->gridLinkActions,
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
            'view'   => function (\$model, \$key, \$index) {
                return \$model->readable() && Yii::\$app->user->can(Yii::\$app->controller->module->id . '_' . Yii::\$app->controller->id . '_view');
            },
            'update' => function (\$model, \$key, \$index) {
                return \$model->editable() && Yii::\$app->user->can(Yii::\$app->controller->module->id . '_' . Yii::\$app->controller->id . '_update');
            },
            'delete' => function (\$model, \$key, \$index) {
                return \$model->deletable() && Yii::\$app->user->can(Yii::\$app->controller->module->id . '_' . Yii::\$app->controller->id . '_delete');
            },
        ],
        'hiddenFromExport' => true,
    ],\n
PHP;

$count = 0;
$selectedColumn = "";
$columns = [];
foreach ($safeAttributes as $attribute) {
    $format = trim($generator->columnFormat($attribute, $model, 'index'));
    if ($format == false || in_array(strtolower($attribute), $generator->hidden_attributes)
    ) {
        continue;
    }

    $foreignModel = null;


    $fkFormat = null;
    foreach ($generator->getModelRelations($generator->modelClass) as $modelRelation) {
        foreach ($modelRelation->link as $linkKey => $link) {
            if ($attribute == $link) {
                //die("<pre>" . print_r($modelRelation->,true) . "</pre>");
                $foreignModelClass = $modelRelation->modelClass;
                $foreignControllerClass = str_replace("models", "controllers", $foreignModelClass);
                $foreignControllerClass .= "Controller";
                $foreignAttribute = $generator->fetchForeignAttribute($attribute);
                $gen = new \andrej2013\yiiboilerplate\templates\crud\Generator();
                $gen->controllerClass = $foreignControllerClass;

                // is a relation-column
                $foreignModel = null;
                $fkFormat = str_replace("ID", "", $attribute) . ".toString";
                $reflection = new ReflectionClass($foreignModelClass);
                $shortForeignModel = $reflection->getShortName();
                $foreignObjectName = \yii\helpers\BaseInflector::variablize(str_replace("_id", "", $attribute));
                $columns[] = "[
    'class' => '\\kartik\\grid\\DataColumn',
    'attribute' => '$attribute',
    'format' => 'html',
    'content' => function (\$model) {
        if (\$model->" . $foreignObjectName . ") {
            if (Yii::\$app->getUser()->can('app_" . $gen->getControllerID() . "_view') && \$model->" . $foreignObjectName . "->readable()) {
                return Html::a(\$model->" . $foreignObjectName ."->toString, ['" . $gen->getControllerID() . "/view', '$foreignAttribute' => \$model->$attribute]);
            } else {
                return \$model->" . $foreignObjectName ."->toString;
            }
        }
    },
    'filterType' => GridView::FILTER_SELECT2,
    'filterWidgetOptions' => [
        'data' => \\yii\\helpers\\ArrayHelper::map($foreignModelClass::find()->all(), '$foreignAttribute', 'toString'),
        'options' => [
            'placeholder' => '',
            'multiple' => true,
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ]
    ],
]";
            }
        }
    }
    if ($fkFormat != null || $format == false) {
        continue;
    }
    $columns[] = $format;
}
$columns[] = <<<PHP
[
    'class'             => {$generator->actionButtonClass},
    'header'            => Yii::t('app', 'Actions'),
    'contentOptions'    => [
        'nowrap'        => 'nowrap',
    ],
    'buttons'           => Yii::\$app->controller->gridLinkActions,
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
        'view'   => function (\$model, \$key, \$index) {
            return \$model->readable() && Yii::\$app->user->can(Yii::\$app->controller->module->id . '_' . Yii::\$app->controller->id . '_view');
        },
        'update' => function (\$model, \$key, \$index) {
            return \$model->editable() && Yii::\$app->user->can(Yii::\$app->controller->module->id . '_' . Yii::\$app->controller->id . '_update');
        },
        'delete' => function (\$model, \$key, \$index) {
            return \$model->deletable() && Yii::\$app->user->can(Yii::\$app->controller->module->id . '_' . Yii::\$app->controller->id . '_delete');
        },
    ],
    'hiddenFromExport' => true,
]
PHP;
$columns[] = <<<PHP
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
    'content'          => function (\$model, \$key) {
        return AwesomeCheckbox::widget([
            'name'    => 'selection[]',
            'type'    => AwesomeCheckbox::TYPE_CHECKBOX,
            'style'   => AwesomeCheckbox::STYLE_PRIMARY,
            'options' => [
                'value' => \$key,
                'class' => 'kv-row-checkbox',
                'label' => '&nbsp',
            ],
        ]);
    },
    'hAlign'           => 'center',
    'vAlign'           => 'middle',
    'hiddenFromExport' => true,
    'mergeHeader'      => true,
]
PHP;
echo implode(",\n", $columns);
?>
];
$exportColumns = $gridColumns;
$gridConfig = Yii::createObject([
    'class' => \andrej2013\yiiboilerplate\modules\backend\models\GridConfig::class,
    'columns' => $gridColumns,
    'model'   => $searchModel,
    'grid_id' => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-grid',
]);
$slicedGridColumns = $gridConfig->sliceColumns();
foreach ($exportColumns as $column => $value) {
    // Remove checkbox and action columns from Excel export
    if (isset($value['hiddenFromExport']) && $value['hiddenFromExport']) {
        unset($exportColumns[$column]);
    }
}
?>

<div class="box box-<?='<?php '; ?>echo \Yii::$app->params['style']['primary_color']; ?>">
    <div class="giiant-crud box-body <?= Inflector::camel2id(
        StringHelper::basename($generator->modelClass),
        '-',
        true
    )
    ?>-index">
        <div class="table-responsive">
            <?= '<?php echo ' ?>GridView::widget([
                'dataProvider'      => $dataProvider,
                'filterModel'       => $searchModel,
                'filterSelector'    => 'select[name="pageSize"]',
                'options'           => [
                    'id'            => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-grid'
                ],
                'columns'           => $slicedGridColumns,
                'responsive'        => false,
                'containerOptions'  => [
                    'style'         => 'overflow: auto'
                ], // only set when $responsive = false
                'headerRowOptions'  => [
                    'class'         => 'kartik-sheet-style'
                ],
                'filterRowOptions'  => [
                    'class'         => 'kartik-sheet-style'
                ],
                'toolbar'           => [
                    [
                        'content'   => Html::button('<i class="fa fa-info"></i> ', [
                            'class'       => 'btn',
                            'color'       => Html::TYPE_DEFAULT,
                            'data-toggle' => 'modal',
                            'data-target' => '#info_modal',
                        ]),
                    ],
                    <?php if ($generator->generateExtendedSearch) { ?>[
                        'content'   => \andrej2013\yiiboilerplate\widget\FilterWidget::widget([
                            'model'         => $searchModel,
                            'pjaxContainer' => 'country-pjax-container',
                        ]),
                    ],<?php } ?>
                    [
                        'content'   => Html::a('<i class="fa fa-ban"></i>', ['index', 'reset' => 1], [
                            'class' => 'btn',
                            'color' => Html::TYPE_DEFAULT,
                            'title' => Yii::t('app', 'Reset Grid'),
                        ]),
                    ],
                    <?php if ($generator->generateExportButton) { ?>'{export}',<?php } ?>
                    [
                        'content'   => \andrej2013\yiiboilerplate\widget\PageSize::widget([
                            'pjaxContainerId'   => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-pjax-container',
                            'model'             => $searchModel,
                        ])
                    ],
                    <?php if ($generator->generateGridConfig) { ?>[
                        'content' => \andrej2013\yiiboilerplate\modules\backend\widgets\GridConfig::widget([
                            'model'           => $searchModel,
                            'columns'         => $gridColumns,
                            'grid'            => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-grid',
                            'pjaxContainerId' => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-pjax-container',
                        ]),
                    ],<?php } ?>
                    [
                        'content'           => \yii\bootstrap\ButtonDropdown::widget([
                            'id'            => 'actions',
                            'encodeLabel'   => false,
                            'label'         => Yii::t('app', 'Actions'),
                            'dropdown'      => [
                                'options'   => [
                                    'class' => 'dropdown-menu-right',
                                ],
                                'encodeLabels' => false,
                                'items'        => $gridDropdownItems,
                            ],
                            'options'       => [
                                'class'     => 'btn',
                                'color'     => Html::TYPE_DEFAULT,
                                'style'     => (empty($gridDropdownItems) ? 'display: none' : null),
                            ],
                        ]),
                    ],
                ],
                'panel'             => [
                    'heading'       => '<h4 class="panel-title"><i class="fa fa-list-o"></i>  ' .<?= $generator->generateString(
                            Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))
                        )
                        ?> . '</h4>',
                    'type'          => Yii::$app->params['style']['primary_color'],
                    'before'        => (Yii::$app->getUser()
                                           ->can(Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_create') ? Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'New'), ['create'], [
                        'class' => 'btn',
                        'preset'    => Html::PRESET_PRIMARY,
                        'data-pjax' => 0,
                    ]) : ''),
                    'after'         => '{pager}',
                    'footer'        => false
                ],
                // set export properties
                'export'            => [
                    'fontAwesome'   => true,
                    'label'         => Yii::t('app', 'Export'),
                    'options'       => [
                        'class'     => 'btn',
                        'color'     => Html::TYPE_DEFAULT,
                    ],
                    'showConfirmAlert' => false,
                ],
                'exportConfig'      => [
                    GridView::PDF   => [],
                    GridView::HTML  => [],
                    GridView::CSV   => [],
                    GridView::TEXT  => [],
                    GridView::JSON  => [],
                    GridView::EXCEL => [
                        //Override default export option with ExportMenu Widget
                        'external'  => true,
                        'label'     => ExportMenu::widget([
                            'asDropdown'            => false,
                            'dataProvider'          => $dataProvider,
                            'showColumnSelector'    => false,
                            'columns'               => $exportColumns,
                            'fontAwesome'           => true,
                            'exportConfig'          => [
                                ExportMenu::FORMAT_HTML     => false,
                                ExportMenu::FORMAT_PDF      => false,
                                ExportMenu::FORMAT_CSV      => false,
                                ExportMenu::FORMAT_EXCEL    => false,
                                ExportMenu::FORMAT_TEXT     => false,
                            ],
                        ]),
                    ],
                ],
                'striped'       => true,
                'pjax'          => true,
                'pjaxSettings'  => [
                    'options'   => [
                        'id'    => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-pjax-container',
                    ],
                    'clientOptions' => [
                        'method'    => 'POST'
                    ]
                ],
                'hover'         => true,
                'pager'         => [
                    'class'     => yii\widgets\LinkPager::className(),
                    'firstPageLabel'    => Yii::t('app', 'First'),
                    'lastPageLabel'     => Yii::t('app', 'Last')
                ],
            ])
            ?>
        </div>
    </div>
</div>
<?php echo "<?php
\andrej2013\yiiboilerplate\widget\Modal::begin([
    'header'        => Html::tag('h4', Yii::t('app', 'Information')),
    'toggleButton'  => false,
    'id'            => 'info_modal',
    'footer'       => Html::a('<span class=\"fa fa-ban\"></span>&nbsp;' . Yii::t('app', 'Close'), ['#'], [
            'class'        => 'btn btn-danger pull-left',
            'data-dismiss' => 'modal',
        ]),
    
]);?>"?>
<?php echo "<?= \$this->render('@andrej2013-boilerplate/views/_info_modal') ?>" ?>
<?php echo "<?php \\andrej2013\\yiiboilerplate\\widget\\Modal::end();
?>" ?>
