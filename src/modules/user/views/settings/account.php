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
 * @var dektrium\user\models\SettingsForm $model
 */

$this->title = Yii::t('app', 'Account settings');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="active tab-pane">
    <?php $form = ActiveForm::begin([
        'id'                     => 'account-form',
        'options'                => ['class' => 'form-horizontal'],
        'fieldConfig'            => [
            'template'     => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
            'labelOptions' => ['class' => 'col-lg-3 control-label'],
        ],
        'enableAjaxValidation'   => true,
        'enableClientValidation' => false,
    ]); ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'new_password')
             ->passwordInput() ?>

    <hr/>

    <?= $form->field($model, 'current_password')
             ->passwordInput() ?>

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-9">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-block', 'type' => Html::PRESET_PRIMARY]) ?><br>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <?php if ($model->module->enableAccountDelete) { ?>
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('app', 'Delete account') ?></h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
                <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="">
                <p>
                    <?= Yii::t('app', 'Once you delete your account, there is no going back') ?>.
                    <?= Yii::t('app', 'It will be deleted forever') ?>.
                    <?= Yii::t('app', 'Please be certain') ?>.
                </p>
                <?= Html::a(Yii::t('app', 'Delete account'), ['delete'], [
                    'class'        => 'btn',
                    'type'         => Html::PRESET_DANGER,
                    'data-method'  => 'post',
                    'data-confirm' => Yii::t('app', 'Are you sure? There is no going back'),
                ]) ?>
            </div>
            <!-- /.box-body -->
        </div>
    <?php } ?>
</div>