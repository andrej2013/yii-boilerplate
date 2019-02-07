<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace andrej2013\yiiboilerplate\rest\actions;

use andrej2013\yiiboilerplate\helpers\DebugHelper;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Transaction;
use yii\rest\CreateAction as Action;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

/**
 * Class BatchAction
 * @package andrej2013\yiiboilerplate\rest\actions
 */
class BatchAction extends Action
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var string
     */
    protected $mappingId = '_id';

    /**
     * Lookup column for checking isUpdated
     * @var array
     */
    protected $lookupId = [];

    /**
     * Create or update several items at the same time.
     * @return array
     */
    public function run()
    {
        $returnModels = [];
        $this->mappingId = Yii::$app->getRequest()->get('mapping_id', '_id');
        $lookup = Yii::$app->getRequest()->get('lookup_id', null);
        if (!empty($lookup)) {
            $this->lookupId = explode(',', $lookup);
        }
        // Loop through the array
        $data = Yii::$app->getRequest()->getBodyParams();

        $transaction = Yii::$app->db->beginTransaction(
            Transaction::SERIALIZABLE
        );
        foreach ($data as $item) {
            // Check if it's an update, if we have all primary keys
            if ($model = $this->isUpdate($item)) {
                $returnModel = $this->update($model, $item);
            } else {
                $returnModel = $this->create($item);
            }
            $returnModelArray = $returnModel->toArray();
            // Add back the _id from our frontend apps
            if ($item[$this->mappingId] !== null) {
                $returnModelArray[$this->mappingId] = $item[$this->mappingId];
            } else {
                $returnModelArray[$this->mappingId] = is_array($returnModel->primaryKey) ?
                    implode('_', $returnModel->primaryKey) : $returnModel->primaryKey;
            }
            $returnModels[] = $returnModelArray;
        }
        // If there are no errors that mean that all models are saved so commit
        if (empty($this->errors)) {
            $transaction->commit();
        } else {
            // Otherwise rollback transaction and return array of errors
            $transaction->rollBack();
            return json_encode($this->errors);
        }
        return json_encode($returnModels);
    }

    /**
     * Check if a model can be updated
     * @param $item
     * @return bool|model
     */
    protected function isUpdate($item)
    {
        /**
         * @var ActiveRecord $modelClass
         */
        $modelClass = $this->modelClass;
        $keys = $modelClass::primaryKey();

        if (count($keys) > 1) {
            $values = [];
            foreach ($keys as $key) {
                $values[] = $item[$key];
            }
            if (count($keys) === count($values)) {
                $model = $modelClass::findOne(array_combine($keys, $values));
            }
        } elseif (!empty($item[$keys[0]])) {
            $model = $modelClass::findOne($item[$keys[0]]);
        }

        // Lookup id provided to the request
        if (empty($model) && !empty($this->lookupId)) {
            $lookup = [];
            foreach ($this->lookupId as $column) {
                $lookup[$column] = $item[$column];
            }
            $model = $modelClass::findOne($lookup);
        }

        if (isset($model)) {
            return $model;
        }
        return false;
    }

    /**
     * Update a model with new data
     * @param ActiveRecord $model
     * @param $item
     * @return ActiveRecord
     */
    protected function update($model, $item)
    {
        $model->scenario = Model::SCENARIO_DEFAULT;
        $model->load($item, '');
        $mappingId = $this->mappingId;

        // Check if we are deleting the item from the frontend. Do this way instead of calling the /delete api directly,
        // since this way is easy if we are supporting offline mode.
        if (!empty($item['deleted_at']) && $item['deleted_at'] != '0000-00-00 00:00:00' and empty($item['deleted_by'])) {
            try {
                $model->delete();
            } catch (\Exception $e) {
                if (property_exists($model, $mappingId)) {
                    $mapId = $model->$mappingId;
                } else {
                    $mapId = is_array($model->primaryKey) ? implode('_', $model->primaryKey) : $model->primaryKey;
                }
                $this->errors[$mapId]['errors'] = $e->getMessage();
            }
        } elseif ($model->save() === false) {
            if (property_exists($model, $mappingId)) {
                $mapId = $model->$mappingId;
            } else {
                $mapId = is_array($model->primaryKey) ? implode('_', $model->primaryKey) : $model->primaryKey;
            }
            $this->errors[$mapId]['errors'] = $model->errors;
        }

        return $model;
    }

    /**
     * Create a new model
     * @param $data
     * @return \yii\db\ActiveRecord
     */
    protected function create($data)
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass([
            'scenario' => Model::SCENARIO_DEFAULT,
        ]);

        if (!empty($data[$model->primaryKey])) {
            unset($data[$model->primaryKey]);
        }

        $model->load($data, '');

        if ($model->save() === false) {
            $mappingId = $this->mappingId;
            if (property_exists($model, $mappingId)) {
                $mapId = $model->$mappingId;
            } elseif (!empty($data[$mappingId])) {
                $mapId = $data[$mappingId];
            } else {
                $mapId = is_array($model->primaryKey) ? implode('_', $model->primaryKey) : $model->primaryKey;
            }
            if ($mapId !== null) {
                $this->errors[$mapId]['errors'] = $model->errors;
            } else {
                $this->errors['new'][]['errors'] = $model->errors;
            }
        }
        return $model;
    }
}
