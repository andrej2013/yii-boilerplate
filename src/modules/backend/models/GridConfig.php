<?php

namespace andrej2013\yiiboilerplate\modules\backend\models;

use Yii;
use \andrej2013\yiiboilerplate\modules\backend\models\base\GridConfig as BaseGridConfig;
use yii\base\Exception;

/**
 * This is the model class for table "grid_config".
 */
class GridConfig extends BaseGridConfig
{
    public $columns;
    /**
     * @var \app\models\ActiveRecord
     */
    public $model;
    public $grid_id;

    /**
     * @return array
     */
    public function sliceColumns()
    {
        $exists = self::find()
                      ->andWhere(['user_id' => Yii::$app->user->id, 'grid' => $this->grid_id])
                      ->count();
        $columns = $this->parseColumns();
        if (! $exists) {
            foreach ($columns as $i => $column) {
                $gridConfig = new self();
                $gridConfig->user_id = Yii::$app->user->id;
                $gridConfig->grid = $this->grid_id;
                $gridConfig->column = $column['attribute'];
                $gridConfig->show = 0;
                if ($i < 9) {
                    $gridConfig->show = 1;
                }
                $gridConfig->save(false);
            }
        }
        $sliced = [];
        foreach ($this->columns as $i => $column) {
            if (isset($column['visible']) && $column['visible'] == false) {
                continue;
            }
            $attribute = $this->findAttribute($column);
            if ($attribute == false) {
                $sliced[] = $column;
                continue;
            }
            $gridConfig = self::findOne(['user_id' => Yii::$app->user->id, 'grid' => $this->grid_id, 'column' => $attribute]);
            if ($gridConfig && $gridConfig->show === 0) {
                continue;
            } else if (!$gridConfig) {
                $gridConfig = new self();
                $gridConfig->user_id = Yii::$app->user->id;
                $gridConfig->grid = $this->grid_id;
                $gridConfig->column = $attribute;
                $gridConfig->show = 1;
                $gridConfig->save();
            }
            $sliced[] = $column;
        }
        if (count($sliced) == 2) {
            array_unshift($sliced, 'id');
        }
        return $sliced;
    }

    /**
     * @return array
     */
    public function parseColumns()
    {
        $columns = [];
        foreach ($this->columns as $column) {
            if (isset($column['visible']) && $column['visible'] == false) {
                continue;
            }
            if (isset($column['label']) && isset($column['attribute'])) {
                $columns[] = [
                    'attribute' => $this->findAttribute($column),
                    'label'     => $column['label'],
                ];
            } else if (isset($column['attribute'])) {
                $label = $this->model->getAttributeLabel($column['attribute']);
                $columns[] = [
                    'attribute' => $this->findAttribute($column),
                    'label'     => $label,
                ];
            } else if (is_string($column)) {
                $attribute = $this->findAttribute($column);
                $label = $this->model->getAttributeLabel($attribute);
                $columns[] = [
                    'attribute' => $attribute,
                    'label'     => $label,
                ];
            } else {
                continue;
            }
        }
        return $columns;
    }

    /**
     * @param $column
     * @return bool
     */
    protected function findAttribute($column)
    {
        if (isset($column['attribute'])) {
            return $column['attribute'];
        } else if (is_string($column)) {
            $attributes = explode(':', $column);
            return $attributes[0];
        }
        return false;
    }
}
