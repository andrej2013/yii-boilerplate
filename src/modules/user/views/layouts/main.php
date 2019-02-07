<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

use dmstr\widgets\Alert;
use yii\helpers\Html;
use dektrium\user\widgets\Connect;

/* @var $this \yii\web\View */
/* @var $content string */
$this->title = $this->title . ' [Login]';
andrej2013\yiiboilerplate\assets\TwAsset::register($this);
andrej2013\yiiboilerplate\modules\user\assets\LoginAssets::register($this);
//if (class_exists('\app\modules\backend\assets\AdminAsset')) {
//    \app\modules\backend\assets\AdminAsset::register($this);
//}


$this->render('@andrej2013-boilerplate/views/blocks/raven');?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html class="login">
<head>
    <meta charset="UTF-8">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Theme style -->
    <?php $this->head() ?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>

<body class="hold-transition <?= isset(Yii::$app->params['adminSkin']) ? Yii::$app->params['adminSkin'] : 'skin-black' ?> login background-image">
<?php $this->beginBody() ?>
<div class="wrapper login">
    <!-- Main content -->
    <section class="content login">
        <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
            <div class="login-logo logo-image">

            </div>
        </div>
        <div class="login-wrapper col-xs-12">
            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
                <?= $content ?>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- ./wrapper -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
