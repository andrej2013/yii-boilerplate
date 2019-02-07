<?php
/**
 * @link      http://www.phundament.com
 * @copyright Copyright (c) 2014 herzog kommunikation GmbH
 * @license   http://www.phundament.com/license/
 */

namespace andrej2013\yiiboilerplate\templates\model;

use Yii;
use yii\db\ColumnSchema;
use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\db\Schema;
use schmunk42\giiant\generators\model\Generator as BaseGenerator;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Tobias Munk <schmunk@usrbin.de>
 * @since  0.0.1
 */
class Generator extends BaseGenerator
{
    /**
     * whether to overwrite (extended) model classes, will be always created, if file does not exist
     * @var bool
     */
    public $generateModelClass = false;

    /**
     * whether to overwrite (extended) model classes, will be always created, if file does not exist
     * @var bool
     */
    public $enableI18N = true;

    /**
     * String for the table prefix, which is ignored in generated class name
     * @var null
     */
    public $tablePrefix = null;

    /**
     * key-value pairs for mapping a table-name to class-name, eg. 'prefix_FOObar' => 'FooBar'
     * @var array
     */
    public $tableNameMap = [];

    /**
     * @var string
     */
    public $messageCategory = 'app';

    public $baseClass = 'app\models\ActiveRecord';

    public $queryBaseClass = 'app\db\ActiveQuery';

    /**
     * @var
     */
    protected $classNames2;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Giiant Model';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates an ActiveRecord class and base class for the specified database table.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['generateModelClass'], 'boolean'],
            [['tablePrefix'], 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'generateModelClass' => 'Generate Model Class',
        ]);
    }

    protected function generateRelations()
    {
        $relations = parent::generateRelations();

        // inject namespace
        $ns = "\\{$this->ns}\\";
        // Replace Namespace if User-Attributes like CreatedBy or UpdatedBy
        $nsUser = "\\app\\models\\";
        foreach ($relations as $model => $relInfo) {
            foreach ($relInfo as $relName => $relData) {
                // removed duplicated relations, eg. klientai, klientai0
                if ($this->removeDuplicateRelations && is_numeric(substr($relName, -1))) {
                    unset($relations[$model][$relName]);
                    continue;
                }

                $relations[$model][$relName][0] = preg_replace('/(has[A-Za-z0-9]+\()([a-zA-Z0-9]+::)/', '$1__NS__$2', $relations[$model][$relName][0]);
                $relations[$model][$relName][0] = str_replace('::className()', '::class', $relations[$model][$relName][0]);
                if ($relName == "created_by" || $relName == "updated_by") {
                    $relations[$model][$relName][0] = str_replace('__NS__', $nsUser, $relations[$model][$relName][0]);
                } else {
                    $relations[$model][$relName][0] = str_replace('__NS__', $ns, $relations[$model][$relName][0]);
                }
                $relations[$model][$relName][0] = preg_replace_callback('!(return \$this->(hasOne|hasMany|belongs)\()(.*\(\), )(\[.+?\])(\);*)((.+\()((.*, )(.*))(\);))*!', function ($matches) {
                    $out = $matches[1] . "\n" . str_repeat(' ', 12) . trim($matches[3]) . "\n" . str_repeat(' ', 12) . trim($matches[4]) . "\n" . str_repeat(' ', 8) . trim($matches[5]);
                    if (isset($matches[6])) {
                        $out .= $matches[7] . "\n" . str_repeat(' ', 12) . trim($matches[9]) . "\n" . str_repeat(' ', 12) . trim($matches[10]) . "\n" . str_repeat(' ', 8) . $matches[11];
                    }
                    return $out;
                }, $relations[$model][$relName][0]);
            }
        }

        return $relations;
    }

    /**
     * Generates the attribute labels for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated attribute labels (name => label)
     */
    public function generateLabels($table)
    {
        $labels = [];
        foreach ($table->columns as $column) {
            $comments = $this->extractComments($column);
            if ($this->generateLabelsFromComments && ($comments->label)) {
                $labels[$column->name] = $comments->label;
            } else if (! strcasecmp($column->name, 'id')) {
                $labels[$column->name] = 'ID';
            } else {
                $label = Inflector::camel2words($column->name);
                if (! empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                    $label = substr($label, 0, -3); // Removing ID from label
                }
                $labels[$column->name] = $label;
            }
        }

        return $labels;
    }

    /**
     * Generates the attribute labels for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated attribute labels (name => label)
     */
    public function generatePlaceholders($table)
    {
        $placeholders = [];
        foreach ($table->columns as $column) {
            $comments = $this->extractComments($column);
            if ($comments && ($comments->placeholder)) {
                $placeholders[$column->name] = $comments->placeholder;
            }
        }
        return $placeholders;
    }


    /**
     * Generates the attribute labels for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated attribute labels (name => label)
     */
    public function generateHints($table)
    {
        $hints = [];
        foreach ($table->columns as $column) {
            $comments = $this->extractComments($column);
            if ($comments && ($comments->hint)) {
                $hints[$column->name] = $comments->hint;
            }
        }
        return $hints;
    }

    /**
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
     * @param      $tableSchema
     * @param      $relations
     * @param      $ns
     * @param      $className
     * @param bool $translation
     * @return bool|null
     */
    public function toStringAttribute($tableSchema)
    {
        $attribute = null;
        foreach ($tableSchema->columns as $column) { // search for toString attribute name
            if (strtolower($column->name) == "name") {
                return $column->name;
            }
        }
        foreach ($tableSchema->columns as $column) { // search for other attribute
            if (strstr(strtolower($column->name), "name") !== false) {
                return $column->name;
            }
        }

        foreach ($tableSchema->columns as $column) { // search for toString attribute key
            if (strtolower($column->name) == "key") {
                return $column->name;
            }
        }

        foreach ($tableSchema->columns as $column) { // search for other attribute
            if ($column->phpType == "string" && strpos(strtolower($column->name), "date") === false) {
                return $column->name;
            }
        }

        foreach ($tableSchema->columns as $column) { // find first varchar
            if (strtolower($column->phpType) == "string") {
                return $column->name;
            }
        }

        if ($foreignLabelAttribute == null) { // if still no attribute is found
            foreach ($tableSchema->columns as $column) {
                return $column->name; // get first attribute
            }
        }
    }

    /**
     * Determine if a table has more than one primary key
     * @param $tableSchema
     * @return bool
     */
    public function tableHasCompositePrimaryKey($tableSchema)
    {
        $primaryKeys = $this->generatePrimaryKeys($tableSchema);
        return count($primaryKeys) >= 2;
    }

    /**
     * Get all the primary keys
     * @param $tableSchema
     * @return array
     */
    public function generatePrimaryKeys($tableSchema)
    {
        $primaryKeys = [];
        foreach ($tableSchema->columns as $column) {
            if ($column->isPrimaryKey) {
                $primaryKeys[] = $column->name;
            }
        }
        return $primaryKeys;
    }

    /**
     * @param \yii\db\TableSchema $table
     * @return array
     */
    public function generateRules($table)
    {
        $rules = parent::generateRules($table);
        foreach ($rules as $k => $rule) {
            $rule = str_replace('::className()', '::class', $rule);
            $rule = preg_replace_callback('!\[(\[.+?\]), (.+)?\]!', function ($matches) {
                $rest = explode(',', $matches[2]);
                foreach ($rest as &$r) {
                    $r = trim($r);
                }
                $rest = implode(",\n" . str_repeat(' ', 16), ($rest));
                return str_repeat(' ', 0) . "[\n" . str_repeat(' ', 16) . $matches[1] . ",\n" . str_repeat(' ', 16) . $rest . "\n" . str_repeat(' ', 12) . ']';
            }, $rule);
            $rules[$k] = $rule;
        }
        foreach ($table->columns as $column) {
            if ($this->isCommentType($column, 'googlemap')) {
                $rules[] = "['" . $column->name . "', function (\$attribute) {\n" . str_repeat(' ', 16) . "\$parts = explode('|', \$this->\$attribute);\n" . str_repeat(' ', 16) . "if (empty(\$parts[0]) && empty(\$parts[2]) && !empty(\$parts[1])) {\n" . str_repeat(' ', 20) . "\$this->addError(\$attribute, Yii::t('app', 'You must enter Longitude'));\n" . str_repeat(' ', 16) . "} elseif (empty(\$parts[1]) && empty(\$parts[2]) && !empty(\$parts[0])) {\n" . str_repeat(' ', 20) . "\$this->addError(\$attribute, Yii::t('app', 'You must enter Latitude'));\n" . str_repeat(' ', 16) . "}\n" . str_repeat(' ', 12) . "}\n" . str_repeat(' ', 12) . "]";
            }
        }
        return $rules;
    }

    public function stickyAttributes()
    {
        $sticky = parent::stickyAttributes();
        unset($sticky[array_keys($sticky, 'messageCategory')[0]]);
        return $sticky;
    }

    /**
     * @return array
     */
    public function getUploadFields()
    {
        $crudGenerator = new \andrej2013\yiiboilerplate\templates\crud\Generator();
        $safeAttributes = Yii::$app->db->getTableSchema($this->tableName)->columnNames;
        $out = [];
        foreach ($safeAttributes as $attribute) {
            $column = ArrayHelper::getValue(Yii::$app->db->getTableSchema($this->tableName)->columns, $attribute);
            if ($crudGenerator->checkIfUploaded($column) && $column->allowNull) {
                $out[] = $attribute;
            }
        }
        return $out;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function isCommentType($attribute, $type)
    {
        if (! $attribute instanceof ColumnSchema) {
            $attribute = $this->getDbConnection()
                              ->getTableSchema($this->tableName)
                              ->getColumn($attribute);
        }
        return strtolower($this->extractComments($attribute)->inputtype) == strtolower($type);
    }

    /**
     * @param $type
     * @return bool
     */
    public function haveCommentType($type)
    {
        foreach ($this->getDbConnection()
                      ->getTableSchema($this->tableName)->columns as $column) {
            if ($this->isCommentType($column, $type)) {
                return true;
            }
        }
        return false;
    }
}
