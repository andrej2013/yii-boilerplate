<?php

namespace andrej2013\yiiboilerplate\modules\v1;


class Module extends \yii\base\Module
{
    public $controllerNamespace = 'andrej2013\yiiboilerplate\modules\v1\controllers';

    // Token Expire-Time for Confirm in Seconds
    public $confirmWithin = 60*60*24;
    // Token Expire-Time for Recover in Seconds
    public $recoverWithin = 60*60*24;

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}