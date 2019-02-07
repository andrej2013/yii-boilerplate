<?php

namespace andrej2013\yiiboilerplate\templates\crud\providers;

use yii\db\ActiveRecord;

class DateProvider extends \schmunk42\giiant\base\Provider
{
    /**
     * Formatter for detail view attributes, who have get[..]ValueLabel function.
     * @param $attribute
     * @param $model ActiveRecord
     */
    public function columnFormat($attribute, $model)
    {
        return;
    }

    /**
     * @param $attribute
     * @return string|void
     */
    public function activeField($attribute)
    {
        if (isset($this->generator->getTableSchema()->columns[$attribute])) {
            $column = $this->generator->getTableSchema()->columns[$attribute];
        } else {
            return;
        }

        switch (true) {
            case in_array($column->name, $this->columnNames):
                $this->generator->requires[] = 'zhuravljov/yii2-datetime-widgets';

                return <<<EOS
\$form->field(\$model, '{$column->name}')->widget(\kartik\date\DatePicker::className(), [
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