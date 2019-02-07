<?php

namespace andrej2013\yiiboilerplate\modules\backend\models;

use Yii;
use dektrium\rbac\models\Assignment as BaseAssignment;
use yii\helpers\ArrayHelper;

class Assignment extends BaseAssignment
{

    /**
     * Returns all available auth items to be attached to user.
     * @return array
     */
    public function getAvailableItems()
    {
        $type = Yii::$app->user->can('Authority') ? null: 1;
        return ArrayHelper::map($this->manager->getItems($type), 'name', function ($item) {
            return empty($item->description)
                ? $item->name
                : $item->name . ' (' . $item->description . ')';
        });
    }
}