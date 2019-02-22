<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use dektrium\user\models\User;
use yii\bootstrap\Nav;
use yii\web\View;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var View   $this
 * @var User   $user
 * @var string $content
 */

$this->title = Yii::t('app', 'Update user account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="nav-tabs-custom">
    <?= $this->render('_menu') ?>
    <div class="tab-content">
        <div class="row">
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?= Nav::widget([
                            'options' => [
                                'class' => 'nav-pills nav-stacked',
                            ],
                            'items'   => [
                                [
                                    'label' => Yii::t('app', 'Account details'),
                                    'url'   => ['/user/admin/create'],
                                ],
                                [
                                    'label'   => Yii::t('app', 'Profile details'),
                                    'url'     => ['/user/admin/update-profile', 'id' => $user->id],
                                    'visible' => Yii::$app->user->can('Authority'),
                                    'options' => [
                                        'class'   => 'disabled',
                                        'onclick' => 'return false;',
                                    ],
                                ],
                                [
                                    'label'   => Yii::t('app', 'Information'),
                                    'url'     => ['/user/admin/info', 'id' => $user->id],
                                    'visible' => Yii::$app->user->can('Authority'),
                                    'options' => [
                                        'class'   => 'disabled',
                                        'onclick' => 'return false;',
                                    ],
                                ],
                                [
                                    'label'   => Yii::t('app', 'Assignments'),
                                    'url'     => ['/user/admin/assignments', 'id' => $user->id],
                                    'visible' => isset(Yii::$app->extensions['dektrium/yii2-rbac']) && Yii::$app->user->can('Administrator'),
                                    'options' => [
                                        'class'   => 'disabled',
                                        'onclick' => 'return false;',
                                    ],
                                ],
                                '<hr>',
                                [
                                    'label'       => Yii::t('app', 'Confirm'),
                                    'url'         => ['/user/admin/confirm', 'id' => $user->id],
                                    'visible'     => ! $user->isConfirmed && Yii::$app->user->can('Authority'),
                                    'linkOptions' => [
                                        'class'        => 'text-success',
                                        'data-method'  => 'post',
                                        'data-confirm' => Yii::t('app', 'Are you sure you want to confirm this user?'),
                                    ],
                                    'options'     => [
                                        'class'   => 'disabled',
                                        'onclick' => 'return false;',
                                    ],
                                ],
                                [
                                    'label'       => Yii::t('app', 'Block'),
                                    'url'         => ['/user/admin/block', 'id' => $user->id],
                                    'visible'     => ! $user->isBlocked && Yii::$app->user->can('Authority'),
                                    'linkOptions' => [
                                        'class'        => 'text-danger',
                                        'data-method'  => 'post',
                                        'data-confirm' => Yii::t('app', 'Are you sure you want to block this user?'),
                                    ],
                                    'options'     => [
                                        'class'   => 'disabled',
                                        'onclick' => 'return false;',
                                    ],
                                ],
                                [
                                    'label'       => Yii::t('app', 'Unblock'),
                                    'url'         => ['/user/admin/block', 'id' => $user->id],
                                    'visible'     => $user->isBlocked && Yii::$app->user->can('Authority'),
                                    'linkOptions' => [
                                        'class'        => 'text-success',
                                        'data-method'  => 'post',
                                        'data-confirm' => Yii::t('app', 'Are you sure you want to unblock this user?'),
                                    ],
                                    'options'     => [
                                        'class'   => 'disabled',
                                        'onclick' => 'return false;',
                                    ],
                                ],
                                [
                                    'label'       => Yii::t('app', 'Delete'),
                                    'url'         => ['/user/admin/delete', 'id' => $user->id],
                                    'visible'     => Yii::$app->user->can('Authority'),
                                    'linkOptions' => [
                                        'class'        => 'text-danger',
                                        'data-method'  => 'post',
                                        'data-confirm' => Yii::t('app', 'Are you sure you want to delete this user?'),
                                    ],
                                    'options'     => [
                                        'class'   => 'disabled',
                                        'onclick' => 'return false;',
                                    ],
                                ],
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="alert alert-info">
                            <?= Yii::t('app', 'Credentials will be sent to the user by email') ?>.
                            <?= Yii::t('app', 'A password will be generated automatically if not provided') ?>.
                        </div>
                        <?php $form = ActiveForm::begin([
                            'layout'                 => 'horizontal',
                            'enableAjaxValidation'   => true,
                            'enableClientValidation' => false,
                            'fieldConfig'            => [
                                'horizontalCssClasses' => [
                                    'wrapper' => 'col-sm-9',
                                ],
                            ],
                        ]); ?>

                        <?= $this->render('_user', ['form' => $form, 'user' => $user]) ?>

                        <div class="form-group">
                            <div class="col-lg-offset-3 col-lg-9">
                                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-block', 'preset' => Html::PRESET_PRIMARY]) ?>
                            </div>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>