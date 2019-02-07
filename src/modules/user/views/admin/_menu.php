<?php

/*
 * This file is part of the Dektrium project
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\bootstrap\Nav;

?>
<?= Nav::widget([
    'options' => [
        'class' => 'nav-tabs',
        'style' => 'margin-bottom: 15px',
    ],
    'items' => [
        [
            'label'   => Yii::t('app', 'Users'),
            'url'     => ['/user/admin/index'],
            'visible' => Yii::$app->user->can('Administrator'),
        ],
        [
            'label'   => Yii::t('app', 'Roles'),
            'url'     => ['/rbac/role/index'],
            'visible' => isset(Yii::$app->extensions['dektrium/yii2-rbac']) && Yii::$app->user->can('Authority'),
        ],
        [
            'label' => Yii::t('app', 'Permissions'),
            'url'   => ['/rbac/permission/index'],
            'visible' => isset(Yii::$app->extensions['dektrium/yii2-rbac']) && Yii::$app->user->can('Authority'),
        ],
        [
            'label' => Yii::t('app', 'Create'),
            'visible' => Yii::$app->user->can('Authority'),
            'items' => [
                [
                    'label'   => Yii::t('app', 'New user'),
                    'url'     => ['/user/admin/create'],
                ],
                [
                    'label' => Yii::t('app', 'New role'),
                    'url'   => ['/rbac/role/create'],
                    'visible' => isset(Yii::$app->extensions['dektrium/yii2-rbac']),
                ],
                [
                    'label' => Yii::t('app', 'New permission'),
                    'url'   => ['/rbac/permission/create'],
                    'visible' => isset(Yii::$app->extensions['dektrium/yii2-rbac']),
                ],
            ],
        ],
    ],
]) ?>
