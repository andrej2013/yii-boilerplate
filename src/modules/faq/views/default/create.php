<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\models\Faq $model
 */

$this->title = Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Frequently Asked Questions Manage'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-<?php echo \Yii::$app->params['style']['primary_color']; ?>">
    <div
        class="giiant-crud box-body faq-create">

        <div class="clearfix crud-navigation">
            <div class="pull-left">
                <?= Html::a(
                    '<span class="fa fa-ban"></span> '.Yii::t('app', 'Cancel'),
                    \yii\helpers\Url::previous(),
                    [
                        'class' => 'btn',
                        'preset' => Html::PRESET_DANGER,
                    ]
                ) ?>
            </div>
        </div>

        <?= $this->render('_form', [
            'model' => $model,
            'hide'  => $hide,
        ]); ?>

    </div>
</div>
