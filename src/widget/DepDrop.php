<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

namespace andrej2013\yiiboilerplate\widget;

use kartik\depdrop\DepDropAsset;
use kartik\depdrop\DepDropExtAsset;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use kartik\depdrop\DepDrop as BaseDepDrop;
use andrej2013\yiiboilerplate\widget\Select2;

class DepDrop extends BaseDepDrop
{
    /**
     *
     */
    public function registerAssets()
    {
        $view = $this->getView();
        DepDropAsset::register($view)->addLanguage($this->language, 'depdrop_locale_');
        DepDropExtAsset::register($view);
        $this->registerPlugin($this->pluginName);

        if ($this->type === self::TYPE_SELECT2) {
            $loading = ArrayHelper::getValue($this->pluginOptions, 'loadingText', 'Loading ...');
            $this->select2Options['data'] = $this->data;
            $this->select2Options['options'] = $this->options;
            if ($this->hasModel()) {
                $settings = ArrayHelper::merge($this->select2Options, [
                    'model' => $this->model,
                    'attribute' => $this->attribute
                ]);
            } else {
                $settings = ArrayHelper::merge($this->select2Options, [
                    'name' => $this->name,
                    'value' => $this->value
                ]);
            }
            echo Select2::widget($settings);
            $id = $this->options['id'];
            $view->registerJs("initDepdropS2('{$id}','{$loading}');");
        } else {
            echo $this->getInput('dropdownList', true);
        }
    }
}
