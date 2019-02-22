<?php
/**
 * /home/ntesic/www/yii2-my-starter-kit/src/../runtime/giiant/d4b4964a63cc95065fa0ae19074007ee
 *
 * @package default
 */


use yii\helpers\Html;
use yii\helpers\Url;
use andrej2013\yiiboilerplate\grid\GridView;
use yii\widgets\DetailView;
use dmstr\bootstrap\Tabs;

/**
 *
 * @var yii\web\View $this
 * @var andrej2013\yiiboilerplate\models\ArHistory $model
 */
$copyParams = $model->attributes;

$this->title = Yii::t('app', 'Ar History') . ' ' . $model->toString;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ar Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->toString, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'View');
?>
<div class="box box-<?php echo \Yii::$app->params['style']['primary_color']; ?>">
    <div class="giiant-crud box-body" id="ar-history-view">

            <div class="clearfix crud-navigation">
                <!-- menu buttons -->
                <div class='pull-left'>
                    
                </div>
                <div class="pull-right">
                    <?php echo Html::a(
    '<span class="fa fa-list"></span> ' . Yii::t('app', 'List ArHistories'),
    ['index'],
    [
        'class' => 'btn',
        'color' => Html::TYPE_DEFAULT,
    ]
) ?>
                </div>
            </div>

        <?php $this->beginBlock('andrej2013\yiiboilerplate\models\ArHistory'); ?>
                <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'table_name',
            'row_id',
            'event',
            'field_name',
            [
                'attribute' => 'old_value',
                'content' => function ($model) {
                    return nl2br(strlen($model->old_value) > 500 ? substr($model->old_value, 0, 500) . '...' : $model->old_value);
                },
            ],
            [
                'attribute' => 'new_value',
                'content' => function ($model) {
                    return nl2br(strlen($model->new_value) > 500 ? substr($model->new_value, 0, 500) . '...' : $model->new_value);
                },
            ],
        ],
    ]); ?>


        <?php echo Yii::$app->getUser()->can(
    Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_delete'
) && $model->deletable() ?
    Html::a(
    '<span class="fa fa-trash"></span> ' . Yii::t('app', 'Delete'),
    ['delete', 'id' => $model->id],
    [
        'class' => 'btn',
        'preset' => Html::PRESET_DANGER,
        'data-url' => Url::toRoute(['delete', 'id' => $model->id]),
        'data-confirm' => Yii::t('app', 'Are you sure to delete this item?'),
        'data-method' => 'post',
    ]
)
    : ''
?>
        <?php $this->endBlock(); ?>


                <div class="nav-tabs-custom">
        <?php echo Tabs::widget(
    [
        'id' => 'relation-tabs',
        'encodeLabels' => false,
        'items' => [
            [
                'label'     => '<b> ' . $model->toString . '</b>',
                'content'   => $this->blocks['andrej2013\yiiboilerplate\models\ArHistory'],
                'active'    => true,
            ],
        ]
    ]
);
?>        </div>
    </div>
</div>