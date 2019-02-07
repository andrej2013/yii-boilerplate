<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * /srv/www/nassi-v2/src/../runtime/giiant/5e8fd7d768b734cb74455f7c7b1f3a7e
 *
 * @package default
 */


use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;

/**
 *
 * @var yii\web\View                              $this
 * @var \andrej2013\yiiboilerplate\models\ArHistory $model
 * @var boolean                                   $useModal
 */
$copyParams = $model->attributes;

$this->title = Yii::t('app', 'Ar History') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ar History'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'View');
?>
<div class="box box-default">
    <div class="giiant-crud box-body" id="address-view">

        <!-- flash message -->
        <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
            <span class="alert alert-info alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <?php echo \Yii::$app->session->getFlash('deleteError') ?>
            </span>
        <?php endif; ?>
        <?php if (!$useModal) : ?>
            <div class="clearfix crud-navigation">
                <!-- menu buttons -->
                <div class='pull-left'>
                    <?php echo Yii::$app->getUser()->can(
                        Yii::$app->controller->module->id .
                        '_' .
                        \Yii::$app->controller->id .
                        '_update'
                    ) ?
                        Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('app', 'Edit'),
                            ['update', 'id' => $model->id],
                            ['class' => 'btn btn-info']
                        )
                        :
                        ''
                    ?>
                    <?php echo Yii::$app->getUser()->can(
                        Yii::$app->controller->module->id .
                        '_' .
                        \Yii::$app->controller->id .
                        '_create'
                    ) ?
                        Html::a(
                            '<span class="glyphicon glyphicon-copy"></span> ' . Yii::t('app', 'Copy'),
                            ['create', 'id' => $model->id, 'Address' => $copyParams],
                            ['class' => 'btn btn-success']
                        )
                        :
                        ''
                    ?>
                    <?php echo Yii::$app->getUser()->can(
                        Yii::$app->controller->module->id .
                        '_' .
                        \Yii::$app->controller->id .
                        '_create'
                    ) ?
                        Html::a(
                            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'New'),
                            ['create'],
                            ['class' => 'btn btn-success']
                        )
                        :
                        ''
                    ?>
                </div>
                <div class="pull-right">
                    <?php echo Html::a(
                        '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('app', 'List Ar History'),
                        ['index'],
                        ['class' => 'btn btn-default']
                    ) ?>
                </div>
            </div>
        <?php endif; ?>
        <?php $this->beginBlock('Ar History'); ?>


        <?php echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'table_name',
                'field_name',
                'row_id',
                'event',
                'old_value',
                'new_value',
                [
                    'attribute' => 'created_by',
                    'format' => 'html',
                    'value' => ($model->getUser()->one() ?
                        Html::a(
                            $model->getUser()->one()->toString,
                            [
                                '/user/admin/update-profile',
                                'id' => $model->getUser()->one()->id,
                            ]
                        )
                        :
                        '<span class="label label-warning">?</span>'
                    ),
                ],
                [
                    'format' => 'dateTime',
                    'attribute' => 'created_at',
                ],
            ],
        ]); ?>


        <hr/>

        <?php echo Yii::$app->getUser()->can(
            Yii::$app->controller->module->id . '_' . \Yii::$app->controller->id . '_delete'
        ) ?
            Html::a(
                '<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('app', 'Delete'),
                $useModal ? false : ['delete', 'id' => $model->id],
                [
                    'class' => 'btn btn-danger' . ($useModal ? ' ajaxDelete' : ''),
                    'data-url' => Url::toRoute(['delete', 'id' => $model->id]),
                    'data-confirm' => $useModal ? false : Yii::t('app', 'Are you sure to delete this item?'),
                    'data-method' => $useModal ? false : 'post',
                ]
            )
            :
            ''
        ?>

        <?php $this->endBlock(); ?>

        <?php echo Tabs::widget(
            [
                'id' => 'relation-tabs',
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => '<b class=""># ' . $model->id . '</b>',
                        'content' => $this->blocks['Ar History'],
                        'active' => true,
                    ],
                ]
            ]
        );
        ?>
    </div>
</div>
