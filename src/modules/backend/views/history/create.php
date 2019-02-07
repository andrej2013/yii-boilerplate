<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * /srv/www/nassi-v2/src/../runtime/giiant/550b5d6e91bda0f79d4dbde409c53f05
 *
 * @package default
 */


use yii\helpers\Html;

/**
 *
 * @var yii\web\View                              $this
 * @var \andrej2013\yiiboilerplate\models\ArHistory $model
 * @var string                                    $relatedTypeForm
 */
$this->title = Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ar History'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-default">
    <div
        class="giiant-crud box-body address-create">

        <div class="clearfix crud-navigation">
            <div class="pull-left">
                <?php echo Html::a(
                    Yii::t('app', 'Cancel'),
                    \yii\helpers\Url::previous(),
                    [
                        'class' => 'btn btn-default'
                    ]
                ) ?>
            </div>
        </div>

        <?php echo $this->render('_form', [
            'model' => $model,
            'inlineForm' => $inlineForm,
            'action' => $action,
            'relatedTypeForm' => $relatedTypeForm,
        ]); ?>

    </div>
</div>
