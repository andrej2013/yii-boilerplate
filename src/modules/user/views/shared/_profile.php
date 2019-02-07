<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 5/18/2017
 * Time: 9:23 AM
 */
?>

<?= $form->field($model, 'name') ?>

<?= $form->field($model, 'public_email') ?>

<?= $form->field($model, 'website') ?>

<?= $form->field($model, 'location')
         ->textArea(['rows' => 5]) ?>

<?php
//echo $form->field($model, 'gravatar_email')->hint(\yii\helpers\Html::a(Yii::t('user', 'Change your avatar at Gravatar.com'), 'http://gravatar.com'))
// ?>

<?= $form->field($model, 'bio')
         ->textarea() ?>
<?php
echo $form->field($model, 'picture', [
    'selectors' => [
        'input' => '#' . \yii\helpers\Html::getInputId($model, 'picture'),
    ],
])
          ->widget(\kartik\file\FileInput::class, [
              'pluginOptions' => [

                  'maxFileSize'            => 8192,
                  'allowedExtensions'      => ['png', 'jpg', 'jpeg', 'pdf', 'txt', 'csv'],
                  'initialPreview'         => [
                      $model->picture === null ? null : $model->getFileUrl('picture'),
                  ],
                  'initialCaption'         => $model->picture,
                  'initialPreviewAsData'   => true,
                  'initialPreviewFileType' => $model->getFileType('picture'),
                  'showUpload'             => false,
                  'fileActionSettings'     => [
                      'indicatorNew'      => $model->picture === null ? '' : '<a href=" ' . $model->getFileUrl('picture') . '" target="_blank"><i class="glyphicon glyphicon-hand-down text-warning"></i></a>',
                      'indicatorNewTitle' => \Yii::t('app', 'Download'),
                  ],
                  'overwriteInitial'       => true,
              ],
              'pluginEvents'  => [
                  'fileclear' => 'function() { var prev = $("input[name=\'" + $(this).attr("name") + "\']")[0]; $(prev).val(-1); }',
              ],
              'options'       => [
                  'id' => \yii\helpers\Html::getInputId($model, 'picture'),
              ],
          ])
?>
<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?= \yii\helpers\Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-block', 'preset' => \yii\helpers\Html::PRESET_PRIMARY]) ?>
    </div>
</div>
