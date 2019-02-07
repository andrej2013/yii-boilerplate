<?php
/**
 * Created by PhpStorm.
 * User: ntesic
 * Date: 2/6/19
 * Time: 4:17 PM
 */

namespace andrej2013\yiiboilerplate\modules\backend\controllers;

use andrej2013\yiiboilerplate\modules\backend\models\GridConfig;
use yii\helpers\Url;
use yii\web\Controller;

class GridConfigController extends Controller
{
    
    public function actionIndex()
    {
        $saved = GridConfig::find()->select(['column'])->andWhere(['user_id' => \Yii::$app->user->id, 'grid' => \Yii::$app->request->post('grid'), 'show' => 1])->column();
        $checked = \Yii::$app->request->post('GridConfig', []);
        $saved = array_combine($saved, $saved);
        $to_delete = array_diff_key($saved, $checked);
        foreach ($to_delete as $attribute => $value) {
            $grid_config = GridConfig::findOne(['user_id' => \Yii::$app->user->id, 'grid' => \Yii::$app->request->post('grid'), 'column' => $attribute]);
            $grid_config->show = 0;
            $grid_config->save();
        }
        
        foreach ($checked as $attribute => $value) {
            $grid_config = GridConfig::findOne(['user_id' => \Yii::$app->user->id, 'grid' => \Yii::$app->request->post('grid'), 'column' => $attribute]);
            $grid_config->show = 1;
            $grid_config->save();
        }
        \Yii::$app->response->redirect(Url::previous());
    }
}