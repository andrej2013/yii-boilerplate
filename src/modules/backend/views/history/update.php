<?php
/**
 * /home/ntesic/www/yii2-my-starter-kit/src/../runtime/giiant/fcd70a9bfdf8de75128d795dfc948a74
 *
 * @package default
 */


use yii\helpers\Html;

/**
 *
 * @var yii\web\View $this
 * @var app\models\ArHistory $model
 * @var string $relatedTypeForm
 */
$this->title = Yii::t('app', 'Ar History') . ' ' . $model->id . ', ' . Yii::t('app', 'Edit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ar Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="box box-<?php echo \Yii::$app->params['style']['primary_color']; ?>">
    <div
        class="giiant-crud box-body ar-history-update">

        <div class="crud-navigation">
            <?php echo Html::a(
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
	]); ?>

    </div>
</div>
