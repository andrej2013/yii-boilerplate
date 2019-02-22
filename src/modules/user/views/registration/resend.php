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
 * @var yii\web\View $this
 * @var dektrium\user\models\ResendForm $model
 */

$this->title = Yii::t('app', 'Request new confirmation message');
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
            'id'                     => 'registration-form',
            'enableAjaxValidation'   => true,
            'enableClientValidation' => false,
        ]); ?>

        <?= $form->field($model, 'email', [
            'template' => '{label}<div class="form-group has-feedback">{input}
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span></div>{error}',
        ])
                 ->textInput(['autofocus' => true, 'type' => 'email', 'tabindex' => 1]) ?>

        <?= Html::submitButton(Yii::t('app', 'Continue'), ['class' => 'btn btn-block', 'preset' => Html::PRESET_PRIMARY, 'tabindex' => 4]) ?>

        <?php ActiveForm::end(); ?>

        <?= Html::a(Yii::t('app', 'Already a member? Login here'), ['/user/login']) ?>.
    </div>
    <!-- /.login-box-body -->
</div>
