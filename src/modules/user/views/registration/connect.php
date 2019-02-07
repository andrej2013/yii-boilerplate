<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $content string */
$this->title = $this->title . ' [' . Yii::t('app', 'Login') . ']';

$this->context->layout = '@andrej2013-backend-views/themes/adminlte/layouts/main-login';
?>
<div class="login-box">
    <div class="login-logo logo-image">

    </div>
    <div class="alert alert-info">
        <p>
            <?= Yii::t('app', 'In order to finish your registration, we need you to enter following fields') ?>:
        </p>
    </div>
    <?php
    echo \dmstr\widgets\Alert::widget();
    ?>
    <div class="login-box-body">
        <p class="login-box-msg"><?= Html::encode($this->title) ?></p>

        <?php $form = ActiveForm::begin([
            'id' => 'connect-account-form',
        ]); ?>

        <?= $form->field($model, 'email', [
            'template' => '{label}<div class="form-group has-feedback">{input}
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span></div>{error}',
        ])
                 ->textInput(['type' => 'email']) ?>

        <?= $form->field($model, 'username', [
            'template' => '{label}<div class="form-group has-feedback">{input}
            <span class="glyphicon glyphicon-user form-control-feedback"></span></div>{error}',
        ]) ?>

        <?= Html::submitButton(Yii::t('app', 'Continue'), ['class' => 'btn btn-block', 'preset' => Html::PRESET_PRIMARY]) ?>

        <?php ActiveForm::end(); ?>
        <?= Html::a(Yii::t('app', 'If you already registered, sign in and connect this account on settings page'), ['/user/settings/networks']) ?>.

    </div>
    <!-- /.login-box-body -->
</div>
