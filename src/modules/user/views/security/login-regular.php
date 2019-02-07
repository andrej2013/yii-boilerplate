<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use dektrium\user\widgets\Connect;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View                   $this
 * @var dektrium\user\models\LoginForm $model
 * @var dektrium\user\Module           $module
 */

$this->title = Yii::t('user', 'Sign in');

?>
<?php $form = ActiveForm::begin([
    'id'                   => 'login-form',
    'enableAjaxValidation' => true,
    'validateOnBlur'       => false,
    'validateOnType'       => false,
    'validateOnChange'     => false,
]) ?>

<?= $form->field($model, 'login', [
    'template' => '{label}<div class="form-group has-feedback">{input}
            <span class="glyphicon glyphicon-user form-control-feedback"></span></div>{error}',
    'inputOptions' => ['class' => 'form-control', 'tabindex' => '1']
]); ?>
<?= $form->field($model, 'password', [
    'template'     => '{label}<div class="form-group has-feedback">{input}
            <span class="glyphicon glyphicon-lock form-control-feedback"></span></div>{error}',
    'inputOptions' => ['class' => 'form-control', 'tabindex' => '2'],
])
         ->passwordInput();
//         ->label(Yii::t('user', 'Password'))
 ?>

    <div class="row">
        <div class="col-xs-8">
            <?= $form->field($model, 'rememberMe', [
                'template' => '{input}',
            ])
                     ->widget(\bookin\aws\checkbox\AwesomeCheckbox::class, [
                         'type'  => \bookin\aws\checkbox\AwesomeCheckbox::TYPE_CHECKBOX,
                         'style' => \bookin\aws\checkbox\AwesomeCheckbox::STYLE_PRIMARY,
                         'options' => ['tabindex' => '3']
                     ]) ?>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
            <?= Html::submitButton(Yii::t('app', 'Sign in'), ['class' => 'btn btn-block', 'preset' => Html::PRESET_PRIMARY, 'tabindex' => '4']) ?>
        </div>
        <!-- /.col -->
    </div>
<?php
ActiveForm::end();
