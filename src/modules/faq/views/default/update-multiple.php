<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\modules\faq\models\Faq $model
 */

$this->title = 'Faq ' . $model->title . ', ' . Yii::t('app', 'Edit Multiple');
$this->params['breadcrumbs'][] = ['label' => 'Faqs', 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit Multiple');
?>
<div class="box box-default">
    <div
        class="giiant-crud box-body faq-update">

        <h1>
            <?= Yii::t('app', 'Faq') ?>
        </h1>

        <div class="crud-navigation">
        </div>

        <?php echo $this->render('_form', [
            'model' => $model,
            'pk' => $pk,
            'show' => $show,
            'multiple' => true,
        ]); ?>

    </div>
</div>