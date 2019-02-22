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


$user_id = $model->user->id;
$ga = new \andrej2013\yiiboilerplate\modules\user\models\twoway\lib\GoogleAuthenticator();
echo Html::beginTag('div', ['class' => 'text-center']);
echo Html::img($ga->getQRCodeGoogleUrl($model->user->email, $qr, getenv('APP_TITLE')), [
    'class' => 'qr-code-image',
]);
echo Html::endTag('div');
?>
<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'action' => \yii\helpers\Url::toRoute('/user/security/check-code'),
    'enableAjaxValidation' => false,
]) ?>
<?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>
<?= $form->field(
    $model,
    'code',
    ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'tabindex' => '1']]
) ?>

    <div class="alert alert-danger" style="display: none" id="info"></div>
<?= Html::submitButton(
    Yii::t('user', 'Sign in'),
    ['class' => 'btn btn-primary btn-block', 'tabindex' => '3']
) ?>

<?php
ActiveForm::end();
