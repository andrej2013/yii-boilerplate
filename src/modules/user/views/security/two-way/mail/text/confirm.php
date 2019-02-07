<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 7/26/2017
 * Time: 8:37 AM
 */

/**
 * @var dektrium\user\models\User
 */
?>

<?= Yii::t('app', 'Dear {0}', $user) ?>, <br>
<?= Yii::t('app', 'Click on following link to complete login process') ?>: <br>
<?= \yii\helpers\Html::a(Yii::t('app', 'Click'), \andrej2013\yiiboilerplate\modules\user\models\UserAuthCode::getUrl($code)) ?><br><br>

<?= Yii::t('user', 'If you did not make this request you can ignore this email') ?>.
