<?php

use yii\helpers\Html;
use yii\helpers\Url;
use andrej2013\yiiboilerplate\grid\GridView;
use yii\widgets\DetailView;
use dmstr\bootstrap\Tabs;

/**
 * @var yii\web\View   $this
 * @var app\models\Faq $model
 */
$copyParams = $model->attributes;

$this->title = Yii::t('app', 'Faq') . ' ' . $model->toString;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Faqs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->toString, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'View');
?>
<div class="box box-<?php echo \Yii::$app->params['style']['primary_color']; ?>">
    <div class="giiant-crud box-body" id="faq-view">

        <div class="clearfix crud-navigation">
            <!-- menu buttons -->
            <div class='pull-left'>
                <?= Yii::$app->getUser()
                             ->can(Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_update' && $model->editable()) ? Html::a('<span class="fa fa-pencil"></span> ' . Yii::t('app', 'Edit'), [
                        'update',
                        'id' => $model->id,
                    ], [
                        'class'  => 'btn',
                        'preset' => Html::PRESET_SECONDARY,
                    ]) : '' ?>                    <?= Yii::$app->getUser()
                                                               ->can(Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_create') ? Html::a('<span class="fa fa-copy"></span> ' . Yii::t('app', 'Copy'), [
                        'create',
                        'id'  => $model->id,
                        'Faq' => $copyParams,
                    ], [
                        'class'  => 'btn',
                        'preset' => Html::PRESET_SECONDARY,
                    ]) : '' ?>                    <?= Yii::$app->getUser()
                                                               ->can(Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_create') ? Html::a('<span class="fa fa-plus"></span> ' . Yii::t('app', 'New'), ['create'], [
                        'class'  => 'btn',
                        'preset' => Html::PRESET_PRIMARY,
                    ]) : '' ?>
            </div>
            <div class="pull-right">
                <?= Html::a('<span class="fa fa-list"></span> ' . Yii::t('app', 'List Faqs'), ['index'], [
                        'class' => 'btn',
                        'color' => Html::TYPE_DEFAULT,
                    ]) ?>
            </div>
        </div>

        <?php $this->beginBlock('app\models\Faq'); ?>
        <?= DetailView::widget([
            'model'      => $model,
            'attributes' => [
                'title',
                [
                    'attribute' => 'content',
                    'content'   => function ($model) {
                        return nl2br(strlen($model->content) > 500 ? substr($model->content, 0, 500) . '...' : $model->content);
                    },
                    'format' => 'raw',
                ],
                /*Generated by andrej2013\yiiboilerplate\templates\crud\providers\Select2Provider::attributeFormat*/
                [
                    'format'    => 'html',
                    'attribute' => 'language_id',
                    'value'     => function ($model) {
                        $foreign = $model->getLanguage()
                                         ->one();
                        if ($foreign) {
                            return $foreign->toString;
                        }
                        return '<span class="label label-warning">?</span>';
                    },
                ],
                [
                    'attribute' => 'level',
                    'value'   => function($model) {
                        return $model->levelName;
                    } 
                ],
                'order',
            ],
        ]); ?>


        <?= Yii::$app->getUser()
                     ->can(Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_delete') && $model->deletable() ? Html::a('<span class="fa fa-trash"></span> ' . Yii::t('app', 'Delete'), [
                'delete',
                'id' => $model->id,
            ], [
                'class'        => 'btn',
                'preset'       => Html::PRESET_DANGER,
                'data-url'     => Url::toRoute(['delete', 'id' => $model->id]),
                'data-confirm' => Yii::t('app', 'Are you sure to delete this item?'),
                'data-method'  => 'post',
            ]) : '' ?>
        <?php $this->endBlock(); ?>


        <div class="nav-tabs-custom">
            <?= Tabs::widget([
                    'id'           => 'relation-tabs',
                    'encodeLabels' => false,
                    'items'        => [
                        [
                            'label'   => '<b> ' . $model->toString . '</b>',
                            'content' => $this->blocks['app\models\Faq'],
                            'active'  => true,
                        ],
                        [
                            'content' => \andrej2013\yiiboilerplate\widget\HistoryTab::widget(['model' => $model]),
                            'label'   => '<small>' . Yii::t('app', 'History') . '&nbsp;<span class="badge badge-default">' . $model->getHistory()
                                                                                                                                   ->count() . '</span></small>',
                            'active'  => false,
                            'visible' => Yii::$app->user->can('Administrator'),
                        ],
                    ],
                ]); ?>        </div>
        <?= andrej2013\yiiboilerplate\widget\RecordHistory::widget(['model' => $model]) ?>
    </div>
</div>
