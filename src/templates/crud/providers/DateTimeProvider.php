<?php

namespace andrej2013\yiiboilerplate\templates\crud\providers;

class DateTimeProvider extends \schmunk42\giiant\base\Provider
{
    /**
     * @param $attribute
     * @return string|void
     */
    public function activeField($attribute)
    {
        switch (true) {
            case in_array($attribute, $this->columnNames):
                $this->generator->requires[] = 'zhuravljov/yii2-datetime-widgets';

                return <<<EOS
\$form->field(\$model, '{$attribute}')->widget(\kartik\date\DatePicker::className(), [
    'convertFormat' => true,
    'type' => \kartik\date\DatePicker::TYPE_COMPONENT_APPEND,
    'pluginOptions' => [
	'format' => Yii::\$app->formatter->dateFormat,
	'todayHighlight' => true,
	'autoclose' => true,
	'class' => 'form-control'
    ],
])
EOS;
                break;
            default:
                return;
        }
    }
}
