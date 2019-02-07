<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace andrej2013\yiiboilerplate\rest\actions;

use Yii;
use yii\rest\CreateAction as Action;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

/**
 * CreateAction implements the API endpoint for creating a new model from the given data.
 */
class CreateAction extends Action
{
    /**
     * Creates a new model.
     * @return \yii\db\ActiveRecordInterface the model newly created
     * @throws ServerErrorHttpException if there is any error when creating the model
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass([
            'scenario' => $this->scenario,
        ]);

        // Unset the primary key in case it was sent with the request.
        $data = Yii::$app->getRequest()->getBodyParams();
        if (!empty($data[$model->primaryKey])) {
            unset($data[$model->primaryKey]);
        }

        $model->load($data, '');
        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(200);

            // Don't do more. Cors doesn't allow a redirect to the created resource.
            return json_encode($model->toArray());
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        } else {
            // Errors in the model. Catch them and die
            throw new ServerErrorHttpException('Failed to create the object for the following reasons: ' .
                print_r($model->getErrors(), true));
        }

        return json_encode($model->toArray());
    }
}
