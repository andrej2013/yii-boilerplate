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

    const TYPE_SELECT2      = 0;
    const TYPE_DEPEND       = 1;
    const TYPE_UPLOAD       = 2;
    const TYPE_INPUT        = 3;
    const TYPE_GOOGLE_MAP   = 4;
    const TYPE_QR_CODE      = 5;
    const TYPE_CHECKBOX     = 6;
    const TYPE_ENUM         = 7;
    const TYPE_DATE         = 8;
    const TYPE_HTML_EDITOR  = 9;
    const TYPE_TIME         = 10;
    const TYPE_COLOR_PICKER = 11;
    const TYPE_DATETIME     = 12;
    const TYPE_EMAIL        = 13;
    const TYPE_PHONE        = 14;
    const TYPE_URL          = 15;
    const TYPE_NUMBER       = 16;
    const TYPE_HIDDEN       = 17;
    const TYPE_PASSWORD     = 18;
    const TYPE_TEXT         = 19;


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

    public $generateExportButton   = true;
    public $generateExtendedSearch = true;
    public $generateGridConfig     = true;

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
        $tableSchema = $this->getTableSchema();
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

    /**
     * @param \yii\db\ColumnSchema $column
     * @return bool
     */
    public function isUpload(ColumnSchema $column)
    {
        $comment = $this->getComment($column);
        return preg_match('/(_upload|_file)$/i', $column->name) || $comment->type == 'upload' || $comment->type == 'file';
    }

    /**
     * @param        $var
     * @param string $indent
     * @return mixed|string
     */
    public function var_export54($var, $indent = '', $encode_keys = true)
    {
        switch (gettype($var)) {
            case 'string':
                return $var;
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case 'array':
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    " . ($indexed ? '' : (($encode_keys ? "'" : "") . $this->var_export54($key) . ($encode_keys ? "'" : "")) . ' => ') . $this->var_export54($value, "$indent    ");
                }

                return "[\n" . implode(",\n", $r) . "\n" . $indent . ']';
            case 'boolean':
                return $var ? 'true' : 'false';
            default:
                return var_export($var, true);
        }
    }

    /**
     * @param \yii\db\ColumnSchema $column
     * @return mixed|\stdClass
     */
    public function getComment(ColumnSchema $column)
    {
        return json_decode($column->comment) ?? new \stdClass();
    }

    public function getAttributeType($attribute, $model = null)
    {
        if ($attribute instanceof ColumnSchema) {
            $column = $attribute;
            $attribute = $column->name;
        } else {
            $column = $this->getColumnByAttribute($attribute, $model);
        }
        $comment = $this->getComment($column);
        if ($comment->type === 'hidden') {
            return self::TYPE_HIDDEN;
        } else if ($this->isUpload($column)) {
            return self::TYPE_UPLOAD;
        } else if ($column->phpType === 'boolean' || $column->dbType === 'tinyint(1)' || substr($column->name, 0, 3) == 'is_' || substr($column->name, 0, 4) == 'has_' || ($comment->type === 'checkbox')) {
            return self::TYPE_CHECKBOX;
        } else if (is_array($column->enumValues) && count($column->enumValues) > 0) {
            return self::TYPE_ENUM;
        } else if ($column->type === 'string' && strtolower($comment->type) === 'qr_code') {
            return self::TYPE_QR_CODE;
        } else if ($column->type === 'string' && strtolower($comment->type) === 'google_map') {
            return self::TYPE_GOOGLE_MAP;
        } else if ($column->type === 'text' && ($comment->type === 'editor' || (strpos($column->name, '_html') !== false))) {
            return self::TYPE_HTML_EDITOR;
        } else if ($column->dbType === 'date' || $comment->type === 'date') {
            return self::TYPE_DATE;
        } else if ($column->dbType === 'datetime' || $comment->type === 'datetime') {
            return self::TYPE_DATETIME;
        } else if ($column->dbType === 'time' || $comment->type === 'time') {
            return self::TYPE_TIME;
        } else if (class_exists($this->modelClass) && $this->getRelationByColumn($this->modelClass, $column)) {
            if ($comment->type === 'depend') {
                return self::TYPE_DEPEND;
            }
            return self::TYPE_SELECT2;
        } else if ($column->type == 'string' && (strtolower($comment->type) == 'color' || (strpos($column->name, '_color') !== false))) {
            return self::TYPE_COLOR_PICKER;
        } else if ($column->phpType === 'integer' || $column->phpType === 'double') {
            return self::TYPE_NUMBER;
        } else if ($column->type === 'text' || $comment->type === 'text') {
            return self::TYPE_TEXT;
        } else if ($column->type === 'string') {
            if (strpos($column->name, 'email') !== false || $comment->type === 'email') {
                return self::TYPE_EMAIL;
            } else if (strpos($column->name, 'telephone') !== false || strpos($column->name, '_tel') !== false || $comment->type === 'telephone' || strpos($column->name, 'phone') !== false || $comment->type === 'phone') {
                return self::TYPE_PHONE;
            } else if (strpos($column->name, 'url') !== false || $comment->type === 'url') {
                return self::TYPE_URL;
            } else if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name) || $comment->type === 'password') {
                return self::TYPE_PASSWORD;
            } else {
                return self::TYPE_INPUT;
            }
        } else {
            return self::TYPE_INPUT;
        }
    }
}
