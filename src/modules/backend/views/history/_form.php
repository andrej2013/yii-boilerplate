<?php
/**
 * /home/ntesic/www/yii2-my-starter-kit/src/../runtime/giiant/4b7e79a8340461fe629a6ac612644d03
 *
 * @package default
 */


use yii\helpers\ArrayHelper;
use andrej2013\yiiboilerplate\widget\Select2;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\Inflector;
use yii\helpers\Url;
use kartik\helpers\Html;
use andrej2013\yiiboilerplate\widget\DepDrop;
use andrej2013\yiiboilerplate\modules\backend\widgets\RelatedForms;

/**
 *
 * @var yii\web\View $this
 * @var app\models\ArHistory $model
 * @var yii\widgets\ActiveForm $form
 * @var array $pk
 * @var array $show
 * @var string $action
 * @var boolean $is_popup value to know if this form is in popup
 * @var string $caller_id represent id of select2 input from which is popup opened. In main form it is always ID without anything appended
 */
$caller_id = $is_popup ? ('_from_' . $caller_id) : '';
?>
<div class="hide header_title"><?php echo Html::tag('h4', Yii::t('app', 'Ar History'));?></div>
<div class="ar-history-form">
    <?php $form = ActiveForm::begin([
		'fieldClass' => '\andrej2013\yiiboilerplate\widget\ActiveField',
		'id' => 'ArHistory' . ($is_popup ? '_popup_' . $caller_id : ''),
		'layout' => 'horizontal',
		'enableClientValidation' => true,
		'errorSummaryCssClass' => 'error-summary alert alert-error',
		'action' => $action,
		'options' => [
			'name' => 'ArHistory',
		],
	]);
?>
<?php $this->beginBlock('main'); ?>
<?php echo $form->field(
	$model,
	'table_name',
	[
		'selectors' => [
			'input' => '#'.Html::getInputId($model, 'table_name') . $caller_id
		]
	]
)
->textInput([
		'id' => Html::getInputId($model, 'table_name') . $caller_id,
		'maxlenght' => true,
		'placeholder' => $model->getAttributePlaceholder('table_name'),
		'input' => '#'.Html::getInputId($model, 'table_name') . $caller_id
	])
->hint($model->getAttributeHint('table_name'))?>
<?php echo $form->field(
	$model,
	'event',
	[
		'selectors' => [
			'input' => '#'.Html::getInputId($model, 'event') . $caller_id
		]
	]
)
->textInput([
		'id' => Html::getInputId($model, 'event') . $caller_id,
		'placeholder' => $model->getAttributePlaceholder('event'),
		'type' => 'number',
		'step' => 1,
		'min' => ''
	])
->hint($model->getAttributeHint('event'))?>
<?php echo $form->field(
	$model,
	'field_name',
	[
		'selectors' => [
			'input' => '#'.Html::getInputId($model, 'field_name') . $caller_id
		]
	]
)
->textInput([
		'id' => Html::getInputId($model, 'field_name') . $caller_id,
		'maxlenght' => true,
		'placeholder' => $model->getAttributePlaceholder('field_name'),
		'input' => '#'.Html::getInputId($model, 'field_name') . $caller_id
	])
->hint($model->getAttributeHint('field_name'))?>
<?php echo $form->field(
	$model,
	'old_value',
	[
		'selectors' => [
			'input' => '#'.Html::getInputId($model, 'old_value') . $caller_id
		]
	]
)
->textarea([
		'id' => Html::getInputId($model, 'old_value') . $caller_id,
		'rows' => 6,
		'placeholder' => $model->getAttributePlaceholder('old_value')
	])
->hint($model->getAttributeHint('old_value'));?>
<?php echo $form->field(
	$model,
	'new_value',
	[
		'selectors' => [
			'input' => '#'.Html::getInputId($model, 'new_value') . $caller_id
		]
	]
)
->textarea([
		'id' => Html::getInputId($model, 'new_value') . $caller_id,
		'rows' => 6,
		'placeholder' => $model->getAttributePlaceholder('new_value')
	])
->hint($model->getAttributeHint('new_value'));?>
        <?php $this->endBlock(); ?>
            <div class="nav-tabs-custom">
        <?php echo Tabs::widget([
		'encodeLabels' => false,
		'items' => [
			[
				'label' => Yii::t('app', Inflector::camel2words('ArHistory')),
				'content' => $this->blocks['main'],
				'active' => true,
			],
		]
	])
?>    </div>
        <?php echo $form->errorSummary($model); ?>
    <div class="pull-right">
        <?php echo Html::submitButton(
	'<span class="fa fa-check"></span> ' .
	($model->isNewRecord && !$multiple ?
		Yii::t('app', 'Create') :
		Yii::t('app', 'Save')),
	[
		'id' => 'save-' . $model->formName(),
		'class' => 'btn',
		'preset'    => Html::PRESET_PRIMARY,
		'name' => 'submit-default'
	]
);
?>
        <?php if (!$is_popup) { ?>
                    <?php echo Html::submitButton(
		'<span class="fa fa-check"></span> ' .
		($model->isNewRecord && !$multiple ?
			Yii::t('app', 'Create & New') :
			Yii::t('app', 'Save & New')),
		[
			'id' => 'save-' . $model->formName(),
			'class' => 'btn',
			'preset'    => Html::PRESET_SECONDARY,
			'name' => 'submit-new'
		]
	);
?>
                            <?php echo Html::submitButton(
		'<span class="fa fa-check"></span> ' .
		($model->isNewRecord && !$multiple ?
			Yii::t('app', 'Create & Close') :
			Yii::t('app', 'Save & Close')),
		[
			'id' => 'save-' . $model->formName(),
			'class' => 'btn',
			'preset'    => Html::PRESET_SECONDARY,
			'name' => 'submit-close'
		]
	);
?>
                <?php  } ?>
        </div>
            <?php if (!$model->isNewRecord && Yii::$app->getUser()->can(Yii::$app->controller->module->id .
		'_' . Yii::$app->controller->id . '_delete') && $model->deletable() && !$is_popup) { ?>
                <?php echo Html::a(
		'<span class="fa fa-trash"></span> ' .
		Yii::t('app', 'Delete'),
		['delete', 'id' => $model->id],
		[
			'class' => 'btn btn-danger',
			'preset'    => Html::PRESET_DANGER,
			'data-confirm' => '' . Yii::t('app', 'Are you sure to delete this item?') . '',
			'data-method' => 'post',
		]
	);
?>
            <?php } ?>
        <?php if ($is_popup) { ?>
            <?php echo Html::a(
		'<span class="fa fa-ban"></span> ' .
		Yii::t('app', 'Close'),
		['#'],
		[
			'class' => 'btn btn-danger pull-left',
			'preset'    => Html::PRESET_DANGER,
			'data-dismiss' => 'modal',
			'name' => 'close'
		]
	);
?>
        <?php } ?>
        <?php ActiveForm::end(); ?>
</div>
<?php
\yii\helpers\Html::tag('div', null, ['class' => 'clearfix']);
