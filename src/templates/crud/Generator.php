<?php
/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

namespace andrej2013\yiiboilerplate\templates\crud;

use andrej2013\yiiboilerplate\helpers\DebugHelper;
use schmunk42\giiant\helpers\SaveForm;
use Yii;
use yii\base\Exception;
use yii\db\ColumnSchema;
use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use schmunk42\giiant\generators\crud\Generator as SchmunkGenerator;
use yii\db\Schema;
use yii\web\JsExpression;

/**
 * This generator generates an extended version of CRUDs.
 * @author Tobais Munk <schmunk@usrbin.de>
 * @since  1.0
 */
class Generator extends SchmunkGenerator
{
    use ProviderTrait, ModelTrait;

    public $template            = 'adminlte';
    public $enableI18N          = true;
    public $searchModelClass    = 'app\\models\\search\\';
    public $controllerClass     = 'app\\controllers\\';
    public $controllerApiModule = 'v1';
    public $modelClass          = 'app\\models\\';
    public $twoColumnsForm      = false;
    public $baseControllerClass = 'app\\controllers\\CrudController';
    public $selectModal         = false;
    public $tidyOutput          = true;

    public $hidden_attributes = [
        'id',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
    ];

    private $_p = [];


    /**
     * @var null comma separated list of provider classes
     */
    public $providerList = null;

    /**
     * @todo review
     * @var string
     */
    public $actionButtonClass = '\kartik\grid\ActionColumn::class';

    /**
     * @var array relations to be excluded in UI rendering
     */
    public $skipRelations = [];

    /**
     * @var string default view path
     */
    public $viewPath = '@backend/views';

    public $tablePrefix = null;
    public $pathPrefix  = null;
    public $formLayout  = 'horizontal';

    /**
     * @var string translation catalogue
     */
    public $messageCategory = 'app';

    /**
     * @var int maximum number of columns to show in grid
     */
    public $gridMaxColumns = 8;

    public $generateExportButton = true;
    public $generateExtendedSearch = true;
    public $generateGridConfig   = true;

    public $generateCopyButton        = true;
    public $generateViewActionButtons = true;

    public $generateSaveAndNew   = true;
    public $generateSaveAndClose = true;

    /**
     * @return string
     */
    public function getName()
    {
        return 'Giiant CRUD';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'This generator generates an extended version of CRUDs.';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [
                [
                    'twoColumnsForm',
                    'generateExportButton',
                    'generateExtendedSearch',
                    'generateGridConfig',
                    'generateCopyButton',
                    'generateViewActionButtons',
                    'generateSaveAndNew',
                    'generateSaveAndClose',
                ],
                'safe',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'generateExportButton'      => 'Generate Export Button on index page',
            'generateExtendedSearch'    => 'Generate Extended form on index page',
            'generateGridConfig'        => 'Generate Grid config form on index page',
            'generateCopyButton'        => '',
            'generateViewActionButtons' => '',
            'generateSaveAndNew'        => '',
            'generateSaveAndClose'      => '',
        ], SaveForm::hint());
    }

    public function formAttributes()
    {
        return ArrayHelper::merge(parent::formAttributes(), [
            'generateExportButton',
            'generateExtendedSearch',
            'generateGridConfig',
            'generateCopyButton',
            'generateViewActionButtons',
            'generateSaveAndNew',
            'generateSaveAndClose',
        ]);
    }

    /**
     * Generates code for active field
     * @param string  $attribute
     * @param string  $name
     * @param boolean $hidden
     * @param string  $id
     * @return string
     */
    public function generateActiveField($attribute, $name = null, $hidden = false, $id = null, $translate = false, $indentMore = 0)
    {
        $indentMore = str_repeat(' ', 4 * $indentMore);
        $tableSchema = $this->getTableSchema();

        // What is this scenario? No tableschema the column isn't set? Feels like generating a login page of some sort.
        if ($tableSchema === false || ! isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "\$form->field(\$model, '$attribute')->hint(\$model->getAttributeHint('$attribute')->passwordInput()";
            } else {
                return "\$form->field(\$model, '$attribute')->hint(\$model->getAttributeHint('$attribute'))";
            }
        }

        $model = "\$model";
        $selector = [
            'selectors' => [
                'input' => "'#'.Html::getInputId(\$model, '$attribute') . \$caller_id",
            ],
        ];
        $selector = $this->var_export54($selector);
        $options = [];
        $options += [
            'id' => "Html::getInputId(\$model, '$attribute') . \$caller_id",
        ];
        $column = $tableSchema->columns[$attribute];
        $comment = $this->extractComments($column);
        $nameid = trim(rtrim(trim($name) . "\n" . str_repeat(' ', 28) . $id, ','));
        if ($hidden) {
            $options = $this->var_export54($options);
            $html = <<<HTML
HTML::activeHiddenInput(
    $model,
    '$attribute',
    $options
);
HTML;
            return $html;
        } else if ($column->phpType === 'boolean' || $column->dbType === 'tinyint(1)' || substr($column->name, 0, 3) == 'is_' || substr($column->name, 0, 4) == 'has_' || ($comment && $comment->inputtype === 'checkbox')) {
            $html = <<<HTML
\$form->field(
    \$model,
    '$attribute',
    $selector
    )
    ->checkbox([
        $options
    ])
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;

        } else if (($column->type === 'text' && ($comment && $comment->inputtype === 'editor')) || (strpos($column->name, '_html') !== false)) {
            $toolset = 'standard';
            if ($comment && $comment->toolset) {
                $toolset = $comment->toolset;
            }
            return "\$form->field(
$indentMore                $model,
$indentMore                '{$attribute}'$selector
$indentMore            )
$indentMore                     ->widget(
$indentMore                         \\andrej2013\\yiiboilerplate\\widget\\CKEditor::class,
$indentMore                         [
$indentMore                             $nameid
$indentMore                             'editorOptions' => \\mihaildev\\elfinder\\ElFinder::ckeditorOptions(
$indentMore                                 'elfinder-backend',
$indentMore                                 [
$indentMore                                     'height' => 300,
$indentMore                                     'preset' => '$toolset',
$indentMore                                 ]
$indentMore                             ),
$indentMore                         ]
$indentMore                     )
$indentMore                    ->hint(" . $model . "->getAttributeHint('$attribute'))";
        } else if (($column->type === 'string' && ($comment && strtolower($comment->inputtype) === 'qrcode'))) {
            $options = $this->var_export54($options);
            $html = <<<HTML
\$form->field(
    \$model,
    '{$attribute}',
    $selector
    )
     ->widget(\\andrej2013\\yiiboilerplate\\widget\\QrInput::class, $options
     )
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;
            return $html;
        } else if (($column->type === 'string' && ($comment && strtolower($comment->inputtype) === 'googlemap'))) {
            $options = $this->var_export54($options);
            $html = <<<HTML
\$form->field(
    \$model,
    '{$attribute}',
    $selector
    )
     ->widget(\\andrej2013\\yiiboilerplate\\widget\\GoogleMapInput::class, $options
     )
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;
            return $html;
            // HTML 5 input fields
        } else if (strpos($column->name, 'email') !== false || ($comment && $comment->inputtype === 'email')) {
            $options += [
                'type'        => "'email'",
                'placeholder' => "\$model->getAttributePlaceholder('$attribute')",
            ];
            $options = $this->var_export54($options);
            $html = <<<HTML
\$form->field(
    \$model,
    '{$attribute}',
    $selector
    )
     ->textInput($options)
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;
            return $html;
        } else if (strpos($column->name, 'telephone') !== false || strpos($column->name, '_tel') !== false || ($comment && $comment->inputtype === 'telephone') || strpos($column->name, 'phone') !== false || ($comment && $comment->inputtype === 'phone')) {
            $options += [
                'type'        => "'tel'",
                'placeholder' => "\$model->getAttributePlaceholder('$attribute')",
            ];
            $options = $this->var_export54($options);
            $html = <<<HTML
\$form->field(
    \$model,
    '{$attribute}',
    $selector
    )
     ->textInput($options)
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;
            return $html;
        } else if (strpos($column->name, 'search') !== false || ($comment && $comment->inputtype === 'search')) {
            $options += [
                'type'        => "'search'",
                'placeholder' => "\$model->getAttributePlaceholder('$attribute')",
            ];
            $options = $this->var_export54($options);
            $html = <<<HTML
\$form->field(
    \$model,
    '{$attribute}',
    $selector
    )
     ->textInput($options)
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;
            return $html;
        } else if (strpos($column->name, 'url') !== false || ($comment && $comment->inputtype === 'url')) {
            $options += [
                'type'        => "'url'",
                'placeholder' => "\$model->getAttributePlaceholder('$attribute')",
            ];
            $options = $this->var_export54($options);
            $html = <<<HTML
\$form->field(
    \$model,
    '{$attribute}',
    $selector
    )
     ->textInput($options)
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;
            return $html;
        } else if ($column->type === 'text' || ($comment && $comment->inputtype === 'text')) {
            $options += [
                'rows'        => 6,
                'placeholder' => "\$model->getAttributePlaceholder('$attribute')",
            ];
            $options = $this->var_export54($options);
            $html = <<<HTML
\$form->field(
    \$model,
    '{$attribute}',
    $selector
    )
     ->textarea($options)
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;
            return $html;
        } else if ($column->dbType === 'date' || ($comment && $comment->inputtype === 'date')) {
            $options += [
                'type'    => '\\kartik\\datecontrol\\DateControl::FORMAT_DATE',
                'options' => [
                    'type'          => '\\kartik\\date\\DatePicker::TYPE_COMPONENT_APPEND',
                    'pluginOptions' => [
                        'todayHighlight' => true,
                        'autoclose'      => true,
                        'class'          => "'form-control'",
                    ],
                ],
            ];
            $options = $this->var_export54($options);
            $html = <<<HTML
\$form->field(
    \$model,
    '{$attribute}',
    $selector
    )->widget(\\kartik\\datecontrol\\DateControl::class, $options)
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;
            return $html;
        } else if ($column->dbType === 'datetime' || ($comment && $comment->inputtype === 'datetime')) {
            $options += [
                'type'    => '\\kartik\\datecontrol\\DateControl::FORMAT_DATETIME',
                'options' => [
                    'type'           => '\\kartik\\datetime\\DateTimePicker::TYPE_COMPONENT_APPEND',
                    'ajaxConversion' => true,
                    'pickerButton'   => [
                        'icon' => 'time',
                    ],
                    'pluginOptions'  => [
                        'todayHighlight' => true,
                        'autoclose'      => true,
                        'class'          => "'form-control'",
                    ],
                ],
            ];
            $options = $this->var_export54($options);
            $html = <<<HTML
\$form->field(
    \$model,
    '{$attribute}',
    $selector
    )->widget(\\kartik\\datecontrol\\DateControl::class, $options)
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;
            return $html;
        } else if ($column->dbType === 'time' || ($comment && $comment->inputtype === 'time')) {
            $options += [
                'pluginOptions' => [
                    'autoclose'   => true,
                    'showSeconds' => true,
                    'class'       => "'form-control'",
                ],
            ];
            $options = $this->var_export54($options);
            $html = <<<HTML
\$form->field(
    \$model,
    '{$attribute}',
    $selector
    )->widget(\\kartik\\time\\TimePicker::class, $options)
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;
            return $html;
        } else if (! empty($column) && $this->checkIfUploaded($column)) {
            $limitation = [];
            if ($comment) {
                if ($comment->fileSize) {
                    $options['maxFileSize'] = $comment->fileSize;
                }
                if ($comment->allowedExtensions && is_array($comment->allowedExtensions)) {
                    $extensions = [];
                    foreach ($comment->allowedExtensions as $extension) {
                        $extensions[] = "'$extension'";
                    }
                    $limitation[] = implode(",\n", $extensions);
                }
            }
            $limitation = implode(",\n", $limitation);
            $options = [
                'pluginOptions' => [
                    'required'               => $column->allowNull ? false : true,
                    'initialPreview'         => "!empty(\$model->$attribute) ? [\$model->getFileUrl('$attribute')] : ''",
                    'initialCaption'         => "\$model->$attribute",
                    'initialPreviewAsData'   => true,
                    'initialPreviewFileType' => "\$model->getFileType('$attribute')",
                    'fileActionSettings'     => [
                        'indicatorNew'      => "\$model->$attribute === null ? '' : Html::a(Html::tag('i', ['class' => 'glyphicon glyphicon-hand-down text-warning']), \$model->getFileUrl('$attribute'), ['target' => '_blank'])",
                        'indicatorNewTitle' => "\\Yii::t('app','Download')",
                    ],
                    'overwriteInitial'       => true,
                ],
                'options'       => $options,
                'pluginEvents'  => [
                    'fileclear' => 'new \yii\web\JsExpression(\'function() { var prev = $("input[name=\\\'" + $(this).attr("name") + "\\\']")[0]; $(prev).val(-1); }\')',
                ],
            ];
            $options = $this->var_export54($options);
            $html = <<<HTML
\$form->field(
    \$model,
    '$attribute',
    $selector
)->widget(\\andrej2013\\yiiboilerplate\\widget\\FileInput::class,
    $options
    )
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;

            return $html;
        } else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name) || ($comment && $comment->inputtype === 'password')) {
                $input = 'passwordInput';
            } else {
                $input = 'textInput';
            }
            if ((is_array($column->enumValues) && count($column->enumValues) > 0) || ($comment && $comment->inputtype === 'enum')) {
                $dropOptions = $indentMore . "[";
                $dropdown_options = [];
                foreach ($column->enumValues as $enumValue) {
                    $dropdown_options[$enumValue] = "Yii::t('app', '" . Inflector::humanize($enumValue) . "')";
                    $dropOptions .= "\n$indentMore" . str_repeat(" ", 32) . "'" . $enumValue . "' => Yii::t('app', '" . Inflector::humanize($enumValue) . "'),";
                }
                $dropOptions .= "\n$indentMore" . str_repeat(" ", 28) . "$indentMore]";
                $options = [
                    'options'       => $options,
                    'data'          => $dropdown_options,
                    'hideSearch'    => true,
                    'pluginOptions' => [
                        'allowClear' => false,
                    ],
                ];
                $options = $this->var_export54($options);
                $html = <<<HTML
\$form->field(
    \$model,
    '$attribute',
    $selector
    )
    ->widget(Select2::class, $options
                    )
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;
                return $html;
            } else if ($column->phpType === 'integer' || $column->phpType === 'double') {
                $step = $column->phpType === 'double' ? 'any' : '1';
                $min = $column->unsigned ? "'min' => '0'," : '';
                $options += [
                    'placeholder' => "\$model->getAttributePlaceholder('$attribute')",
                    'type'        => "'number'",
                    'step'        => $column->phpType === 'double' ? "'any'" : 1,
                    'min'         => $column->unsigned ? 0 : "''",
                ];
                $options = $this->var_export54($options);
                $html = <<<HTML
\$form->field(
    \$model,
    '$attribute',
    $selector
    )
    ->$input($options)
    ->hint(\$model->getAttributeHint('$attribute'))
HTML;
                return $html;
            } else if ($column->phpType !== 'string' || $column->size === null || ($comment && $comment->inputtype === 'string')) {
                $options += [
                    'placeholder' => "\$model->getAttributePlaceholder('$attribute')",
                ];
                $options = $this->var_export54($options);
                $html = <<<HTML
\$form->field(
    \$model,
    '$attribute',
    $selector
    )
    ->$input($options)
    ->hint(\$model->getAttributeHint('$attribute'))
HTML;
                return $html;
            } else {
                $options += [
                    'maxlenght'   => true,
                    'placeholder' => "\$model->getAttributePlaceholder('$attribute')",
                    'input'       => "'#'.Html::getInputId(\$model, '$attribute') . \$caller_id",
                ];
                $options = $this->var_export54($options);
                $html = <<<HTML
\$form->field(
    \$model,
    '$attribute',
    $selector
    )
    ->$input($options)
    ->hint(\$model->getAttributeHint('$attribute'))
HTML;
                return $html;
            }
        }
    }

    /**
     * Specify the API Controller Location in modules/{module}/controllers
     * @inheritdoc
     */
    public function generate()
    {
        if ($this->singularEntities) {
            $this->modelClass = Inflector::singularize($this->modelClass);
            $this->controllerClass = Inflector::singularize(substr($this->controllerClass, 0, strlen($this->controllerClass) - 10)) . 'Controller';
            $this->searchModelClass = Inflector::singularize($this->searchModelClass);
        }

        $controllerFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->controllerClass, '\\')) . '.php');
        $baseControllerFile = StringHelper::dirname($controllerFile) . '/base/' . StringHelper::basename($controllerFile);
        $apiDir = StringHelper::dirname(Yii::getAlias('@' . str_replace('\\', '/', ltrim('\\app\\modules\\' . $this->controllerApiModule . '\\controllers\\', '\\'))));
        $restControllerFile = $apiDir . '/' . StringHelper::basename($controllerFile);

        if (! file_exists($apiDir)) {
            throw new Exception("Rest API Dir does not exist: " . $apiDir);
        }
        $files[] = new CodeFile($baseControllerFile, $this->render('controller.php'));
        $params['controllerClassName'] = \yii\helpers\StringHelper::basename($this->controllerClass);

        if ($this->overwriteControllerClass || ! is_file($controllerFile)) {
            $files[] = new CodeFile($controllerFile, $this->render('controller-extended.php', $params));
        }

        if ($this->overwriteRestControllerClass || ! is_file($restControllerFile)) {
            $files[] = new CodeFile($restControllerFile, $this->render('controller-rest.php', $params));
        }

        if (! empty($this->searchModelClass)) {
            $searchModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->searchModelClass, '\\') . '.php'));
            $files[] = new CodeFile($searchModel, $this->render('search.php'));
        }

        $viewPath = $this->getViewPath();
        $templatePath = $this->getTemplatePath() . '/views';
        foreach (scandir($templatePath) as $file) {
            if (empty($this->searchModelClass) && $file === '_search.php') {
                continue;
            }
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile("$viewPath/$file", $this->render("views/$file"));
            }
        }

        /*$viewPath = $this->getViewPath();
        $templatePath = $this->getTemplatePath() . '/views/base';
        foreach (scandir($templatePath) as $file) {
            if (empty($this->searchModelClass) && $file === '_search.php') {
                continue;
            }
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile("$viewPath/base/$file", $this->render("views/base/$file"));
            }
        }*/

        return $files;
    }

    /**
     * Get Foreign Model-ClassName (with namespace)
     * @param $attribute
     * @return mixed|string
     */
    public function fetchForeignClass($attribute)
    {
        foreach ($this->getModelRelations($this->modelClass) as $modelRelation) {
            foreach ($modelRelation->link as $linkKey => $link) {
                if ($attribute == $link) {
                    return $foreignModelClass = $modelRelation->modelClass;
                }
            }
        }
        return null;
    }

    /**
     * @param $attribute
     * @return int|null|string
     */
    public function fetchForeignAttribute($attribute)
    {
        $foreignModelClass = null;
        foreach ($this->getModelRelations($this->modelClass) as $modelRelation) {
            foreach ($modelRelation->link as $linkKey => $link) {
                if ($attribute == $link) {
                    return $linkKey;
                }
            }
        }
        return null;
    }

    /**
     * @param ColumnSchema $column
     * @param              $comment
     * @return string
     */
    public function activeFieldDepend(ColumnSchema $column, $comment)
    {
        $attribute = $column->name;
        $tableSchema = $this->getTableSchema();

        $fullModel = $this->fetchForeignClass($attribute);
        $foreignPk = $fullModel::primaryKey()[0];
        $reflection = new \ReflectionClass($fullModel);
        $attributeLabel = $this->fetchForeignAttribute($attribute);

        $on = $comment->inputtype->depend->on;
        $onFullModel = $reflection->getNamespaceName() . '\\' . $on;
        $onAttribute = $comment->inputtype->depend->onAttribute;
        $onRelation = $comment->inputtype->depend->onRelation;

        if ($attributeLabel == null) {
            $attributeLabel = $attribute;
        }
        $column = $tableSchema->columns[$attribute];
        $foreignController = $this->getForeignController($fullModel);

        $ajaxRelatedDep = $on . ucfirst(Inflector::singularize($onRelation));
        $ajaxRelatedDepOn = lcfirst($on) . '_id';
        //        $append = !$this->twoColumnsForm ? "<div class=\"col-sm-3\"></div>" : '';
        $append = '';
        $this->selectModal = true;
        return "\$form->field(
                \$model,
                '$attribute'
            )
                    ->widget(
                        DepDrop::class,
                        [
                            'type'=>DepDrop::TYPE_SELECT2,
                            'data'=>($onFullModel::findOne(\$model->$onAttribute)) ? ArrayHelper::map($onFullModel::findOne(\$model->$onAttribute)->$onRelation, '$attributeLabel', 'toString') : [],
                            'options'=>[
                                'id' => '$attribute' . \$owner,
                                'placeholder' => Yii::t('app', 'Select a value...'),
                            ],
                            'select2Options' => [
                                'pluginOptions' => [
                                    'allowClear' => " . ($column->allowNull ? "true" : "false") . ",
                                ],
                            'addon' => (!\$relatedForm || (\$relatedType != RelatedForms::TYPE_MODAL && !\$useModal)) ? [
                                'append' => [
                                    'content' => [
                                        RelatedForms::widget([
                                            'relatedController' => '$foreignController',
                                            'type' => \$relatedTypeForm,
                                            'selector' => '$attribute' . (\$ajax || \$useModal ? '_ajax_' . \$owner : ''),
                                            'primaryKey' => '$foreignPk',
                                            'depend' => true,
                                            'dependOn' => '$onAttribute',
                                            'relation' => '$ajaxRelatedDep',
                                            'relationId' => '$ajaxRelatedDepOn',
                                        ]),
                                    ],
                                    'asButton' => true
                                ],
                            ] : []
                            ],
                            'pluginOptions'=>[
                                'depends' => ['$onAttribute'],
                                'url' => Url::to(['depend', 'on' => '$on', 'onRelation' => '$onRelation']),
                            ]
                        ]
                    )
                ?>
                $append
                <?php";
    }

    /**
     * DropDown-Field Select2 Generator Method
     *
     * Generates code for active field dropdown by using the provider queue
     *
     * @param ColumnSchema $column
     * @param null         $model
     *
     * @return mixed|string
     */
    public function activeFieldDropDown(ColumnSchema $column, $model = null)
    {
        if (($comment = $this->extractComments($column)) && isset($comment->inputtype->depend) && isset($comment->inputtype->depend->on) && isset($comment->inputtype->depend->onAttribute) && isset($comment->inputtype->depend->onRelation)) {
            return $this->activeFieldDepend($column, $comment);
        }
        $attribute = $column->name;
        $tableSchema = $this->getTableSchema();

        $foreignLabelAttribute = "toString";
        $fullModel = $this->fetchForeignClass($attribute);
        if ($fullModel == null) {
            throw new \yii\base\Exception('Relation for attribute ' . $attribute . ' is not defined in database');
        }
        $foreignPk = $fullModel::primaryKey()[0];
        $reflection = new \ReflectionClass($fullModel);
        $shortModel = $reflection->getShortName();
        $attributeLabel = $this->fetchForeignAttribute($attribute);

        if ($attributeLabel == null) {
            $attributeLabel = $attribute;
        }

        $column = $tableSchema->columns[$attribute];
        $foreignController = $this->getForeignController($fullModel);

        $related_form_options = [
            'relatedController' => "'$foreignController'",
            'selector'          => "'$attribute' . (\$is_popup ? '_popup_' . \$caller_id : '')",
            'primaryKey'        => "'$foreignPk'",
            'modelName'         => $this->generateString($shortModel),
        ];
        $related_form_options = $this->var_export54($related_form_options);
        $ajax_options = [
            'url'      => "\\yii\\helpers\\Url::to(['list'])",
            'dataType' => "'json'",
            'data'     => "new \\yii\\web\\JsExpression('function(params) {
                return {
                    q:params.term, m: \"" . $shortModel . "\"
                };
            }')",
        ];
        $ajax_options = $this->var_export54($ajax_options);
        $options = [
            'data'          => "$fullModel::find()->count() > 50 ? null : ArrayHelper::map($fullModel::find()->all(), '$attributeLabel', 'toString')",
            'initValueText' => "$fullModel::find()->count() > 50 ? \\yii\\helpers\\ArrayHelper::map($fullModel::find()->andWhere(['$attributeLabel' => \$model->{$attribute}])->all(), '$attributeLabel', 'toString') : ''",
            'options'       => [
                'placeholder' => $this->generateString('Select a value...'),
                'id'          => "'$attribute' . (\$is_popup ? '_from' . \$caller_id : '')",
            ],
            'pluginOptions' => [
                'allowClear'         => $column->allowNull ? true : false,
                'minimumInputLength' => "$fullModel::find()->count() > 50 ? 3 : false",
                'ajax'               => "$fullModel::find()->count() > 50 ? $ajax_options : null",
            ],
            'is_popup'      => '$is_popup',
            'addon'         => [
                'append' => [
                    'content'  => [
                        "RelatedForms::widget($related_form_options)",
                    ],
                    'asButton' => true,
                ],
            ],
        ];
        $options = $this->var_export54($options);
        $html = <<<HTML
\$form->field(\$model, '$attribute')
        ->widget(Select2::class, $options);
HTML;
        return $html;
    }

    /**
     * Generates code for active field by using the provider queue.
     * @param      $attribute
     * @param null $model
     * @return mixed|string|void
     */
    public function activeField($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code !== null) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);

            return $code;
        } else {
            $column = $this->getColumnByAttribute($attribute);
            if (! $column) {
                return;
            } else {
                return $this->generateActiveField($attribute);
            }
        }
    }

    /**
     * Extract comments from database
     * @param $column
     * @return bool|mixed
     */
    public function extractComments($column)
    {
        $output = json_decode($column->comment);
        if (is_object($output)) {
            return $output;
        }
        return false;
    }

    /**
     * Helper function to get controller name of foreign model for ajax call
     * @param string $model
     * @return string
     */
    protected function getForeignController($model)
    {
        $modelName = $foreignController = substr($model, strrpos($model, '\\') + 1);
        $controllerName = strtolower(Inflector::slug(Inflector::camel2words($modelName), '-'));
        $ns = \yii\helpers\StringHelper::dirname(ltrim($this->controllerClass, '\\'));
        $url = str_replace(['\\app', 'app', '\\controllers'], '', $ns);
        $url = str_replace('\\', '/', $url);
        return $url . '/' . $controllerName;
    }

    /**
     * Generates search conditions
     * @return array
     */
    public function generateSearchConditions()
    {
        $columns = [];
        if (($table = $this->getTableSchema()) === false) {
            $class = $this->modelClass;
            /* @var $model \yii\base\Model */
            $model = new $class();
            foreach ($model->attributes() as $attribute) {
                $columns[$attribute] = 'unknown';
            }
        } else {
            foreach ($table->columns as $column) {
                $columns[$column->name] = $column->type;
            }
        }

        $likeConditions = [];
        $hashConditions = [];
        $dateConditions = [];
        $conditions = [];
        foreach ($columns as $column => $type) {
            switch ($type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                case Schema::TYPE_TIME:
                case Schema::TYPE_TIMESTAMP:
                    $hashConditions[] = "\$this->applyIntegerFilter('{$column}', \$query);";
                    break;
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_DATE:
                    $dateConditions[] = "\$this->applyDateFilter('{$column}', \$query);";
                    break;
                default:
                    $likeConditions[] = "\$this->applyStringFilter('{$column}', \$query);";
                    break;
            }
        }

        if (! empty($hashConditions)) {
            $conditions[] = implode("\n" . str_repeat(' ', 8), $hashConditions);
        }
        if (! empty($likeConditions)) {
            $conditions[] = implode("\n" . str_repeat(' ', 8), $likeConditions);
        }
        if (! empty($dateConditions)) {
            $conditions[] = implode("\n" . str_repeat(' ', 8), $dateConditions);
        }


        return $conditions;
    }

    /**
     * Generates validation rules for the search model.
     * @return array the generated validation rules
     */
    public function generateSearchRules()
    {
        if (($table = $this->getTableSchema()) === false) {
            return ["[['" . implode("', '", $this->getColumnNames()) . "'], 'safe']"];
        }
        $types = [];
        foreach ($table->columns as $column) {
            switch ($column->type) {
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                default:
                    $types['safe'][] = $column->name;
                    break;
            }
        }

        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[
                [
                    '" . implode("',\n" . str_repeat(' ', 20) . "'", $columns) . "'\n" . str_repeat(' ', 16) . "],
                '$type'
            ]";
        }

        return $rules;
    }

    /**
     * List of primary keys
     * @return array
     */
    public function getPrimaryKeys()
    {
        $schema = $this->getTableSchema();
        $primaryKeys = [];
        foreach ($schema->columns as $column) {
            if ($column->isPrimaryKey) {
                $primaryKeys[] = $column->name;
            }
        }

        return $primaryKeys;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getUploadFields($class = null)
    {
        if ($class === null) {
            $class = $this->modelClass;
        }
        /** @var \yii\db\ActiveRecord $model */
        $model = new $class;
        $model->setScenario('crud');
        $safeAttributes = $model->safeAttributes();
        if (empty($safeAttributes)) {
            $safeAttributes = $model->getTableSchema()->columnNames;
        }
        $out = [];
        foreach ($safeAttributes as $attribute) {
            $column = ArrayHelper::getValue($this->getTableSchema()->columns, $attribute);
            if (! empty($column) && $this->checkIfUploaded($column)) {
                $out[] = $attribute;
            }
        }
        return $out;
    }

    /**
     * @param ColumnSchema $column
     * @return bool
     */
    public function checkIfUploaded(ColumnSchema $column)
    {
        $comment = $this->extractComments($column);
        if (preg_match('/(_upload|_file)$/i', $column->name) || ($comment && ($comment->inputtype === 'upload' || $comment->inputtype === 'file'))) {
            return true;
        }
        return false;
    }

    public function var_export54($var, $indent = '')
    {
        switch (gettype($var)) {
            case 'string':
                return $var;
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case 'array':
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    " . ($indexed ? '' : ("'" . $this->var_export54($key) . "'") . ' => ') . $this->var_export54($value, "$indent    ");
                }

                return "[\n" . implode(",\n", $r) . "\n" . $indent . ']';
            case 'boolean':
                return $var ? 'true' : 'false';
            default:
                return var_export($var, true);
        }
    }

}
