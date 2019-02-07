<?php
namespace andrej2013\yiiboilerplate\widget;

use \yii\bootstrap\ActiveField as YiiActiveField;
use yii\helpers\Html;

class ActiveField extends YiiActiveField
{
    /**
     * Returns the JS options for the field.
     * @return array the JS options
     */
    protected function getClientOptions()
    {
        $options = parent::getClientOptions();
        $options['id'] = $this->getCustomInputId();
        return $options;
    }

    /**
     * @return mixed
     */
    protected function getCustomInputId()
    {
        return (isset($this->selectors['input']) ? str_replace('#', '', $this->selectors['input']) : Html::getInputId($this->model, $this->attribute));
    }
}
