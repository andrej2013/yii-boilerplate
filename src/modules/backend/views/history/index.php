<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * /srv/www/nassi-v2/src/../runtime/giiant/8d7617987c202c306c0b7e366206e7e4
 *
 * @package default
 */


use yii\helpers\Html;
use yii\helpers\Url;
use andrej2013\yiiboilerplate\grid\GridView;
use kartik\export\ExportMenu;
// use yii\grid\GridView;
use yii\web\View;

/**
 *
 * @var yii\web\View                                           $this
 * @var yii\data\ActiveDataProvider                            $dataProvider
 * @var boolean                                                $useModal
 * @var boolean                                                $importer
 * @var \andrej2013\yiiboilerplate\models\search\ArHistorySearch $searchModel
 */
$this->title = Yii::t('app', 'Ar History');
$this->params['breadcrumbs'][] = $this->title;


/* ------- Multiple-Delete Batch Action ------ */
$inlineScript = 'var gridViewKey = "address", useModal = ' . ($useModal ? 'true' : 'false') . ';';
$this->registerJs($inlineScript, View::POS_HEAD, 'my-inline-js');

$gridColumns = [
    'table_name',
    'field_name',
    'row_id',
    'event',
    'old_value',
    'new_value',
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'created_by',
        'format' => 'html',
        'content' => function ($model) {
            if ($model->created_by) {
                return Html::a($model->user->toString, ['/user/admin/update-profile', 'id' => $model->created_by]);
            }
        },
        'filter' => \yii\helpers\ArrayHelper::map(app\models\User::find()->all(), 'id', 'toString'),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'options' => [
                'placeholder' => '',
                'multiple' => true,
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ]
        ],
    ],
    [
        'attribute' => 'created_at',
        'content' => function ($model) {
            return \Yii::$app->formatter->asDatetime($model->created_at);;
        },
        'class' => '\kartik\grid\DataColumn',
        'format' => 'date',
        'filterType' => GridView::FILTER_DATE_RANGE,
        'filterWidgetOptions' => [
            'presetDropdown' => true,
            'pluginOptions' => [
                'opens'=>'left',
                'locale' => [
                    'format' => 'YYYY-MM-DD',
                    'separator' => ' TO ',
                ]
            ],
        ],
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'urlCreator' => function ($action, $model, $key, $index) {

            /**
             *
             * @var \yii\db\ActiveRecord $model
             */
            // using the column name as key, not mapping to 'id' like the standard generator
            $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string)$key];
            $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;
            return Url::toRoute($params);
        },
        'contentOptions' => [
            'nowrap' => 'nowrap',
        ],
        'template' => (Yii::$app->getUser()->can(Yii::$app->controller->module->id .
                '_' .
                \Yii::$app->controller->id . '_view') ? '{view}' : '') .
            ' ' .
            (Yii::$app->getUser()->can(Yii::$app->controller->module->id .
                '_' .
                \Yii::$app->controller->id . '_update') ? '{update}' : '') .
            ' ' .
            (Yii::$app->getUser()->can(Yii::$app->controller->module->id .
                '_' .
                \Yii::$app->controller->id . '_delete') ? '{delete}' : ''),
        'buttons' => $useModal ? [
            'update' => function ($url, $model, $key) use ($useModal) {
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $useModal ? '#modalForm' : $url, [
                    'title' => Yii::t('app', 'Update'),
                    'data-toggle' => 'modal',
                    'data-url' => $url,
                    'data-pjax' => 1,
                ]);
            },
            'delete' => function ($url, $model, $key) use ($useModal) {
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $useModal ? '#modalForm' : $url, [
                    'title' => Yii::t('app', 'Delete'),
                    'data-url' => $url,
                    'data-pjax' => 1,
                    'class' => 'ajaxDelete',
                ]);
            },
            'view' => function ($url, $modal, $key) use ($useModal) {
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $useModal ? '#modalForm' : $url, [
                    'title' => Yii::t('app', 'View'),
                    'data-toggle' => 'modal',
                    'data-url' => $url,
                    'data-pjax' => 1,
                ]);
            },
        ] : [],
    ],
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
<?php $this->beginBlock('info');
\yii\bootstrap\Modal::begin([
    'header' => '<h2>' . Yii::t('app', 'Information') . '</h2>',
    'toggleButton' => [
        'tag' => 'btn',
        'label' => '?',
        'class' => 'btn btn-default',
        'style' => 'border-bottom-right-radius: 3px; border-top-right-radius: 3px',
    ],
]); ?><?php echo $this->render('@andrej2013-boilerplate/views/_info_modal') ?><?php \yii\bootstrap\Modal::end();
$this->endBlock(); ?>
<div class="box box-default">
    <div class="giiant-crud box-body address-index">
        <?php
        // echo $this->render('_search', ['model' =>$searchModel]);
        ?>

        <div class="table-responsive">
            <?php echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'options' => [
                    'id' => 'address-grid'
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
                        'content' => Html::a(
                            '<i class="glyphicon glyphicon-repeat"></i>',
                            ['index'],
                            ['class' => 'btn btn-default', 'title' => Yii::t('app', 'Reset Grid')]
                        )
                    ],
//                    '{export}',
                    ($importer && Yii::$app->user->can('import')) ?
                        \andrej2013\yiiboilerplate\modules\import\widget\Importer::widget([
                            'dataProvider' => $dataProvider,
                            'header' => Yii::t('app', 'Import'),
                            'pjaxContainerId' => 'address-pjax-container',
                        ]) : '',
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
                                    (Yii::$app->getUser()->can(
                                        Yii::$app->controller->module->id .
                                        '_' .
                                        \Yii::$app->controller->id .
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
                                ]
                            ],
                            'options' => [
                                'class' => 'btn-default'
                            ]
                        ])
                    ],
                ],
                'panel' => [
                    'heading' => "<h3 class=\"panel-title\"><i class=\"glyphicon glyphicon-list\"></i>  " .
                        Yii::t('app', 'Ar History') .
                        "</h3>" . \andrej2013\yiiboilerplate\modules\import\widget\ImportResult::widget(),
                    'type' => 'default',
                    'before' => (\Yii::$app->getUser()->can(
                            Yii::$app->controller->module->id .
                            '_' .
                            \Yii::$app->controller->id .
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
                            ) : '')
                    ,
                    'after' => '{pager}',
                    'footer' => false
                ],
                // set export properties
                /*'export' => [
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
                ],*/
                'striped' => true,
                'pjax' => true,
                'pjaxSettings' => [
                    'options' => [
                        'id' => 'address-pjax-container',
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
<?php \yii\bootstrap\Modal::begin([
    'size' => \yii\bootstrap\Modal::SIZE_DEFAULT,
    'header' => '<h4>' . Yii::t('app', 'Choose fields to edit') . ':</h4>',
    'id' => 'edit-multiple',
    'clientOptions' => [
        'backdrop' => 'static',
    ],
]);
?>
<?php echo Html::beginForm(['update-multiple'], 'POST'); ?>
<div class="form-group">
    <div class="col-sm-6 col-sm-offset-1">
        <div class="checkbox">
            <label for="city">
                <input
                    type="checkbox"
                    id="city"
                    name="city"
                    value="1"
                >City
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-6 col-sm-offset-1">
        <div class="checkbox">
            <label for="street">
                <input
                    type="checkbox"
                    id="street"
                    name="street"
                    value="1"
                >Street
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-6 col-sm-offset-1">
        <div class="checkbox">
            <label for="country">
                <input
                    type="checkbox"
                    id="country"
                    name="country"
                    value="1"
                >Country
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-6 col-sm-offset-1">
        <div class="checkbox">
            <label for="comment">
                <input
                    type="checkbox"
                    id="comment"
                    name="comment"
                    value="1"
                >Comment
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-6 col-sm-offset-1">
        <div class="checkbox">
            <label for="phone_number">
                <input
                    type="checkbox"
                    id="phone_number"
                    name="phone_number"
                    value="1"
                >Phone Number
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-6 col-sm-offset-1">
        <div class="checkbox">
            <label for="fax_number">
                <input
                    type="checkbox"
                    id="fax_number"
                    name="fax_number"
                    value="1"
                >Fax Number
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-6 col-sm-offset-1">
        <div class="checkbox">
            <label for="zip">
                <input
                    type="checkbox"
                    id="zip"
                    name="zip"
                    value="1"
                >Zip
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-6 col-sm-offset-1">
        <div class="checkbox">
            <label for="number">
                <input
                    type="checkbox"
                    id="number"
                    name="number"
                    value="1"
                >Number
            </label>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<button type="submit" class="btn btn-success" id="submit-multiple">Update</button>
<?php echo Html::endForm(); ?>

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
<?php \yii\bootstrap\Modal::begin([
    'size' => \yii\bootstrap\Modal::SIZE_LARGE,
    'id' => 'modalFormMultiple',
    'clientOptions' => [
        'backdrop' => 'static',
    ],
]);
?>
<?php \yii\bootstrap\Modal::end();
?>
<?php \yii\bootstrap\Modal::begin([
    'size' => \yii\bootstrap\Modal::SIZE_LARGE,
    'id' => 'modalForm',
    'clientOptions' => [
        'backdrop' => 'static',
    ],
]);
?>
<?php \yii\bootstrap\Modal::end();
?>
<?php
if ($useModal) {
    \andrej2013\yiiboilerplate\modules\backend\assets\ModalFormAsset::register($this);
}
?>
