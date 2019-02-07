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
 * @var yii\web\View                                            $this
 * @var \andrej2013\yiiboilerplate\modules\user\models\User       $user
 * @var \andrej2013\yiiboilerplate\modules\user\models\UserTwData $model
 */
?>

<?php $this->beginContent('@andrej2013-boilerplate/modules/user/views/admin/update.php', ['user' => $user]) ?>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'wrapper' => 'col-sm-9',
        ],
    ],
]); ?>
<?php
$skippedAttributes = [
    'created_at',
    'created_by',
    'updated_at',
    'updated_by',
    'deleted_at',
    'deleted_by',
    'id',
    'user_id',
];
$attributes = $model->getAttributes();
?>
<?php foreach ($attributes as $attribute => $value) : ?>
    <?= !in_array($attribute, $skippedAttributes) ?
        $form->field($model, $attribute)->{$model->getAttributeType($attribute)}() :
        ''
    ?>
<?php endforeach; ?>

<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?= Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-block btn-success btn-flat']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
