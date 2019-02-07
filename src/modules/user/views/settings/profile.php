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
use dektrium\user\helpers\Timezone;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View                 $this
 * @var yii\widgets\ActiveForm       $form
 * @var dektrium\user\models\Profile $model
 */

$this->title = Yii::t('app', 'Profile settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="active tab-pane">

    <?php $form = ActiveForm::begin([
        'id'                     => 'profile-form',
        'options'                => [
            'class'   => 'form-horizontal',
            'enctype' => 'multipart/form-data',
        ],
        'fieldConfig'            => [
            'template'     => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
            'labelOptions' => ['class' => 'col-lg-3 control-label'],
        ],
        'enableAjaxValidation'   => true,
        'enableClientValidation' => false,
        'validateOnBlur'         => false,
    ]); ?>
    <?= $this->render('../shared/_profile', ['model' => $model, 'form' => $form]) ?>

    <?php ActiveForm::end(); ?>
</div>