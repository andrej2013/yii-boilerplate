<?php

namespace andrej2013\yiiboilerplate\rest\actions;

use Yii;
use yii\rest\UpdateAction as Action;

class UpdateAction extends Action
{
    /**
     * Update a model, including file uploades
     *
     * @param $id
     * @return mixed
     */
    public function run($id)
    {
        $model = parent::run($id);

        var_dump($_FILES);
        die("ok");

        if (!empty($_FILES)) {
            $attribute = Yii::$app->getRequest()->get('fileAttribute');
            $model->uploadFile(
                $attribute,
                $_FILES['file']['tmp_name'],
                $_FILES['file']['name']
            );
        }

        return $model;
    }
}
