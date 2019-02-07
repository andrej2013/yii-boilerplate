<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * /srv/www/nassi-v2/src/../runtime/giiant/375156467ad52c46593e305e25d54430
 *
 * @package default
 */


use yii\helpers\Html;

/**
 *
 * @var yii\web\View                              $this
 * @var \andrej2013\yiiboilerplate\models\ArHistory $model
 * @var string                                    $relatedTypeForm
 */
$this->title = Yii::t('app', 'Ar History') . ' ' . $model->id . ', ' . Yii::t('app', 'Edit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ar History'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="box box-default">
    <div
        class="giiant-crud box-body address-update">

        <div class="crud-navigation">
            <?php echo Html::a(
                '<span class="glyphicon glyphicon-eye-open"></span> ' . Yii::t('app', 'View'),
                ['view', 'id' => $model->id],
                ['class' => 'btn btn-default']
            ) ?>
        </div>

        <?php echo $this->render('_form', [
            'model' => $model,
            'inlineForm' => $inlineForm,
            'relatedTypeForm' => $relatedTypeForm,
        ]); ?>

    </div>
</div>
