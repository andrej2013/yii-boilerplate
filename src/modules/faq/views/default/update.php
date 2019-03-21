<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\models\Faq $model
 */

$this->title = Yii::t('app', 'Frequently Asked Questions Manage') . ' ' . $model->toString . ', ' . Yii::t('app', 'Edit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Frequently Asked Questions Manage'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->toString, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="box box-<?php echo \Yii::$app->params['style']['primary_color']; ?>">
    <div
        class="giiant-crud box-body faq-update">

        <div class="crud-navigation">
            <?= Html::a(
                '<span class="fa fa-eye"></span> ' . Yii::t('app', 'View'),
                ['view', 'id' => $model->id],
                [
                    'class' => 'btn',
                    'preset'    => Html::PRESET_PRIMARY,
                ]
            ) ?>
        </div>

        <?php echo $this->render('_form', [
            'model' => $model,
            'hide'  => $hide,
        ]); ?>

    </div>
</div>