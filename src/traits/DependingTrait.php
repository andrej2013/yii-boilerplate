<?php
/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 11/10/2016
 * Time: 9:42 AM
 */

namespace andrej2013\yiiboilerplate\traits;

use Yii;
use yii\helpers\Json;

trait DependingTrait
{
    public function actionDepend($on, $onRelation)
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];
                if ($cat_id != null) {
                    $out = self::getSubList($cat_id, $on, $onRelation);
                    echo Json::encode(['output' => $out]);
                    return;
                }
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    public static function getSubList($cat_id, $on, $onRelation)
    {
        $model = 'app\models\\' . $on;
        $results = $model::findOne($cat_id);
        $out = [];
        foreach ($results->$onRelation as $result) {
            $out[] = [
                'id' => $result->id,
                'name' => $result->toString,
            ];
        }
        return $out;
    }

    /**
     * @param $relation
     */
    public function actionDepending($relation)
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $id = $parents[0];
                if ($id != null) {
                    $out = $this->getDependingList($id, $relation);
                    echo Json::encode(['output' => $out]);
                    return;
                }
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    /**
     * @param $id
     * @param $relation
     * @return array
     */
    protected function getDependingList($id, $relation)
    {
        $model = $this->model;
        $model = $model::findOne($id);
        $out = [];
        foreach ($model->$relation as $result) {
            $out[] = [
                'id' => $result->id,
                'name' => $result->toString,
            ];
        }
        return $out;
    }
}