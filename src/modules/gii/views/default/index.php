<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $generators \yii\gii\Generator[] */
/* @var $content string */

$generators = Yii::$app->controller->module->generators;
$this->title = 'Welcome to Gii';
?>
<div class="box box-solid">
    <div class="box-header with-border">
        <h1>Welcome to Gii
            <small>a magical tool that can write code for you</small>
        </h1>
    </div>
    <div class="box-body">
        <p class="lead">Start the fun with the following code generators:</p>

        <div class="row">
            <?php foreach ($generators as $id => $generator): ?>
                <div class="generator col-lg-4">
                    <h3><?= Html::encode($generator->getName()) ?></h3>
                    <p><?= $generator->getDescription() ?></p>
                    <p><?= Html::a('Start &raquo;', ['default/view', 'id' => $id], ['class' => 'btn btn-default']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>