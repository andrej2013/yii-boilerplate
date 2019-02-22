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

    public $hide;

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
        try {
            if ($this->model->load($_POST)) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                if ($this->model->save()) {
                    return [
                        'id'    => $this->model->$pk,
                        'label' => $this->model->toString,

                    ];
                } else {
                    Yii::$app->response->statusCode = 500;
                    return $this->display();
                }
            }
            $this->model->load($_GET);
        } catch (\Exception $e) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->statusCode = 500;
            return $this->display();
        }
        return $this->display();
    }

    protected function display()
    {
        return $this->controller->renderAjax($this->viewFile, [
            'model'     => $this->model,
            // Using this to know that call in _form template come from related action ajax call
            'is_popup'  => true,
            // Sending this to _form to know from which select2 call come and based on that always build unique ID
            'caller_id' => Yii::$app->getRequest()
                                    ->get('caller_id'),
            'hide'      => $this->hide,
        ]);
    }

}