<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<div class="site-error container">


    <div class="row">
        <div class="alert alert-danger">
            <?= nl2br(Html::encode($message)) ?>
        </div>
        
        <div class="col-md-12 box">
            <h1><?= Html::encode($this->title) ?></h1>
            <?php if (YII_DEBUG) : ?>
            <p>
                <?php andrej2013\yiiboilerplate\helpers\DebugHelper::pre($exception->getTraceAsString())?>
            </p>
            <?php endif; ?>
            <p>
                The above error occurred while the Web server was processing your request.
            </p>
            <p>
                Please contact us if you think this is a server error. Thank you.
            </p>
        </div>
    </div>



</div>
