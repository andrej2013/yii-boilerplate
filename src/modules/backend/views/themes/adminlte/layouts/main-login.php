<?php

use backend\assets\AppAsset;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

\andrej2013\yiiboilerplate\modules\backend\assets\AdminAsset::register($this);
\andrej2013\yiiboilerplate\modules\user\assets\LoginAssets::register($this);
\andrej2013\yiiboilerplate\assets\LoginAssets::register($this);
\app\assets\LoginAssets::register($this);
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
<body class="login-page hold-transition login background-image">
<?php
?>
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
