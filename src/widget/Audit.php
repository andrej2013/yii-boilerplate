<?php
namespace andrej2013\yiiboilerplate\widget;

use yii\base\Widget;
use bedezign\yii2\audit\web\JSLoggingAsset;

class Audit extends Widget
{
    /**
     *
     */
    public function run()
    {
        JSLoggingAsset::register($this->view);
    }
}
