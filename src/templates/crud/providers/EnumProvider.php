<?php
/**
 * Created by PhpStorm.
 * User: ntesic
 * Date: 2/19/19
 * Time: 11:52 PM
 */

namespace andrej2013\yiiboilerplate\templates\crud\providers;

use andrej2013\yiiboilerplate\templates\crud\Generator;
use andrej2013\yiiboilerplate\templates\crud\providers\base\Provider;
use yii\helpers\Inflector;

class EnumProvider extends Provider
{
    public $type = Generator::TYPE_ENUM;

    /**
     * @param $attribute
     * @return string
     */
    public function activeField($attribute)
    {
        if ($this->condition($attribute)) {
            $method = __METHOD__;
            $column = $this->generator->getColumnByAttribute($attribute);
            $model = $this->generator->modelClass;
            $dropdown_options = [];
            foreach ($column->enumValues as $enumValue) {
                $const = $attribute . '_' . $enumValue;
                $const = '\\' . $model . '::' . strtoupper($const);
                $dropdown_options[$const] = $this->generator->generateString(Inflector::humanize($enumValue));
            }
            $dropdown_options = $this->generator->var_export54($dropdown_options, '', false);
            $options = [
                'options'       => [
                    'id' => "Html::getInputId(\$model, '$attribute') . \$caller_id",
                ],
                'data'          => $dropdown_options,
                'hideSearch'    => true,
                'pluginOptions' => [
                    'allowClear' => count($dropdown_options) > 10 ? true : false,
                ],
            ];
            $options = $this->generator->var_export54($options);
            $html = <<<HTML
/*Generated by $method*/
\$form->field(
    \$model,
    '$attribute',
    [
        'selectors' => [
            'input' => '#'.Html::getInputId(\$model, '$attribute') . \$caller_id,
        ]
    ]
    )
    ->widget(Select2::class, $options
                    )
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;
            return $html;

        }
    }

    /**
     * @param $attribute
     * @param $model
     * @return string
     */
    public function columnFormat($attribute, $model)
    {
        if ($this->condition($attribute)) {
            $method = __METHOD__;
            $column = $this->generator->getColumnByAttribute($attribute);
            $model = $this->generator->modelClass;
            $dropdown_options = [];
            foreach ($column->enumValues as $enumValue) {
                $const = $attribute . '_' . $enumValue;
                $const = '\\' . $model . '::' . strtoupper($const);
                $dropdown_options[$const] = $this->generator->generateString(Inflector::humanize($enumValue));
            }
            $dropdown_options = $this->generator->var_export54($dropdown_options, '', false);
            $options = [
                'attribute'           => "'$attribute'",
                'content'             => "function (\$model) {
                    return \Yii::t('app', \$model->$attribute);
                }",
                'filter'              => $dropdown_options,
                'filterType'          => "GridView::FILTER_SELECT2",
                'class'               => "'\kartik\grid\DataColumn'",
                'filterWidgetOptions' => [
                    'options'       => [
                        'placeholder' => "''",
                        'multiple'    => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ],
            ];
            $html = $this->generator->var_export54($options);
            return <<<HTML
/*Generated by $method*/
$html
HTML;
        }
    }

    public function attributeFormat($attribute)
    {
        if ($this->condition($attribute)) {
            $options = [
                'attribute' => "'$attribute'",
                'content'   => "function (\$model) {
                   return \Yii::t('app', \$model->$attribute);
               }",
            ];
            $method = __METHOD__;
            $html = $this->generator->var_export54($options);
            return <<<HTML
/*Generated by $method*/
$html
HTML;

        }
    }
}