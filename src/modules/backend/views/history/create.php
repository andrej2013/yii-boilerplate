<?php
/**
 * /home/ntesic/www/yii2-my-starter-kit/src/../runtime/giiant/fccccf4deb34aed738291a9c38e87215
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
$this->title = Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ar Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-<?php echo \Yii::$app->params['style']['primary_color']; ?>">
    <div
        class="giiant-crud box-body ar-history-create">

        <div class="clearfix crud-navigation">
            <div class="pull-left">
                <?php echo Html::a(
	'<span class="fa fa-ban"></span> '.Yii::t('app', 'Cancel'),
	\yii\helpers\Url::previous(),
	[
		'class' => 'btn',
		'preset' => Html::PRESET_DANGER,
	]
) ?>
            </div>
        </div>

        <?php echo $this->render('_form', [
		'model' => $model,
	]); ?>

    </div>
</div>
