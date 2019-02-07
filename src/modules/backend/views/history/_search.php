<?php
/**
 * /srv/www/nassi-v2/src/../runtime/giiant/9104fc58a45fdb0cbb2d50c83525c788
 *
 * @package default
 */


use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 *
 * @var yii\web\View $this
 * @var app\models\search\Address $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="address-search">

    <?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

            <?php echo $form->field($model, 'id') ?>

        <?php echo $form->field($model, 'city') ?>

        <?php echo $form->field($model, 'zip') ?>

        <?php echo $form->field($model, 'street') ?>

        <?php echo $form->field($model, 'number') ?>

        <?php // echo $form->field($model, 'comment') ?>

        <?php // echo $form->field($model, 'phone_number') ?>

        <?php // echo $form->field($model, 'fax_number') ?>

        <?php // echo $form->field($model, 'country') ?>

        <?php // echo $form->field($model, 'created_by') ?>

        <?php // echo $form->field($model, 'created_at') ?>

        <?php // echo $form->field($model, 'updated_by') ?>

        <?php // echo $form->field($model, 'updated_at') ?>

        <?php // echo $form->field($model, 'deleted_by') ?>

        <?php // echo $form->field($model, 'deleted_at') ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
