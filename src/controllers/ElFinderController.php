<?php

namespace andrej2013\yiiboilerplate\controllers;

/**
 * Class Controller
 * @package mihaildev\elfinder
 * @property array $options
 */


class ElFinderController extends ElFinderBackendController
{
    /**
     * @return mixed
     */
    public function actionManager()
    {
        $this->layout = "@andrej2013-backend-views/layouts/main";
        return $this->render("@andrej2013-views/elfinder/manager.php", ['options' => $this->getManagerOptions()]);
    }
}
