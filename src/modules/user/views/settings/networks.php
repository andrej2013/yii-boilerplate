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

/**
 * @var $this yii\web\View
 * @var $form yii\widgets\ActiveForm
 */

$this->title = Yii::t('app', 'Networks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="active tab-pane">
    <div class="alert alert-info">
        <p><?= Yii::t('app', 'You can connect multiple accounts to be able to log in using them') ?>.</p>
    </div>
    <?php $auth = Connect::begin([
        'baseAuthUrl' => ['/user/security/auth'],
        'accounts'    => $user->accounts,
        'autoRender'  => false,
        'popupMode'   => false,
    ]) ?>
    <table class="table">
        <?php foreach ($auth->getClients() as $client): ?>
            <tr>
                <td style="width: 32px; vertical-align: middle">
                    <?= Html::tag('span', '', ['class' => 'auth-icon ' . $client->getName()]) ?>
                </td>
                <td style="vertical-align: middle">
                    <strong><?= $client->getTitle() ?></strong>
                </td>
                <td style="width: 120px">
                    <?= $auth->isConnected($client)
                        ? Html::a(Yii::t('app', 'Disconnect'), $auth->createClientUrl($client), [
                            'class'       => 'btn btn-block',
                            'preset'    => Html::PRESET_DANGER,
                            'data-method' => 'post',
                        ])
                        : Html::a(Yii::t('app', 'Connect'), $auth->createClientUrl($client), [
                            'class' => 'btn btn-block',
                            'preset' => Html::PRESET_PRIMARY,
                        ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php Connect::end() ?>
</div>
