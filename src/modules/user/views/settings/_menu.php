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
use yii\widgets\Menu;

/**
 * @var dektrium\user\models\User $user
 */

$user = Yii::$app->user->identity;
$networksVisible = count(Yii::$app->authClientCollection->clients) > 0;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?php
//            echo \andrej2013\yiiboilerplate\widget\Gravatar::widget(
//                [
//                    'email' => !isset(\Yii::$app->user->identity->profile->gravatar_email) || (\Yii::$app->user->identity->profile->gravatar_email === null)
//                        ? \Yii::$app->user->identity->email
//                        : \Yii::$app->user->identity->profile->gravatar_email,
//                    'options' => [
//                        'alt' => \Yii::$app->user->identity->toString,
//                        'style' => 'float:left;'
//                    ],
//                    'size' => 64,
//                    'width' => 24,
//                    'height' => 24,
//                    'image' => Yii::$app->user->identity->getProfilePicture(['width' => 128, 'height' => 128]),
//                ]
//            );
            ?>
            <?= $user->username ?>
        </h3>
    </div>
    <div class="panel-body">
        <?= Menu::widget([
            'options' => [
                'class' => 'nav nav-pills nav-stacked',
            ],
            'items' => [
                ['label' => Yii::t('app', 'Profile'), 'url' => ['/user/settings/profile']],
                ['label' => Yii::t('app', 'Account'), 'url' => ['/user/settings/account']],
                [
                    'label' => Yii::t('app', 'User Parameters'),
                    'url' => ['/user/settings/parameters'],
                    'visible' => Yii::$app->hasModule('user-parameter'),
                ],
                [
                    'label' => Yii::t('app', 'Networks'),
                    'url' => ['/user/settings/networks'],
                    'visible' => $networksVisible
                ],
            ],
        ]) ?>
    </div>
</div>
