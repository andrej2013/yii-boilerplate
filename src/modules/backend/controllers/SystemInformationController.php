<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Author: Eugine Terentev <eugine@terentev.net>
 */

namespace andrej2013\yiiboilerplate\modules\backend\controllers;

use probe\Factory;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\Response;
use Yii;

class SystemInformationController extends Controller
{
    /**
     * @return float|int
     */
    public function actionIndex()
    {
        $this->layout = "@andrej2013-backend-views/layouts/main";
        $provider = Factory::create();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($key = Yii::$app->request->get('data')) {
                switch ($key) {
                    case 'cpu_usage':
                        return $provider->getCpuUsage();
                        break;
                    case 'memory_usage':
                        return ($provider->getTotalMem() - $provider->getFreeMem()) / $provider->getTotalMem();
                        break;
                }
            }
        } else {
            return $this->render('index', ['provider' => $provider]);
        }
    }
}
