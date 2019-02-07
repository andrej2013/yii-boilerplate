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
// use <?= $generator->indexWidgetType === 'grid' ? 'yii\\grid\\GridView' : 'yii\\widgets\\ListView' ?>;
use yii\web\View;

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
) ?>", useModal = ' . ($useModal ? 'true' : 'false') . ';';
$this->registerJs($inlineScript, View::POS_HEAD, 'my-inline-js');

$gridDropdownItems = \yii\helpers\ArrayHelper::merge([
    (Yii::$app->getUser()->can(
    Yii::$app->controller->module->id .
    '_' .
    Yii::$app->controller->id .
    '_delete-multiple'
    ) ?
    [
    'url' => [
    false
    ],
    'options' => [
    'onclick' => 'deleteMultiple(this);',
    'data-url' => Url::toRoute('delete-multiple')
    ],
    'label' => '<i class="fa fa-trash" aria-hidden="true"></i>&nbsp;' .
    Yii::t('app', 'Remove'),
    ] : ''),
    (Yii::$app->getUser()->can(
    Yii::$app->controller->module->id .
    '_' .
    Yii::$app->controller->id .
    '_update-multiple'
    ) ?
    [
    'url' => '#edit-multiple',
    'linkOptions' => [
    'data-toggle' => 'modal',
    ],
    'label' => '<i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;' .
    Yii::t('app', 'Update'),
    ] : ''),
], Yii::$app->controller->gridDropdownActions);

$gridColumns = [
<?php
$actionButtonColumn = <<<PHP
    [
        'class' => '{$generator->actionButtonClass}',
        'urlCreator' => function (\$action, \$model, \$key, \$index) {
            /**
             * @var \yii\db\ActiveRecord \$model
             */
            // using the column name as key, not mapping to 'id' like the standard generator
            \$params = is_array(\$key) ? \$key : [\$model->primaryKey()[0] => (string)\$key];
            \$params[0] = Yii::\$app->controller->id ? Yii::\$app->controller->id . '/' . \$action : \$action;
            return Url::toRoute(\$params);
        },
        'contentOptions' => [
            'nowrap' => 'nowrap',
        ],
        'template' => (Yii::\$app->getUser()->can(Yii::\$app->controller->module->id .
                '_' .
                Yii::\$app->controller->id . '_view') ? '{view}' : '') .
            ' ' .
            (Yii::\$app->getUser()->can(Yii::\$app->controller->module->id .
                '_' .
                Yii::\$app->controller->id . '_update') ? '{update}' : '') .
            ' ' .
            (Yii::\$app->getUser()->can(Yii::\$app->controller->module->id .
                '_' .
                Yii::\$app->controller->id . '_delete') ? '{delete}' : '') .
            ' ' .
            (!empty(Yii::\$app->controller->gridLinkActions) ?
                '{' . implode('} {', array_keys(Yii::\$app->controller->gridLinkActions)) . '}' : null),
        'buttons' => \$useModal ? [
            'update' => function (\$url, \$model, \$key) use (\$useModal) {
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', \$useModal ? '#modalForm' : \$url, [
                    'title' => Yii::t('app', 'Update'),
                    'data-toggle' => 'modal',
                    'data-url' => \$url,
                    'data-pjax' => 1,
                ]);
            },
            'delete' => function (\$url, \$model, \$key) use (\$useModal) {
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', \$useModal ? '#modalForm' : \$url, [
                    'title' => Yii::t('app', 'Delete'),
                    'data-url' => \$url,
                    'data-pjax' => 1,
                    'class' => 'ajaxDelete',
                ]);
            },
            'view' => function (\$url, \$modal, \$key) use (\$useModal) {
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', \$useModal ? '#modalForm' : \$url, [
                    'title' => Yii::t('app', 'View'),
                    'data-toggle' => 'modal',
                    'data-url' => \$url,
                    'data-pjax' => 1,
                ]);
            },
        ] : Yii::\$app->controller->gridLinkActions,
        'visibleButtons' => [
            'view' => function(\$model, \$key, \$index) {
                return \$model->readable();
            },
            'update' => function(\$model, \$key, \$index) {
                return \$model->editable();
            },
            'delete' => function(\$model, \$key, \$index) {
                return \$model->deletable();
            }
        ]
    ],\n
PHP;

$count = 0;
$selectedColumn = "";

foreach ($safeAttributes as $attribute) {
    $format = trim($generator->columnFormat($attribute, $model, 'index'));
    if ($format == false || in_array(strtolower($attribute), ['created_at', 'updated_at', 'created_by', 'updated_by'])
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
                echo str_repeat(' ', 4) . "[
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
            'data' => $foreignModelClass::find()->count() > 50 ? null : \\yii\\helpers\\ArrayHelper::map($foreignModelClass::find()->all(), '$foreignAttribute', 'toString'),
            'initValueText' => $foreignModelClass::find()->count() > 50 ? \\yii\\helpers\\ArrayHelper::map($foreignModelClass::find()->andWhere(['$foreignAttribute' => \$searchModel->{$attribute}])->all(), '$foreignAttribute', 'toString') : '',
            'options' => [
                'placeholder' => '',
                'multiple' => true,
            ],
            'pluginOptions' => [
                'allowClear' => true,
                ($foreignModelClass::find()->count() > 50 ? 'minimumInputLength' : '') => 3,
                ($foreignModelClass::find()->count() > 50 ? 'ajax' : '') => [
                    'url' => \\yii\\helpers\\Url::to(['list']),
                    'dataType' => 'json',
                    'data' => new \\yii\\web\\JsExpression('function(params) {
                        return {
                            q:params.term, m: \\'$shortForeignModel\\'
                        };
                    }')
                ],
            ]
        ],
    ],\n";
            }
        }
    }
    if ($fkFormat != null || $format == false) {
        continue;
    }
    if (++$count < $generator->gridMaxColumns) {
        $selectedColumn .= ($count - 1) . ", ";
        echo str_repeat(' ', 4) . "{$format},\n";
    } else {
        echo str_repeat(' ', 4) . "/*{$format}*/\n";
    }
}
$selectedColumn = rtrim($selectedColumn, ', ');
// action buttons first
echo $actionButtonColumn;
?>
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'headerOptions' => [
            'class' => 'kartik-sheet-style'
        ]
    ],
];
$exportColumns = $gridColumns;
foreach ($exportColumns as $column => $value) {
    // Remove checkbox and action columns from Excel export
    if (isset($value['class']) &&
        (strpos($value['class'], 'CheckboxColumn') !== false || strpos($value['class'], 'ActionColumn') !== false)
    ) {
        unset($exportColumns[$column]);
    }
}
?>
<?php echo "<?php \$this->beginBlock('info');
\yii\bootstrap\Modal::begin([
    'header' => '<h2>' . Yii::t('app', 'Information') . '</h2>',
    'toggleButton' => [
        'tag' => 'btn',
        'label' => '?',
        'class' => 'btn btn-default',
        'style' => 'border-bottom-right-radius: 3px; border-top-right-radius: 3px',
    ],
]);?>"?>
<?php echo "<?= \$this->render('@andrej2013-boilerplate/views/_info_modal') ?>" ?>
<?php echo "<?php \\yii\\bootstrap\\Modal::end();
\$this->endBlock(); ?>" ?>

<div class="box box-default">
    <div class="giiant-crud box-body <?= Inflector::camel2id(
        StringHelper::basename($generator->modelClass),
        '-',
        true
    )
    ?>-index">
<?= str_repeat(' ', 8) . "<?php\n" . str_repeat(' ', 8)  . ($generator->indexWidgetType === 'grid' ? '// ' : '') ?>
<?php if ($generator->searchModelClass !== '') : ?>
echo $this->render('_search', ['model' =>$searchModel]);
<?php endif; ?>
        ?>

        <div class="table-responsive">
            <?= '<?= ' ?>GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'filterSelector' => 'select[name="pageSize"]',
                'options' => [
                    'id' => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-grid'
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
                        'content' => $this->blocks['info'],
                    ],
                    [
                        'content' => \andrej2013\yiiboilerplate\widget\FilterWidget::widget(['model' => $searchModel, 'pjaxContainer' => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-pjax-container'])
                    ],
                    [
                        'content' => Html::a(
                            '<i class="glyphicon glyphicon-repeat"></i>',
                            ['index', 'reset' => 1],
                            ['class' => 'btn btn-default', 'title' => Yii::t('app', 'Reset Grid')]
                        )
                    ],
                    '{export}',
                    ($importer && Yii::$app->user->can('import')) ?
                    \andrej2013\yiiboilerplate\modules\import\widget\Importer::widget([
                        'dataProvider' => $dataProvider,
                        'header' => Yii::t('app', 'Import'),
                        'pjaxContainerId' => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-pjax-container',
                    ]) : '',
                    [
                        'content' => \andrej2013\yiiboilerplate\widget\PageSize::widget([
                            'pjaxContainerId' => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-pjax-container',
                            'model' => $searchModel,
                        ])
                    ],
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
                                'items' => $gridDropdownItems
                            ],
                            'options' => [
                                'class' => 'btn-default',
                                'style' => (empty($gridDropdownItems) ? 'display: none' : null)
                            ]
                        ])
                    ],
                ],
                'panel' => [
                    'heading' => "<h3 class=\"panel-title\"><i class=\"glyphicon glyphicon-list\"></i>  " .
                        <?= $generator->generateString(
                            Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))
                        )
                        ?> .
                        "</h3>" . \andrej2013\yiiboilerplate\modules\import\widget\ImportResult::widget(),
                    'type' => 'default',
                    'before' => (Yii::$app->getUser()->can(
                        Yii::$app->controller->module->id .
                        '_' .
                        Yii::$app->controller->id .
                        '_create'
                    ) ?
                        Html::a(
                            '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'New'),
                            [$useModal ? '#modalForm' : 'create'],
                            [
                                'class' => 'btn btn-success',
                                'data-pjax' => $useModal,
                                'data-toggle' => 'modal',
                                'data-url' => Url::toRoute('create')
                            ]
                        ) : '') . ' ' . (Yii::$app->getUser()->can('Authority') ?
<?php
    $items = '';
    $model = new $generator->modelClass();
?>
<?php foreach ($generator->getModelRelations($model) as $relation) : ?>
<?php
// relation dropdown links
$iconType = ($relation->multiple) ? 'arrow-right' : 'arrow-left';
if ($generator->isPivotRelation($relation)) {
    $iconType = 'random';
}
$controller = $generator->pathPrefix . Inflector::camel2id(
    StringHelper::basename($relation->modelClass),
    '-',
    true
);
    $isUser = new ReflectionClass($relation->modelClass);
    if ($isUser->getShortName() == 'User') {
        $route = "/user/admin";
    } else {
    $route = $generator->createRelationRoute($relation, 'index');
    }
$label = Inflector::titleize(StringHelper::basename($relation->modelClass), true);
$items .= <<<PHP

                                        [
                                            'url' => ['{$route}'],
                                            'label' => '<i class="glyphicon glyphicon-arrow-right">&nbsp;</i>' .
                                                {$generator->generateString($label)},
                                        ],
PHP;
?>
<?php endforeach; ?>
<?php $items .= "\n";?>
                            \yii\bootstrap\ButtonDropdown::widget([
                                'id' => 'giiant-relations',
                                'encodeLabel' => false,
                                'label' => '<span class="glyphicon glyphicon-paperclip"></span> ' .
                                    <?= $generator->generateString('Relations') ?>,
                                'dropdown' => [
                                    'options' => [
                                    ],
                                    'encodeLabels' => false,
                                    'items' => [<?= $items ?>
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
                    'fontAwesome' => true,
                    'label' => Yii::t('kvgrid', 'Export'),
                ],
                'exportConfig' => [
                    GridView::PDF => [],
                    GridView::HTML => [],
                    GridView::CSV => [],
                    GridView::TEXT => [],
                    GridView::JSON => [],
                    GridView::EXCEL => [
                        //Override default export option with ExportMenu Widget
                        'external' => true,
                        'label' => ExportMenu::widget([
                            'asDropdown' => false,
                            'dataProvider' => $dataProvider,
                            'showColumnSelector' => false,
                            'columns' => $exportColumns,
                            'fontAwesome' => true,
                            'exportConfig' => [
                                ExportMenu::FORMAT_HTML => false,
                                ExportMenu::FORMAT_PDF => false,
                                ExportMenu::FORMAT_CSV => false,
                                ExportMenu::FORMAT_EXCEL => false,
                                ExportMenu::FORMAT_TEXT => false,
                            ],
                        ]),
                    ],
                ],
                'striped' => true,
                'pjax' => true,
                'pjaxSettings' => [
                    'options' => [
                        'id' => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-pjax-container',
                    ],
                    'clientOptions' => [
                        'method' => 'POST'
                    ]
                ],
                'hover' => true,
                'pager' => [
                    'class' => yii\widgets\LinkPager::className(),
                    'firstPageLabel' => Yii::t('kvgrid', 'First'),
                    'lastPageLabel' => Yii::t('kvgrid', 'Last')
                ],
            ])
            ?>
        </div>
    </div>
</div>
<?= "<?php"?> \yii\bootstrap\Modal::begin([
    'size' => \yii\bootstrap\Modal::SIZE_DEFAULT,
    'header' => '<h4>' . Yii::t('app', 'Choose fields to edit') . ':</h4>',
    'id' => 'edit-multiple',
    'clientOptions' => [
        'backdrop' => 'static',
    ],
]);
?>
<?= "<?=" ?> Html::beginForm(['update-multiple'], 'POST'); ?>
<?php
foreach ($safeAttributes as $attribute) {
    $column = \yii\helpers\ArrayHelper::getValue($generator->getTableSchema()->columns, $attribute);
    if (in_array(
        strtolower($attribute),
        ['created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_by', 'deleted_at']
    )) {
        continue;
    }
    $db = Yii::$app->get('db', false);
    $uniqueIndexes = $db->getSchema()->findUniqueIndexes($generator->getTableSchema());

    //If is primary key or unique index skip
    if ($column === null || $column->isPrimaryKey || array_key_exists($attribute, $uniqueIndexes)) {
        continue;
    } ?>
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-1">
            <div class="checkbox">
                <label for="<?=$attribute?>">
                    <input
                        type="checkbox"
                        id="<?=$attribute?>"
                        name="<?=$attribute?>"
                        value="1"
                    ><?=$model->getAttributeLabel($attribute) ."\n"?>
                </label>
            </div>
        </div>
    </div>
<?php } ?>
<div class="clearfix"></div>
<button type="submit" class="btn btn-success" id="submit-multiple">Update</button>
<?= "<?="?> Html::endForm(); ?>

<?="<?php"?> \yii\bootstrap\Modal::end();
?>
<?= "\n<?php\n" ?>
$js = <<<JS
jQuery.fn.addHidden = function (name, value) {
    return this.each(function () {
        var input = $("<input>").attr("type", "hidden").attr("name", name).val(value);
        $(this).append($(input));
    });
};
$(document).ready(function () {
    $('#submit-multiple').on('click', function(e){
        e.preventDefault();
        var keys=$("#" + gridViewKey + "-grid").yiiGridView('getSelectedRows'),
            form = $(this).closest('form');
        // Remove old values to prevent duplicated Ids submit
        form.find('input[name="id[]"]').remove();
        form.addHidden('no-post', true);
        $.each(keys , function (key, value) {
            form.addHidden('id[]', value);
        });
        if (useModal) {
            $('body').addClass('kv-grid-loading');
            $.post(
                form.attr("action"),
                form.serialize()
            )
            .done(function (data) {
                $('#modalFormMultiple').modal('show');
                $('#modalFormMultiple').find('.modal-body').html(data);
                $('body').removeClass('kv-grid-loading');
                formSubmitEvent();
                $('.closeMultiple').on('click', function(e){
                    e.preventDefault();
                    $('#modalFormMultiple').modal('hide');
                });
            })
            return false;
        } else {
            form.submit();
        }
    });
});
JS;
$this->registerJs($js);
?>
<?= "<?php \\yii\\bootstrap\\Modal::begin([
    'size' => \\yii\\bootstrap\\Modal::SIZE_LARGE,
    'id' => 'modalFormMultiple',
    'clientOptions' => [
        'backdrop' => 'static',
    ],
]);
?>
<?php \\yii\\bootstrap\\Modal::end();
?>
<?php \\yii\\bootstrap\\Modal::begin([
    'size' => \\yii\\bootstrap\\Modal::SIZE_LARGE,
    'id' => 'modalForm',
    'clientOptions' => [
        'backdrop' => 'static',
    ],
]);
?>
<?php \\yii\\bootstrap\\Modal::end();
?>
<?php
if (\$useModal) {
    \\andrej2013\\yiiboilerplate\\modules\\backend\\assets\\ModalFormAsset::register(\$this);
}
?>
" ?>