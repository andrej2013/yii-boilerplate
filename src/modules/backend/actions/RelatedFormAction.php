<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * @link http://www.agence-inspire.com/
 */

namespace andrej2013\yiiboilerplate\modules\backend\actions;

use Yii;
use yii\base\Action;
use yii\web\Response;

/**
 * The ajax new action.
 *
 * Handles form submitting.
 *
 * @author Mehdi Achour <mehdi.achour@agence-inspire.com>
 */
class RelatedFormAction extends Action
{

    /**
     * @var \yii\db\ActiveRecord The model instance
     */
    public $model = null;

    /**
     * @var string The view file holding the form. It must use the $model variable for the model instance
     */
    public $viewFile = '_form';

    /**
     * @var bool
     */
    public $update = false;

    /**
     * @var array of yii\db\ActiveRecord The translations instances
     */
    public $translations = null;

    /**
     * @var array
     */
    public $languageCodes = null;

    /**
     * @var integer
     */
    public $modelId;

    /**
     * @var string
     */
    public $modelTranslationName;

    /**
     * @var string
     */
    public $modelTranslationNamespace;

    /**
     * @var string
     */
    public $modelTranslationAttribute;

    public $depend;

    public $dependOn;

    public $relation;

    public $relationId;

    public $relationIdValue;

    public $inlineForm;

    public $relatedType;

    /**
     *
     */
    public function run()
    {
        $this->controller->layout = false;
        $options = [];
        $model = $this->model;
        // Always use model primary key for fetching
        $pk = $model::primaryKey()[0];

        if ($this->model->load($_POST) && $this->model->save()) {
            if ($this->depend && $this->relation) {
                $reflection = new \ReflectionClass($this->model);
                $ns = $reflection->getNamespaceName();
                $junkModel = $ns . '\\' . $this->relation;
                $reflection = new \ReflectionClass($this->model);
                $modelName = $reflection->getShortName();
                $relationModel = new $junkModel();
                $relationId = $this->relationId;
                $relationModel->$relationId = $this->relationIdValue;
                $foreignKey = strtolower($modelName) . '_id';
                $relationModel->$foreignKey = $this->model->id;
                $relationModel->save();
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'id'    => $this->model->$pk,
                'label' => $this->model->toString,

            ];
        }
        if ($this->depend) {
            $this->model->{$this->relationId} = $this->relationIdValue;
            $options['depend'] = true;
        }
        return $this->controller->renderAjax($this->viewFile, array_merge([
            'model'     => $this->model,
            // Using this to know that call in _form template come from related action ajax call
            'is_popup'  => true,
            // Sending this to _form to know from which select2 call come and based on that always build unique ID
            'caller_id' => Yii::$app->getRequest()
                                    ->get('caller_id'),
        ], $options));
    }

}