<?php

namespace _;

use Yii;

// This is the dashboard sidebar
$adminMenuItems = [];

$adminMenuItems[] = [
    'label' => Yii::t('app', 'Dashboard'),
    'url' => ['/backend'],
    'visible' => !\Yii::$app->user->isGuest,
    'icon' => 'fa fa-dashboard',
];

if (Yii::$app->user->can('Editor')) {
    $adminMenuItems[] = [
        'label' => Yii::t('app', 'Pages'),
        'url' => ['/pages'],
        'icon' => 'fa fa-tree',
    ];
}

if (Yii::$app->user->can('Administrator')) {
    $adminMenuItems[] = [
        'label' => Yii::t('app', 'Users'),
        'url' => ['/user/admin'],
        'icon' => 'fa fa-user',
    ];
}

echo $this->render('_sidebar_admin', ['adminMenuItems' => $adminMenuItems]);
