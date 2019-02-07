<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */
use yii\bootstrap\Html;

$this->title = $model->filename;
?>
<?= Html::beginForm('', 'POST', ['class' => 'form-inline']); ?>
<?= Html::input('text', 'search', Yii::$app->getRequest()->post('search'),['class' => 'form-control']); ?>
<?= Html::submitButton('Search', ['class' => 'btn btn-default']); ?>
<?= Html::endForm(); ?>
    <h1><?= $this->title ?></h1>
<?php
$iterator = 0;
$snipet = $model->getRow($search);
while (!is_null($snipet) && $iterator < 1000) {
    echo $this->render('_log_line', [
        'model' => $snipet,
    ]);
    $snipet = $model->getRow($search);
    $iterator++;
}

\yii\bootstrap\Modal::begin([
    'header' => '<h2>' . Yii::t('app', 'Log content') . '</h2>',
    'id' => 'modal',
    'size' => \yii\bootstrap\Modal::SIZE_LARGE,
    'options' => [
        'style' => 'word-break:break-word;',
    ],
]);

\yii\bootstrap\Modal::end();