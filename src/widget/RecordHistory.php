<?php
/**
 * Copyright (c) 2019. 
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

namespace andrej2013\yiiboilerplate\widget;

use dektrium\user\models\User;
use yii\base\Widget;

class RecordHistory extends Widget
{
    public $model;
    public $updated_by = null;
    public $created_by = null;
    public $deleted_by = null;
    public $updated_at = null;
    public $created_at = null;
    public $deleted_at = null;

    public function init()
    {
        parent::init();
        if ($this->model->created_by != null && is_numeric($this->model->created_by) && $this->model->created_by > 0) {
            $this->created_by = \app\models\User::findOne($this->model->created_by);
        }
        if ($this->model->updated_by != null && is_numeric($this->model->updated_by) && $this->model->updated_by > 0) {
            $this->updated_by = \app\models\User::findOne($this->model->updated_by);
        }
        if ($this->model->deleted_by != null && is_numeric($this->model->deleted_by) && $this->model->deleted_by > 0) {
            $this->deleted_by = \app\models\User::findOne($this->model->deleted_by);
        }
        if ($this->model->created_at != null && is_numeric($this->model->created_at) && $this->model->created_at > 0) {
            $this->created_at = \app\models\User::findOne($this->model->created_at);
        }
        if ($this->model->updated_at != null && is_numeric($this->model->updated_at) && $this->model->updated_at > 0) {
            $this->updated_at = \app\models\User::findOne($this->model->updated_at);
        }
        if ($this->model->deleted_at != null && is_numeric($this->model->deleted_at) && $this->model->deleted_at > 0) {
            $this->deleted_at = \app\models\User::findOne($this->model->deleted_at);
        }
    }

    public function run()
    {
        return $this->render(
            'RecordHistory',
            [
                'model' => $this->model,
                'created_by' => $this->created_by,
                'updated_by' => $this->updated_by,
                'deleted_by' => $this->deleted_by,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'deleted_at' => $this->deleted_at,
            ]
        );
    }
}
