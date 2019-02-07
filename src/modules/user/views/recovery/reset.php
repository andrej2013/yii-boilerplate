<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View                      $this
 * @var yii\widgets\ActiveForm            $form
 * @var dektrium\user\models\RecoveryForm $model
 */
$this->context->layout = '@andrej2013-boilerplate/modules/user/views/layouts/main';
$this->title = Yii::t('app', 'Reset your password');
$this->params['breadcrumbs'][] = $this->title;
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
            'id'                     => 'password-recovery-form',
            'enableAjaxValidation'   => true,
            'enableClientValidation' => false,
        ]); ?>
        <?= $form->field($model, 'password', [
            'template' => '{label}<div class="form-group has-feedback">{input}
            <span class="glyphicon glyphicon-lock form-control-feedback"></span></div>{error}',
        ])
                 ->passwordInput() ?>

        <?= Html::submitButton(Yii::t('app', 'Finish'), ['class' => 'btn btn-block', 'preset' => Html::PRESET_PRIMARY]) ?><br>

        <?php ActiveForm::end(); ?>

    </div>
    <!-- /.login-box-body -->
</div>
