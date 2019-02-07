<?php

/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

use dektrium\user\widgets\Connect;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \andrej2013\yiiboilerplate\modules\user\models\UserAuthCode;

/**
 * @var yii\web\View                   $this
 * @var dektrium\user\models\LoginForm $model
 * @var dektrium\user\Module           $module
 * @var string                         $qr
 */

$this->title = Yii::t('user', 'Sign in');


$checkUrl = \yii\helpers\Url::toRoute('/user/security/check-code');
$user_id = $model->user->id;
$refreshLink = \yii\helpers\Url::toRoute('/user/security/refresh-code');
echo Html::img(\andrej2013\yiiboilerplate\modules\user\models\UserAuthCode::generateQr($qr), [
    'class' => 'qr-code-image',
]);
$form = ActiveForm::begin([
    'id' => 'login-form',
    'action' => \yii\helpers\Url::toRoute('/user/security/check-code'),
    'enableAjaxValidation' => false,
]) ?>
<?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'code')->hiddenInput()->label(false) ?>

<?php
ActiveForm::end();

$js = "
var success = '" . \andrej2013\yiiboilerplate\modules\user\models\TwoWay::SUCCESS . "',
    error = '" . \andrej2013\yiiboilerplate\modules\user\models\TwoWay::ERROR. "',
    expired = '" . \andrej2013\yiiboilerplate\modules\user\models\TwoWay::EXPIRED . "',
    refreshLink = '" . $refreshLink . "';
";
$this->registerJs($js, \yii\web\View::POS_BEGIN);

\andrej2013\yiiboilerplate\modules\user\assets\TwoWayAssets::register($this);
?>

<div class="alert alert-danger" style="display: none" id="info"></div>
