<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 7/20/2017
 * Time: 2:45 PM
 */

namespace andrej2013\yiiboilerplate\widget;

use andrej2013\yiiboilerplate\components\GoogleMap;
use yii\web\View;
use yii\widgets\InputWidget;
use yii\helpers\Html;

class GoogleMapInput extends InputWidget
{

    const IFRAME = GoogleMap::IFRAME;
    const IMAGE = GoogleMap::IMAGE;

    /**
     * @var
     */
    public $size;
    /**
     * @var
     */
    public $width;
    /**
     * @var
     */
    public $height;
    /**
     * @var string
     */
    public $template = '{input}{code}';
    /**
     * @var string
     */
    public $format = self::IFRAME;

    /**
     *
     */
    public function run()
    {
        $this->options['class'] = 'form-control';
        if ($this->hasModel()) {
            $replace['{input}'] = Html::activeHiddenInput($this->model, $this->attribute, $this->options);
        } else {
            $replace['{input}'] = Html::hiddenInput($this->name, $this->value, $this->options);
        }
        if (!empty($this->model->{$this->attribute})) {
            $split = explode('|', $this->model->{$this->attribute});
            $lng = $split[0];
            $lat = $split[1];
            $address = $split[2];
        } else {
            $lng = null;
            $lat = null;
            $address = null;
        }
        $replace['{code}'] =
            '<br>' .
            '<div style="display: flex;">' .
            '<div style="flex-grow: 1;">' .
            '<div class="form-group">' .
            '<label class="control-label col-lg-3" for="' . $this->options['id'] . '-lng' . '">' . \Yii::t('app', 'Longitude') . ':</label>' .
            '<div class="col-lg-9">' .
            Html::textInput($this->options['id'] . '-lng', $lng, [
                'class' => 'form-control',
                'id' => $this->options['id'] . '-lng',
                'placeholder' => \Yii::t('app', 'Longitude'),
                'type' => 'number',
                'step' => '0.000001',
                'min' => '-180',
                'max' => '180',
            ]) .
            '</div>' .
            '</div>' .
            '<br>' .
            '<br>' .
            '<div class="form-group">' .
            '<label class="control-label col-lg-3" for="' . $this->options['id'] . '-lng' . '">' . \Yii::t('app', 'Latitude') . ':</label>' .
            '<div class="col-lg-9">' .
            Html::textInput($this->options['id'] . '-lat', $lat, [
                'class' => 'form-control',
                'id' => $this->options['id'] . '-lat',
                'placeholder' => \Yii::t('app', 'Latitude'),
                'type' => 'number',
                'step' => '0.000001',
                'min' => '-90',
                'max' => '90',
            ]) .
            '</div>' .
            '</div>' .
            '<br>' .
            '<br>' .
            '<div class="form-group">' .
            '<label class="control-label col-lg-3" for="' . $this->options['id'] . '-lng' . '">' . \Yii::t('app', 'Address') . ':</label>' .
            '<div class="col-lg-9">' .
            Html::textInput($this->options['id'] . '-address', $address, [
                'class' => 'form-control',
                'id' => $this->options['id'] . '-address',
                'placeholder' => \Yii::t('app', 'Address'),
            ]) .
            '</div>' .
            '</div>' .
            '</div>' .
            '{map}' .
            '</div>';
        $map = new GoogleMap();
        $map->longitude = $lng;
        $map->latitude = $lat;
        $map->address = $address;
        $map->width = $this->width;
        $map->height = $this->height;
        $map->size = $this->size;
        $map->format = $this->format;

        $replace['{code}'] = strtr(
            $replace['{code}'],
            ['{map}' => !empty($this->model->{$this->attribute}) ? '<div>' . $map->render() . '</div>' : '']
        );
        echo strtr($this->template, $replace);
        $this->registerJs();
    }

    public function registerJs()
    {
        $hidden = $this->options['id'];
        $lng = $this->options['id'] . '-lng';
        $lat = $this->options['id'] . '-lat';
        $address = $this->options['id'] . '-address';
        $js = <<<JS
$(document).ready(function(){
    $('#$lng, #$lat, #$address').on('keyup change', function(){
        $('#$hidden').val($('#$lng').val() + '|' + $('#$lat').val() + '|' + $('#$address').val());
    });
    $('#$lng').closest('form').on('beforeSubmit', function(e){    
        if ($('#$hidden').val() == '||') {
            $('#$hidden').val('');
        }
    });
});
JS;
        $this->getView()->registerJs($js, View::POS_END);
    }
}