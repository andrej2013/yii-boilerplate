<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use dektrium\user\models\UserSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View               $this
 * @var ActiveDataProvider $dataProvider
 * @var UserSearch         $searchModel
 */

$this->title = Yii::t('app', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nav-tabs-custom">

    <?= $this->render('/admin/_menu') ?>
    <div class="tab-content">
        <?php Pjax::begin() ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'layout'       => "{items}\n{pager}",
            'columns'      => [
                [
                    'attribute' => 'username',
                    'value'     => function ($model) {
                        return $model->toString();
                    },
                ],
                'email:email',
                [
                    'header' => Yii::t('user', 'Roles'),
                    'value'  => function ($model) {
                        $roles = [];
                        foreach (\Yii::$app->authManager->getRolesByUser($model->id) as $role) {
                            $roles[] = $role->name;
                        }
                        return implode(', ', $roles);
                    },
                ],
                [
                    'attribute' => 'created_at',
                    'value'     => function ($model) {
                        if (extension_loaded('intl')) {
                            return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at]);
                        } else {
                            return date('Y-m-d G:i:s', $model->created_at);
                        }
                    },
                ],
                [
                    'header'  => Yii::t('user', 'Confirmation'),
                    'value'   => function ($model) {
                        if ($model->isConfirmed) {
                            return '<div class="text-center">
                                <span class="text-success">' . Yii::t('user', 'Confirmed') . '</span>
                            </div>';
                        } else {
                            return Html::a(Yii::t('app', 'Confirm'), ['confirm', 'id' => $model->id], [
                                'class'        => 'btn btn-xs btn-success btn-block btn-flat',
                                'data-method'  => 'post',
                                'data-confirm' => Yii::t('app', 'Are you sure you want to confirm this user?'),
                            ]);
                        }
                    },
                    'format'  => 'raw',
                    'visible' => Yii::$app->getModule('user')->enableConfirmation,
                ],
                [
                    'header' => Yii::t('app', 'Block status'),
                    'value'  => function ($model) {
                        if ($model->isBlocked) {
                            return Html::a(Yii::t('app', 'Unblock'), ['block', 'id' => $model->id], [
                                'class'        => 'btn btn-xs btn-success btn-block btn-flat',
                                'data-method'  => 'post',
                                'data-confirm' => Yii::t('app', 'Are you sure you want to unblock this user?'),
                            ]);
                        } else {
                            return Html::a(Yii::t('app', 'Block'), ['block', 'id' => $model->id], [
                                'class'        => 'btn btn-xs btn-danger btn-block btn-flat',
                                'data-method'  => 'post',
                                'data-confirm' => Yii::t('app', 'Are you sure you want to block this user?'),
                            ]);
                        }
                    },
                    'format' => 'raw',
                ],
                [
                    'class'    => 'yii\grid\ActionColumn',
                    'template' => '{update}' . (Yii::$app->user->can('Authority') ? ' {delete}' : null),
                    'buttons'  => YII_ENV == 'test' ? [
                        'delete' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title'      => Yii::t('app', 'Delete'),
                                'aria-label' => Yii::t('app', 'Delete'),
                                'data-pjax'  => '0',
                            ]);
                        },
                    ] : [],
                ],
            ],
        ]); ?>

        <?php Pjax::end() ?>
    </div>
</div>