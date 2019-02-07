<?php

use dmstr\widgets\Alert;
use yii\helpers\Html;
use dektrium\user\widgets\Connect;

/* @var $this \yii\web\View */
/* @var $content string */
$this->title = $this->title . ' [Login]';
//$this->context->layout = '@andrej2013-backend-views/themes/adminlte/layouts/main-login';
?>

<div class="login-box">
    <div class="login-logo logo-image">

    </div>
    <?php
    echo \dmstr\widgets\Alert::widget();
    ?>
    <div class="login-box-body">
        <p class="login-box-msg"><?php echo Yii::t('app', 'Sign in to start your session'); ?></p>
        <?= $this->render($section, [
            'model'        => $model,
            'module'       => $module,
            'dataProvider' => isset($dataProvider) ? $dataProvider : null,
            'qr'           => $qr,
        ]) ?>

        <div class="social-auth-links text-center">
            <?= Connect::widget([
                'baseAuthUrl' => ['/user/security/auth'],
            ]) ?>
        </div>
        <!-- /.social-auth-links -->
        <?php
        if ($module->enablePasswordRecovery) {
            echo Html::a(Yii::t('app', 'I forgot my password'), ['/user/recovery/request'], ['tabindex' => '5']);
        }
        if ($module->enableConfirmation) {
            echo '<br>';
            echo Html::a(Yii::t('app', 'Didn\'t receive confirmation message?'), ['/user/registration/resend']);
        }
        if ($module->enableRegistration) {
            echo '<br>';
            echo Html::a(Yii::t('app', 'Don\'t have an account? Sign up!'), ['/user/registration/register']);
        }
        ?>
    </div>
    <!-- /.login-box-body -->
</div>
