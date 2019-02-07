<?php
/**
 * Created by PhpStorm.
 * User: ntesic
 * Date: 2/1/19
 * Time: 7:50 PM
 */

namespace andrej2013\yiiboilerplate\grid;


use yii\helpers\Html;

class CheckboxColumn extends \kartik\grid\CheckboxColumn
{
    public function renderDataCell($model, $key, $index)
    {
        return parent::renderDataCell($model, $key, $index); // TODO: Change the autogenerated stub
    }
    
    protected function renderDataCellContent($model, $key, $index)
    {
        $output = Html::beginTag('div', ['class' => 'checkbox']);
        $output .= Html::beginTag('label');
        $output .= parent::renderDataCellContent($model, $key, $index);
        $output .= Html::beginTag('span', ['class' => 'cr']);
        $output .= Html::tag('i', '', ['class' => 'cr-icon glyphicon glyphicon-ok']);
        $output .= Html::endTag('span');
        $output .= Html::endTag('label');
        $output .= Html::endTag('div');
        return $output;
    }
}