<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 * Created by PhpStorm.
 * User: Nikola
 * Date: 7/26/2017
 * Time: 8:36 AM
 */

use yii\helpers\Html;

?>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('user', 'Hello') ?>,
</p>

<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('app', 'Dear {0}', $name) ?><br>
    <?= Yii::t('app', 'Click on following link to complete login process') ?>:<br>
    <strong><?= Html::a(Yii::t('app', 'Click'), \andrej2013\yiiboilerplate\modules\user\models\UserAuthCode::getUrl($code)) ?></strong><br><br>
</p>

<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('user', 'If you did not make this request you can ignore this email') ?>.
</p>
