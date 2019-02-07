<?php

namespace andrej2013\yiiboilerplate\modules\adminer;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'andrej2013\yiiboilerplate\modules\adminer\controllers';

    public function init()
    {
        parent::init();

        $this->defaultRoute = 'adminer';
    }
}
