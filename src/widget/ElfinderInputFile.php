<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 7/19/2017
 * Time: 11:52 AM
 */

namespace andrej2013\yiiboilerplate\widget;

use mihaildev\elfinder\InputFile;
use mihaildev\elfinder\ElFinder;
use \Yii;
use yii\helpers\Html;
use mihaildev\elfinder\AssetsCallBack;
use yii\helpers\Json;

class ElfinderInputFile extends InputFile
{

    public $shares = [];
    public $template = '<div class="input-group">{input}<span class="input-group-btn">{button}{clear}</span></div>';
    public $controller = 'file';

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        if (empty($this->language)) {
            $this->language = ElFinder::getSupportedLanguage(Yii::$app->language);
        }

        if (empty($this->buttonOptions['id'])) {
            $this->buttonOptions['id'] = $this->options['id'] . '_button';
        }

        $this->buttonOptions['type'] = 'button';

        $managerOptions = [];
        if (!empty($this->filter)) {
            $managerOptions['filter'] = $this->filter;
        }
        $managerOptions['callback'] = $this->options['id'];

        if (!empty($this->language)) {
            $managerOptions['lang'] = $this->language;
        }
        if (!empty($this->multiple)) {
            $managerOptions['multiple'] = $this->multiple;
        }
        if (!empty($this->path)) {
            $managerOptions['path'] = $this->path;
        }
        if (!empty($this->shares)) {
            $managerOptions['shares'] = $this->shares;
        }
        $params = $managerOptions;
        if (!empty($this->startPath)) {
            $params['#'] = ElFinder::genPathHash($this->startPath);
        }

        $this->_managerOptions['url'] = ElFinder::getManagerUrl($this->controller, $params);
        $this->_managerOptions['width'] = $this->width;
        $this->_managerOptions['height'] = $this->height;
        $this->_managerOptions['id'] = $this->options['id'];
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        $this->options['class'] = 'form-control';
        $this->options['readOnly'] = true;
        $this->buttonOptions['class'] = 'btn btn-primary';
        if ($this->hasModel()) {
            $replace['{input}'] = Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            $replace['{input}'] = Html::textInput($this->name, $this->value, $this->options);
        }

        $replace['{button}'] = Html::tag($this->buttonTag, Yii::t('app', 'Browse') . ' ...', $this->buttonOptions);
        $replace['{clear}'] = Html::tag($this->buttonTag, Yii::t('app', 'Clear'), [
            'type' => 'button',
            'class' => 'btn btn-danger',
            'id' => $this->options['id'] . '-clear',
            'style' => 'display: none;'
        ]);


        echo strtr($this->template, $replace);

        AssetsCallBack::register($this->getView());

        if (!empty($this->multiple)) {
            $this->getView()->registerJs("mihaildev.elFinder.register(" .
                Json::encode($this->options['id']) .
                ", function(files, id){ var _f = []; for (var i in files) { _f.push(files[i].file.hash; } \$('#' + id).val(_f.join(', ')).trigger('change', [files, id]); return true;}); $(document).on('click','#" .
                $this->buttonOptions['id'] .
                "', function(){mihaildev.elFinder.openManager(" . Json::encode($this->_managerOptions) . ");});");
        } else {
            $this->getView()->registerJs("mihaildev.elFinder.register(" .
                Json::encode($this->options['id']) .
                ", function(file, id){
                    \$('#' + id).val(file.hash).trigger('change', [file, id]);; return true;}); $(document).on('click', '#" . $this->buttonOptions['id'] .
                "', function(){mihaildev.elFinder.openManager(" . Json::encode($this->_managerOptions) . ");});");
        }
        $this->registerJs();
    }

    public function registerJs()
    {
        $clearButton = $this->options['id'] . '-clear';
        $inputField = $this->options['id'];
        $js = <<<JS
        $(document).ready(function(){
            $('#$inputField').on('change', function(){
                if ($(this).val() != '') {
                    $('#$clearButton').show();
                }
            });
            $('#$clearButton').on('click', function(){
                $('#$inputField').val(null);
                $(this).hide();
            });
        });
JS;
        $this->getView()->registerJs($js, \yii\web\View::POS_END);
    }
}