<?php

/*
 * This file is part of the Dektrium project
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View                 $this
 * @var dektrium\user\models\User    $user
 * @var dektrium\user\models\Profile $profile
 */
?>

<?php $this->beginContent('@andrej2013-boilerplate/modules/user/views/admin/update.php', ['user' => $user]) ?>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'wrapper' => 'col-sm-9',
        ],
    ],
    'options' => [
        'enctype' => 'multipart/form-data'
    ],
]); ?>

<?= $this->render('../shared/_profile', ['model' => $profile, 'form' => $form]) ?>


<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
