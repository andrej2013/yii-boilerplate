<?php

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Faq');
$this->params['breadcrumbs'][] = $this->title;

$formatter = \Yii::$app->formatter;
?>
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <?= \andrej2013\yiiboilerplate\modules\faq\widgets\FaqWidget::widget() ?>
    </div>

    <!-- /.row -->
</section>
