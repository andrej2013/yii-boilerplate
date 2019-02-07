<?php
/**
 * /srv/www/nassi-v2/src/../runtime/giiant/b18644106c982bf7c91ad36ce7219022
 *
 * @package default
 */


use yii\helpers\Html;

/**
 *
 * @var yii\web\View $this
 * @var app\models\Address $model
 * @var array $pk
 * @var array $show
 */
$this->title = 'Address ' . $model->id . ', ' . Yii::t('app', 'Edit Multiple');
$this->params['breadcrumbs'][] = ['label' => 'Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit Multiple');
?>
<div class="box box-default">
    <div
        class="giiant-crud box-body address-update">

        <h1>
            <?php echo Yii::t('app', 'Address') ?>
        </h1>

        <div class="crud-navigation">
        </div>

        <?php echo $this->render('_form', [
		'model' => $model,
		'pk' => $pk,
		'show' => $show,
		'multiple' => true,
		'useModal' => $useModal,
		'action' => $action,
	]); ?>

    </div>
</div>
