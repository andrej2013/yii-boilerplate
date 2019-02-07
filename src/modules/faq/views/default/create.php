<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\modules\faq\models\Faq $model
 */

$this->title = Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => 'Faqs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-default">
    <div
        class="giiant-crud box-body faq-create">

        <h1>
            <?= Yii::t('app', 'Faq') ?>
            <small>
                <?= $model->title ?>
            </small>
        </h1>

        <div class="clearfix crud-navigation">
            <div class="pull-left">
                <?= Html::a(
                    Yii::t('app', 'Cancel'),
                    \yii\helpers\Url::previous(),
                    ['class' => 'btn btn-default']) ?>
            </div>
        </div>

        <?= $this->render('_form', [
            'model' => $model,
        ]); ?>

    </div>
</div>
