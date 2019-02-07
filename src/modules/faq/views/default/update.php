<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\modules\faq\models\Faq $model
 */

$this->title = 'Faq ' . $model->title . ', ' . Yii::t('app', 'Edit');
$this->params['breadcrumbs'][] = ['label' => 'Faqs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="box box-default">
    <div
        class="giiant-crud box-body faq-update">

        <h1>
            <?= Yii::t('app', 'Faq') ?>
            <small>
                <?= $model->title ?>
            </small>
        </h1>

        <div class="crud-navigation">
            <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span> ' . Yii::t('app', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
        </div>

        <?php echo $this->render('_form', [
            'model' => $model,
        ]); ?>

    </div>
</div>