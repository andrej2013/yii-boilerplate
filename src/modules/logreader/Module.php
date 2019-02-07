<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

namespace andrej2013\yiiboilerplate\modules\logreader;

use Yii;

class Module extends \zabachok\logreader\Module
{

    public $controllerNamespace = 'andrej2013\yiiboilerplate\modules\logreader\controllers';
    public $sources = [
        '@app/../runtime/logs',
    ];

}
