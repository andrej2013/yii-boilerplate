<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 6/19/2017
 * Time: 11:01 AM
 */

namespace andrej2013\yiiboilerplate\widget;

class FileInput extends \kartik\file\FileInput
{

    public function registerAssets()
    {
        $this->registerAssetBundle();
        if ($this->pluginOptions['required']) {
            $this->registerPlugin($this->pluginName, null, 'function(){var prev = $("input[name=\'" + $(this).attr("name") + "\']")[0]; $(prev).val($(this)[0].files[0].name)}');
        } else {
            $this->registerPlugin($this->pluginName);
        }
    }
}