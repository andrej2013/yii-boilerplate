<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $content string */
$this->title = $this->title . ' [' . Yii::t('app', 'Sign up') . ']';
//$this->context->layout = '@andrej2013-backend-views/themes/adminlte/layouts/main-login';
?>

<div class="login-box">
    <div class="login-logo logo-image">

    </div>
    <?php
    echo \dmstr\widgets\Alert::widget();
    ?>
    <div class="login-box-body">
        <p class="login-box-msg"><?= Html::encode($this->title) ?></p>
        <?php $form = ActiveForm::begin([
            'id'                     => 'registration-form',
            'enableAjaxValidation'   => true,
            'enableClientValidation' => false,
        ]); ?>

        <?= $form->field($model, 'email', [
            'template' => '{label}<div class="form-group has-feedback">{input}
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span></div>{error}',
        ])
                 ->textInput(['autofocus' => true, 'type' => 'email', 'tabindex' => 1]) ?>

        <?= $form->field($model, 'username', [
            'template' => '{label}<div class="form-group has-feedback">{input}
            <span class="glyphicon glyphicon-user form-control-feedback"></span></div>{error}',
        ])->textInput(['tabindex' => 2]) ?>

        <?php if ($module->enableGeneratingPassword == false): ?>
            <?= $form->field($model, 'password', [
                'template' => '{label}<div class="form-group has-feedback">{input}
            <span class="glyphicon glyphicon-lock form-control-feedback"></span></div>{error}',
            ])
                     ->passwordInput(['tabindex' => 3]) ?>
        <?php endif ?>

        <?= Html::submitButton(Yii::t('app', 'Sign up'), ['class' => 'btn btn-block', 'preset' => Html::PRESET_PRIMARY, 'tabindex' => 4]) ?>

        <?php ActiveForm::end(); ?>

        <?= Html::a(Yii::t('app', 'Already a member? Login here'), ['/user/login']) ?>.
    </div>
    <!-- /.login-box-body -->
</div>
