<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

namespace andrej2013\yiiboilerplate\modules\faq\controllers;

use andrej2013\yiiboilerplate\modules\faq\models\Faq;

/**
 * DefaultController implements the CRUD actions for Faq model.
 */
class DefaultController extends \andrej2013\yiiboilerplate\modules\faq\controllers\base\FaqController
{
    public function init()
    {
        parent::init();
        $this->model = Faq::class;
        $this->searchModel = \andrej2013\yiiboilerplate\modules\faq\models\search\Faq::class;
    }
    
}
