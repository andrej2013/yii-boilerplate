<?php

/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\LoginForm $model
 * @var dektrium\user\Module $module
 */

$this->title = Yii::t('user', 'Sign in');
?>


<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnBlur' => false,
    'validateOnType' => false,
    'validateOnChange' => false,
]) ?>

<?= $form->field(
    $model,
    'login',
    ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control keyboard keyboard-numpad', 'tabindex' => '1', 'type' => 'tel']]
) ?>

<style>
    #login-form-login {
        height: auto;
    }
    #login-form-login, .number-button, button[type="submit"] {
        font-size: 3em;
    }
    .number-pad {
        width: 100%;
        display: inline-block;
        padding-right: 5px;
    }
    .number-button {
        display: inline-block;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        width: calc((100%/3) - 5px);
        height: 100px;
        margin-left: 5px;
        margin-bottom: 5px;
        float: right;
    }
</style>

<div class="number-pad">
    <button type="button" class="btn btn-default number-button" data-num="3"></button>
    <button type="button" class="btn btn-default number-button" data-num="2"></button>
    <button type="button" class="btn btn-default number-button" data-num="1"></button>
    <button type="button" class="btn btn-default number-button" data-num="6"></button>
    <button type="button" class="btn btn-default number-button" data-num="5"></button>
    <button type="button" class="btn btn-default number-button" data-num="4"></button>
    <button type="button" class="btn btn-default number-button" data-num="9"></button>
    <button type="button" class="btn btn-default number-button" data-num="8"></button>
    <button type="button" class="btn btn-default number-button" data-num="7"></button>
    <button type="button" class="btn btn-default number-button" data-num="DEL"></button>
    <button type="button" class="btn btn-default number-button" data-num="0"></button>
    <button type="button" class="btn btn-default number-button" data-num="."></button>
</div>

<?= Html::submitButton(
    Yii::t('user', 'Sign in'),
    ['class' => 'btn btn-primary btn-block', 'tabindex' => '3']
) ?>

<?php ActiveForm::end(); ?>
