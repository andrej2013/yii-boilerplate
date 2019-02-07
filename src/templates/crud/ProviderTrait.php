<?php

namespace andrej2013\yiiboilerplate\templates\crud;

/*
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;

trait ProviderTrait
{
    /**
     * @return array Class names of the providers declared directly under crud/providers folder.
     */
    public static function getCoreProviders()
    {
        $files = FileHelper::findFiles(
            __DIR__ . DIRECTORY_SEPARATOR . 'providers',
            [
                'only' => ['*.php'],
                'recursive' => false,
            ]
        );

        foreach ($files as $file) {
            require_once $file;
        }

        return array_filter(
            get_declared_classes(),
            function ($a) {
                return stripos($a, __NAMESPACE__ . '\providers') !== false;
            }
        );
    }

    /**
     * @return array List of providers. Keys and values contain the same strings.
     */
    public function generateProviderCheckboxListData()
    {
        $coreProviders = self::getCoreProviders();

        return array_combine($coreProviders, $coreProviders);
    }

    /**
     *
     */
    protected function initializeProviders()
    {
        // TODO: this is a hotfix for an already initialized provider queue on action re-entry
        if ($this->_p !== []) {
            return;
        }

        if ($this->providerList) {
            foreach ($this->providerList as $class) {
                $class = trim($class);
                if (!$class) {
                    continue;
                }
                $obj = \Yii::createObject(['class' => $class]);
                $obj->generator = $this;
                $this->_p[] = $obj;
                #\Yii::trace("Initialized provider '{$class}'", __METHOD__);
            }
        }

        \Yii::trace("CRUD providers initialized for model '{$this->modelClass}'", __METHOD__);
    }

    /**
     * Generates code for active field by using the provider queue.
     * @param $attribute
     * @param null $model
     * @return mixed|void
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
            if (!$column) {
                return;
            } else {
                return parent::generateActiveField($attribute);
            }
        }
    }

    /**
     * @param $attribute
     * @param null $model
     * @return mixed
     */
    public function prependActiveField($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);
        }

        return $code;
    }

    /**
     * @param $attribute
     * @param null $model
     * @return mixed
     */
    public function appendActiveField($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);
        }

        return $code;
    }

    public function columnFormat($attribute, $model = null, $template = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code !== null) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);
        } else {
            $code = $this->shorthandAttributeFormat($attribute, $model, $template);
            Yii::trace("using standard formatting for '{$attribute}'", __METHOD__);
        }

        return $code;
    }

    /**
     * @param $attribute
     * @param null $model
     * @return mixed|string|void
     */
    public function attributeFormat($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code !== null) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);

            return $code;
        }

        $column = $this->getColumnByAttribute($attribute);
        if (!$column) {
            return;
        } else {
            return $this->shorthandAttributeFormat($attribute, $model);
        }
        // don't call parent anymore
    }

    /**
     * @param $name
     * @param null $model
     * @return mixed
     */
    public function partialView($name, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $name, $model, $this);
        if ($code) {
            Yii::trace("found provider for partial view '{name}'", __METHOD__);
        }

        return $code;
    }

    /**
     * @param $name
     * @param $relation
     * @param bool $showAllRecords
     * @return mixed
     */
    public function relationGrid($name, $relation, $showAllRecords = false)
    {
        Yii::trace("calling provider queue for '$name'", __METHOD__);

        return $this->callProviderQueue(__FUNCTION__, $name, $relation, $showAllRecords);
    }

    /**
     * @param $attribute
     * @param $model
     * @param null $template
     * @return string|void
     */
    protected function shorthandAttributeFormat($attribute, $model, $template = null)
    {
        $column = $this->getColumnByAttribute($attribute, $model);
        if (!$column) {
            Yii::trace("No column for '{$attribute}' found", __METHOD__);

            return;
        } else {
            Yii::trace("Table column detected for '{$attribute}'", __METHOD__);
        }
        $comment = $this->extractComments($column);
        if (is_array($column->enumValues) && count($column->enumValues) > 0) {
            if ($template === 'index') {
                $dropOptions = "[";
                foreach ($column->enumValues as $enumValue) {
                    $dropOptions .= "\n" . str_repeat(' ', 12) . "'" .
                        $enumValue . "' => Yii::t('app', '" . Inflector::humanize($enumValue) . "'),";
                }
                $dropOptions .= "\n" . str_repeat(' ', 8) . "],";
            }
            return "
                [
        " . ($template !== 'index' ? str_repeat(' ', 12) : '') . "'attribute' => '$column->name',
        " . ($template !== 'index' ? str_repeat(' ', 12) : '') . "'content' => function (\$model) {
            " . ($template !== 'index' ? str_repeat(' ', 12) : '') . "return \\Yii::t('app', \$model->$column->name);
        " . ($template !== 'index' ? str_repeat(' ', 12) : '') . "}," .
                ($template === 'index' ? "\n" . str_repeat(' ', 8) . "'filter' => $dropOptions
        'filterType' => GridView::FILTER_SELECT2,
        'class' => '\\kartik\\grid\\DataColumn',
        'filterWidgetOptions' => [
            'options' => [
                'placeholder' => '',
                'multiple' => true,
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ]
        ]," : "")
                . "\n" . str_repeat(' ', 4) . ($template !== 'index' ? str_repeat(' ', 12) : '') . "]";
        } elseif ($column->phpType === 'boolean' || (strpos($column->name, "is_") === 0)) {
            if ($template === 'index') {
                return "[
        'attribute' => '$column->name',
        'class' => '\\kartik\\grid\\BooleanColumn',
        'trueLabel' => \\Yii::t('app','Yes'),
        'falseLabel' => \\Yii::t('app','No'),
        'content' => function (\$model) {
            return \$model->$column->name == 1 ? Yii::t('app', 'Yes') : Yii::t('app', 'No');
        },
    ]";
            }
            $format = 'boolean';
        } elseif ($comment && $comment->inputtype == strtolower('qrcode')) {
            if ($template === 'index') {
                return "[
        'attribute' => '$column->name',
        'format' => 'html',
        'content' => function (\$model) {
            return Html::img(\$model->renderQrCode('$column->name'));
        },
    ]";
            }
        } elseif ($comment && $comment->inputtype == strtolower('googlemap')) {
            if ($template === 'index') {
                return "[
        'attribute' => '$column->name',
        'format' => 'html',
        'content' => function (\$model) {
			return Html::img(\$model->renderGoogleMap('$column->name', [
				'format' => \\andrej2013\\yiiboilerplate\\components\\GoogleMap::IMAGE,
			]));
        },
    ]";
            }
        } elseif ($column->type === 'text') {
            // For texts, we want to limit to 500 char because otherwise it's unreadable
            return "[
        'attribute' => '$column->name',
        'content' => function (\$model) {
            return nl2br(strlen(\$model->" . $column->name . ") > 500 ? substr(\$model->" . $column->name .
                ", 0, 500) . '...' : \$model->" . $column->name . ");
        },
    ]";
        } elseif ($column->dbType == 'date' || ($column->dbType == 'datetime' && $template === 'index')) {
            if ($template === 'index') {
                $format = $column->dbType == 'datetime' ? 'Yii::$app->formatter->momentJsDateTimeFormat' : 'Yii::$app->formatter->momentJsDateFormat';
                return "[
        'attribute' => '$column->name',
        'content' => function (\$model) {
            return \\Yii::\$app->formatter->as" . ucfirst($column->dbType) . "(\$model->$column->name);
        },
        'class' => '\\kartik\\grid\\DataColumn',
        'format' => '$column->dbType',
        'filterType' => GridView::FILTER_DATE_RANGE,
        'filterWidgetOptions' => [
            'presetDropdown' => true,
            'pluginEvents' => [
                'apply.daterangepicker' => 'function(ev, picker) {
                    if($(this).val() == \"\") {
                        $(this).val(picker.startDate.format(picker.locale.format) + picker.locale.separator +
                        picker.endDate.format(picker.locale.format)).trigger(\"change\");
                    }
                }',
                'show.daterangepicker' => 'function(ev, picker) {
                    picker.container.find(\".ranges\").off(\"mouseenter.daterangepicker\", \"li\");
                    if($(this).val() == \"\") {
                        picker.container.find(\".ranges .active\").removeClass(\"active\");
                    }
                }',
                'cancel.daterangepicker' => 'function(ev, picker) {
                    if($(this).val() != \"\") {
                        $(this).val(\"\").trigger(\"change\");
                    }
                }'
            ],
            'pluginOptions' => [
                'opens'=>'left',
                'locale' => [
                    'format' => $format,
                    'separator' => ' TO ',
                ]
            ],
        ],
    ]";
            }
            $format = 'date';
        } elseif ((stripos($column->name, 'time') !== false && $column->phpType === 'integer') ||
            $column->dbType == 'datetime'
        ) {
            $format = 'datetime';
        } elseif (stripos($column->name, 'email') !== false) {
            $format = 'email';
        } elseif (stripos($column->name, 'url') !== false) {
            $format = 'url';
        } else {
            $format = 'text';
        }

        return str_repeat(' ', 16) . "'" . $column->name . ($format === 'text' ? '' : ':' . $format) . "'";
    }

    /**
     * @param $func
     * @param $args
     * @param $generator
     * @return mixed
     */
    protected function callProviderQueue($func, $args, $generator)
    {
        // TODO: should be done on init, but providerList is empty
        $this->initializeProviders();

        $args = func_get_args();
        unset($args[0]);
        // walk through providers
        foreach ($this->_p as $obj) {
            if (method_exists($obj, $func)) {
                $c = call_user_func_array(array(&$obj, $func), $args);
                // until a provider returns not null
                if ($c !== null) {
                    if (is_object($args)) {
                        $argsString = get_class($args);
                    } elseif (is_array($args)) {
                        $argsString = Json::encode($args);
                    } else {
                        $argsString = $args;
                    }
                    $msg = 'Using provider ' . get_class($obj) . '::' . $func . ' ' . $argsString;
                    Yii::trace($msg, __METHOD__);

                    return $c;
                }
            }
        }
    }
}
