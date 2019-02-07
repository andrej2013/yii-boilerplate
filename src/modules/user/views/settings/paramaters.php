<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 7/7/2017
 * Time: 11:40 AM
 */

/**
 *
 * @var yii\web\View                                                            $this
 * @var \andrej2013\yiiboilerplate\modules\userParamters\models\UserParameterForm $model
 * @var string                                                                  $relatedTypeForm
 */
use yii\helpers\Html;

$this->title = Yii::t('app', 'User Parameters');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-3">
        <?= $this->render('_menu') ?>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::encode($this->title) ?>
            </div>
            <div class="panel-body">
                <?= Html::beginForm('', 'post', [
                    'id' => 'UserParam',
                    'class' => 'form-horizontal',
                ]) ?>

                <?php foreach ($model->attributes as $key => $value) : ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"
                               for="<?= $key ?>"><?= $model->attributesLabels[$key] ?></label>
                        <div class="col-lg-9">
                            <?= $model->generateField($key); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div
                    class="error-summary alert alert-error" <?= empty($model->errors) ? 'style="display:none;"' : '' ?>>
                    <p><?= Yii::t('app', 'Please fix the following errors:') ?></p>
                    <ul>
                        <?php foreach ($model->errors as $error) : ?>
                            <?php if (is_array($error)) :
                                foreach ($error as $e) : ?>
                                    <li><?= $e ?></li>
                                    <?php
                                endforeach;
                            else: ?>
                                <li><?= $error ?></li>
                            <?php endif;
                        endforeach; ?>
                    </ul>
                </div>
                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-9">
                        <?= \yii\helpers\Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-block btn-success']) ?>
                    </div>
                </div>

                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>
