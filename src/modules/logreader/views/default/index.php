<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

use yii\bootstrap\Modal;

$this->title = Yii::t('app', 'Log - File');;
?>
    <h1><?= $this->title ?></h1>
<?php
foreach ($files as $file) {
    echo $this->render('_file', ['model' => $file]);
}

Modal::begin([
    'header' => '<h2>' . Yii::t('app', 'Log content') . '</h2>',
    'id' => 'modal_logreader',
    'size' => Modal::SIZE_LARGE,
    'options' => [
        'style' => 'word-break:break-word;',
    ],
]);

Modal::end();