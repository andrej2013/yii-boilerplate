<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

namespace andrej2013\yiiboilerplate\modules\faq\controllers;

/**
 * DefaultController implements the CRUD actions for Faq model.
 */
class DefaultController extends \andrej2013\yiiboilerplate\modules\faq\controllers\base\FaqController
{
    public function init()
    {
        parent::init();
        $this->layout = '@andrej2013-backend-views/layouts/main.php';
    }
}
