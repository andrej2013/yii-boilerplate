<?php

/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 */

$this->title = Yii::t('user', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
\andrej2013\yiiboilerplate\modules\user\assets\LoginSelectionAssets::register($this);
?>

<div class="row">
    <div class="col-xs-12">
        <?= \yii\bootstrap\Alert::widget([
            'options' => [
                'class' => 'alert-dismissible alert-danger hidden'
            ],
            'body' => false,
            'id' => 'error-message',
        ]) ?>
    </div>
</div>
<?= $dataProvider ? \yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "{items}",
    'itemView' => function ($model, $key, $index, $widget) {
        return Html::a($model->username, '#', ['class' => 'username-selection', 'data-id' => $key, 'data-url' => (\yii\helpers\Url::current([], true))]);
    }
]) : ''; ?>
