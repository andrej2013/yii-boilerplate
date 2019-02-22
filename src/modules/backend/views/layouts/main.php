<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */


if (Yii::$app->controller->action->id === 'login') {
    /**
     * Do not use this code in your template. Remove it.
     * Instead, use the code  $this->layout = '//main-login'; in your controller.
     */
    echo $this->render('main-login', ['content' => $content]);
} else {
    \andrej2013\yiiboilerplate\modules\backend\assets\FlagIconsAsset::register($this);
    \andrej2013\yiiboilerplate\modules\backend\assets\AdminAsset::register($this);
    if (class_exists(\app\assets\AdminAssset::class)) {
        \app\assets\AdminAssset::register($this);
    }
    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition skin-blue sidebar-mini <?= Yii::$app->params['adminSkin'] ?>">
    <?php $this->beginBody() ?>
    <div class="loader hide"></div>

    <div class="wrapper">

        <?= $this->render('header.php', ['directoryAsset' => $directoryAsset]) ?>
<aside class="main-sidebar">
    <section class="sidebar">
        <?= $this->render('_sidebar.php') ?>
    </section>
</aside>
        
        <?= $this->render('content.php', ['content' => $content, 'directoryAsset' => $directoryAsset]) ?>

    </div>

    <?php $this->endBody() ?>
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>
