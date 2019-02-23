<?php
/**
 * Created by PhpStorm.
 * User: ntesic
 * Date: 2/20/19
 * Time: 8:28 PM
 */

namespace andrej2013\yiiboilerplate\templates\crud\providers;

use andrej2013\yiiboilerplate\templates\crud\Generator;
use andrej2013\yiiboilerplate\templates\crud\providers\base\Provider;

class TimeProvider extends Provider
{
    public $type = Generator::TYPE_TIME;

    public function activeField($attribute)
    {
        if ($this->condition($attribute)) {
            $options = [
                'id'            => "Html::getInputId(\$model, '$attribute') . \$caller_id",
                'pluginOptions' => [
                    'autoclose'   => true,
                    'showSeconds' => true,
                    'class'       => "'form-control'",
                ],
            ];
            $options = $this->generator->var_export54($options);
            $method = __METHOD__;
            return <<<HTML
/*Generated by $method*/
\$form->field(
    \$model,
    '{$attribute}',
    [
        'selectors' => [
            'input' => '#'.Html::getInputId(\$model, '$attribute') . \$caller_id,
        ]
    ]
    )
     ->widget(\\kartik\\time\\TimePicker::class, $options
     )
    ->hint(\$model->getAttributeHint('$attribute'));
HTML;
        }
    }

    public function attributeFormat($attribute)
    {
        if ($this->condition($attribute)) {
            $method = __METHOD__;
            return "/*Generated by $method*/
            '$attribute:time'";
        }
    }
}