<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 5/23/2017
 * Time: 2:15 PM
 */

namespace andrej2013\yiiboilerplate\widget;

use kartik\daterange\DateRangePicker;
use andrej2013\yiiboilerplate\templates\crud\Generator;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use andrej2013\yiiboilerplate\widget\Modal;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\ColumnSchema;
use yii\db\TableSchema;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;

class FilterWidget extends Widget
{

    /**
     * @var ActiveRecord
     */
    public $model;

    /**
     * @var
     */
    public $pjaxContainer;

    /**
     * @var array
     */
    public $skipAttributes = [
        'id',
        'deleted_at',
        'deleted_by',
        'updated_at',
        'updated_by',
        'created_at',
        'created_by',
    ];

    /**
     * @var
     */
    protected $shortModelName;

    /**
     * @var TableSchema
     */
    protected $schema;

    /**
     * @var Generator
     */
    protected $generator;

    protected $modelRelations;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->model == null || ! ($this->model instanceof ActiveRecord)) {
            throw new InvalidConfigException(Yii::t('app', 'Model property need to be set and must be instance of Active Record object'));
        }
        parent::init(); // TODO: Change the autogenerated stub
        $reflection = new \ReflectionClass($this->model);
        $this->shortModelName = $reflection->getShortName();
        $this->modelRelations = $this->getModelRelations();
    }

    public function run()
    {
        parent::run(); // TODO: Change the autogenerated stub
        Modal::begin([
            'size'         => Modal::SIZE_LARGE,
            'header'       => Html::tag('h4', Yii::t('app', 'Filter')),
            'footer'       => Html::a('<span class="fa fa-ban"></span>&nbsp;' . Yii::t('app', 'Reset'), Url::toRoute([
                    'index',
                    'reset' => 1,
                ]), ['class' => 'btn reset-form', 'preset' => Html::PRESET_DANGER]) . Html::submitButton('<span class="fa fa-search"></span>&nbsp;' . Yii::t('app', 'Search'), [
                    'class' => 'btn pull-right',
                    'preset' => Html::PRESET_PRIMARY,
                    'form'  => 'extended-search',
                ]),
            'id'           => 'filter_popup',
            'toggleButton' => [
                'tag'   => 'button',
                'label' => '<i class="fa fa-search"></i>',
                'class' => 'btn',
                'color' => Html::TYPE_DEFAULT,
            ],
        ]);
        echo Html::beginForm(Url::to(['index']), 'get', [
            'class'     => 'form-default',
            'id'        => 'extended-search',
            'data-pjax' => 1,

        ]);
        echo $this->renderFields();
        echo Html::tag('div', null, ['class' => 'clearfix']);
        echo Html::endForm();
        $this->registerJs();
        Modal::end();
    }

    /**
     * @return string
     */
    protected function renderFields()
    {
        $output = '';
        foreach ($this->model->attributes as $attribute => $value) {
            if (in_array($attribute, $this->skipAttributes)) {
                continue;
            }
            $output .= '<div class="col-sm-6">';
            $output .= '<div class="form-group">';
            $output .= Html::label($this->model->attributeLabels()[$attribute], "{$this->shortModelName}_$attribute");
            $output .= $this->renderField($attribute);
            $output .= '</div>';
            $output .= '</div>';
        }
        return $output;
    }

    protected function renderField($attribute)
    {
        /**
         * @var $column ColumnSchema
         */
        if ($attribute == 'id') {
            return Html::input('text', "{$this->shortModelName}[$attribute]", $this->model->$attribute, [
                'class' => 'form-control',
            ]);
        }
        $column = $this->getColumnByAttribute($attribute);
        foreach ($this->modelRelations as $modelRelation) {
            foreach ($modelRelation->link as $linkKey => $link) {
                if ($attribute == $link) {
                    $foreignModelClass = $modelRelation->modelClass;
                    $ref = new \ReflectionClass($foreignModelClass);
                    $foreignModelClassShort = $ref->getShortName();
                    $foreignAttribute = $this->fetchForeignAttribute($attribute);
                    return Select2::widget([
                        'pjaxContainerId' => $this->pjaxContainer,
                        'data'            => $foreignModelClass::find()
                                                               ->count() > 50
                            ? null
                            : ArrayHelper::map($foreignModelClass::find()
                                                                 ->all(), $foreignAttribute, 'toString'),
                        'initValueText'   => $foreignModelClass::find()
                                                               ->count() > 50 ? ArrayHelper::map($foreignModelClass::find()
                                                                                                                   ->andWhere(['@alias.'.$foreignAttribute => $this->model->$attribute])
                                                                                                                   ->all(), $foreignAttribute, 'toString') : '',
                        'id'              => "{$this->shortModelName}_$attribute",
                        'name'            => "{$this->shortModelName}[$attribute]",
                        'value'           => $this->model->$attribute,
                        'options'         => [
                            'placeholder' => '',
                            'multiple'    => true,
                        ],
                        'pluginOptions'   => [
                            'allowClear'                                                   => true,
                            ($foreignModelClass::find()
                                               ->count() > 50 ? 'minimumInputLength' : '') => 3,
                            ($foreignModelClass::find()
                                               ->count() > 50 ? 'ajax' : '')               => [
                                'url'      => \yii\helpers\Url::to(['list']),
                                'dataType' => 'json',
                                'data'     => new \yii\web\JsExpression('function(params) {
                                        return {
                                            q:params.term, m: \'' . $foreignModelClassShort . '\'
                                        };
                                    }'),
                            ],
                        ],
                    ]);
                }
            }
        }
        if (is_array($column->enumValues) && count($column->enumValues) > 0) {
            foreach ($column->enumValues as $enumValue) {
                $enumOptions[$enumValue] = Inflector::humanize($enumValue);
            }
            return Select2::widget([
                'pjaxContainerId' => $this->pjaxContainer,
                'data'            => $enumOptions,
                'id'              => "{$this->shortModelName}_$attribute",
                'name'            => "{$this->shortModelName}[$attribute]",
                'options'         => [
                    'placeholder' => '',
                ],
                'pluginOptions'   => [
                    'allowClear' => true,
                ],
            ]);
        } else if ($column->phpType === 'boolean' || (strpos($column->name, "is_") === 0) || (strpos($column->name, "has_") === 0) || $column->type == 'tinyint(1)') {
            return Select2::widget([
                'pjaxContainerId' => $this->pjaxContainer,
                'data'            => [
                    0 => Yii::t('app', 'No'),
                    1 => Yii::t('app', 'Yes'),
                ],
                'value'           => $this->model->$attribute,
                'id'              => "{$this->shortModelName}_$attribute",
                'name'            => "{$this->shortModelName}[$attribute]",
                'options'         => [
                    'placeholder' => '',
                ],
                'pluginOptions'   => [
                    'allowClear' => true,
                ],
            ]);
        } else if ($column->dbType == 'date' || $column->dbType == 'datetime') {
            return DateRangePicker::widget([
                'pjaxContainerId' => $this->pjaxContainer,
                'id'              => "{$this->shortModelName}_$attribute",
                'name'            => "{$this->shortModelName}[$attribute]",
                'presetDropdown'  => true,
                'value'           => $this->model->$attribute,
                'pluginEvents'    => [
                    'apply.daterangepicker'  => 'function(ev, picker) {
            if($(this).val() == "") {
                $(this).val(picker.startDate.format(picker.locale.format) + picker.locale.separator +
                picker.endDate.format(picker.locale.format)).trigger("change");
            }
        }',
                    'show.daterangepicker'   => 'function(ev, picker) {
            picker.container.find(".ranges").off("mouseenter.daterangepicker", "li");
            if($(this).val() == "") {
                picker.container.find(".ranges .active").removeClass("active");
            }
        }',
                    'cancel.daterangepicker' => 'function(ev, picker) {
            if($(this).val() != "") {
                $(this).val("").trigger("change");
            }
        }',
                ],
                'pluginOptions'   => [
                    'parentEl' => '#filterModal',
                    'opens'    => 'left',
                    'locale'   => [
                        'format'    => $column->dbType == 'datetime' ? Yii::$app->formatter->momentJsDateTimeFormat : Yii::$app->formatter->momentJsDateFormat,
                        'separator' => ' TO ',
                    ],
                ],
            ]);
        } else {
            return Html::input('text', "{$this->shortModelName}[$attribute]", $this->model->$attribute, [
                'class' => 'form-control',
            ]);
        }
    }

    public function registerJs()
    {
        $js = <<<JS
$(document).on('submit', '#extended-search', function(e){
    $('.modal-backdrop').remove();
});
$(document).on('click', '.reset-form', function(e){
    $('.modal-backdrop').remove();
});
JS;
        $this->view->registerJs($js);
    }

    /**
     * @param $attribute
     * @return ColumnSchema
     * @throws InvalidConfigException
     */
    private function getColumnByAttribute($attribute)
    {
        /**
         * @var $model ActiveRecord
         */
        if (is_string($this->model)) {
            $modelClass = $this->model;
            $model = new $modelClass();
        } else if ($this->model instanceof ActiveRecord) {
            $model = $this->model;
        }
        return $model->getTableSchema()
                     ->getColumn($attribute);
    }

    /**
     * Finds relations of a model class.
     *
     * return values can be filtered by types 'belongs_to', 'many_many', 'has_many', 'has_one', 'pivot'
     *
     * @param array $types
     *
     * @return array
     */
    private function getModelRelations($types = ['belongs_to', 'many_many', 'has_many', 'has_one', 'pivot'])
    {
        $reflector = new \ReflectionClass($this->model);
        $model = $this->model;
        $stack = [];
        foreach ($reflector->getMethods() as $method) {
            // look for getters
            if (substr($method->name, 0, 3) !== 'get') {
                continue;
            }
            if (isset($model->ignoredRelations)) {
                if (in_array($method->name, $model->ignoredRelations)) {
                    continue;
                }
            }
            // skip class specific getters
            $skipMethods = [
                'getRelation',
                'getBehavior',
                'getFirstError',
                'getOperator',
                'getAttribute',
                'getAttributeLabel',
                'getAttributeHint',
                'getOldAttribute',
                'getFileUrl',
                'getFileType',
                'getUploadPath',
                'getHistory',
                'getAttributePlaceholder',
            ];
            if (in_array($method->name, $skipMethods)) {
                continue;
            }
            // check for relation
            try {
                $relation = @call_user_func([$model, $method->name]);
                if ($relation instanceof \yii\db\ActiveQuery) {
                    if ($relation->multiple === false) {
                        $relationType = 'belongs_to';
                    } else if ($this->isPivotRelation($relation)) { # TODO: detecttion
                        $relationType = 'pivot';
                    } else {
                        $relationType = 'has_many';
                    }

                    if (in_array($relationType, $types)) {
                        $name = $this->generateRelationName([$relation], $model->getTableSchema(), substr($method->name, 3), $relation->multiple);
                        $stack[$name] = $relation;
                    }
                }
            } catch (Exception $e) {
                Yii::error('Error: ' . $e->getMessage(), __METHOD__);
            }
        }
        return $stack;
    }

    /**
     * @param $attribute
     * @return int|null|string
     */
    private function fetchForeignAttribute($attribute)
    {
        $foreignModelClass = null;
        foreach ($this->modelRelations as $modelRelation) {
            foreach ($modelRelation->link as $linkKey => $link) {
                if ($attribute == $link) {
                    return $linkKey;
                }
            }
        }
        return null;
    }

    /**
     * @param ActiveQuery $relation
     * @return array|bool
     */
    private function isPivotRelation(ActiveQuery $relation)
    {
        $model = new $relation->modelClass();
        $table = $model->tableSchema;
        $pk = $table->primaryKey;
        if (count($pk) !== 2) {
            return false;
        }
        $fks = [];
        foreach ($table->foreignKeys as $refs) {
            if (count($refs) === 2) {
                if (isset($refs[$pk[0]])) {
                    $fks[$pk[0]] = [$refs[0], $refs[$pk[0]]];
                } else if (isset($refs[$pk[1]])) {
                    $fks[$pk[1]] = [$refs[0], $refs[$pk[1]]];
                }
            }
        }
        if (count($fks) === 2 && $fks[$pk[0]][0] !== $fks[$pk[1]][0]) {
            return $fks;
        } else {
            return false;
        }
    }

    /**
     * @param $relations
     * @param $table
     * @param $key
     * @param $multiple
     * @return string
     */
    private function generateRelationName($relations, $table, $key, $multiple)
    {
        if (! empty($key) && substr_compare($key, 'id', -2, 2, true) === 0 && strcasecmp($key, 'id')) {
            $key = rtrim(substr($key, 0, -2), '_');
        }
        if ($multiple) {
            $key = Inflector::pluralize($key);
        }
        $name = $rawName = Inflector::id2camel($key, '_');
        $i = 0;
        while (isset($table->columns[lcfirst($name)])) {
            $name = $rawName . ($i++);
        }
        while (isset($relations[$table->fullName][$name])) {
            $name = $rawName . ($i++);
        }

        return $name;
    }
}