<?php
/**
 * Created by PhpStorm.
 * User: ntesic
 * Date: 2/2/19
 * Time: 6:01 PM
 */

namespace andrej2013\yiiboilerplate\modules\backend\widgets;

use kartik\switchinput\SwitchInput;
use yii\bootstrap\Widget;
use yii\helpers\Html;
use andrej2013\yiiboilerplate\widget\Modal;
use Yii;
use yii\helpers\Url;
use yii\web\View;

class GridConfig extends Widget
{
    /**
     * @var string
     */
    public $model;

    /**
     * @var string
     */
    public $grid;

    /**
     * @var array
     */
    public $columns;

    /**
     * @var string
     */
    public $pjaxContainerId;

    public function init()
    {
        return parent::init();
    }

    public function run()
    {
        Modal::begin([
            'header'       => Html::tag('h4', Yii::t('app', 'Table Configuration')),
            'id'           => 'grid_config',
            'footer'       => Html::button('<span class="fa fa-check"></span>&nbsp;' . Yii::t('app', 'Save'), [
                    'class'  => 'btn pull-right',
                    'preset' => Html::PRESET_PRIMARY,
                    'form'   => 'grid_config_form',
                    'id'     => 'grid_config_form_submit',
                ]) . Html::a('<span class="fa fa-ban"></span>&nbsp;' . Yii::t('app', 'Cancel'), ['#'], [
                    'class'        => 'btn',
                    'preset'       => Html::PRESET_DANGER,
                    'data-dismiss' => 'modal',
                ]),
            'toggleButton' => [
                'tag'   => 'button',
                'label' => '<i class="fa fa-cogs"></i>',
                'class' => 'btn',
                'color' => Html::TYPE_DEFAULT,
            ],
        ]);
        echo Html::beginForm(Url::toRoute(['backend/grid-config/index']), 'post', [
            'class' => 'form-default',
            'id'    => 'grid_config_form',

        ]);
        $this->renderFields();
        echo Html::tag('div', null, ['class' => 'clearfix']);
        echo Html::endForm();
        Modal::end();
        $this->registerJS();
        parent::run(); // TODO: Change the autogenerated stub
    }

    protected function renderFields()
    {
        $gridConfig = new \andrej2013\yiiboilerplate\modules\backend\models\GridConfig();
        $gridConfig = Yii::createObject([
            'class'   => \andrej2013\yiiboilerplate\modules\backend\models\GridConfig::class,
            'columns' => $this->columns,
            'model'   => $this->model,
            'grid_id' => $this->grid,
        ]);
        $columns = $gridConfig->parseColumns();
        $attributes = $gridConfig->getFindAttributes();
            $gridConfig = \andrej2013\yiiboilerplate\modules\backend\models\GridConfig::find()->andWhere([
                'user_id' => Yii::$app->user->id,
                'grid'    => $this->grid,
                'column'  => $attributes,
            ])->asArray()->indexBy('column')->all();
        
        echo Html::hiddenInput('grid', $this->grid);
        foreach ($columns as $column) {
            $config = $gridConfig[$column['attribute']];
            $value = $config && (int)$config['show'] === 0 ? 0 : 1;
            echo Html::beginTag('div', ['class' => 'col-sm-6']);
            echo Html::beginTag('div', ['class' => 'form-group']);
            echo Html::label($column['label'], "grid_config_" . $column['attribute'], ['class' => 'control-label']);
            echo SwitchInput::widget([
                'name'            => 'GridConfig[' . $column['attribute'] . ']',
                'id'              => 'grid_config_' . $column['attribute'],
                'value'           => $value,
                'pjaxContainerId' => $this->pjaxContainerId,
                'pluginOptions'   => [
                    'handleWidth' => 60,
                    'onText'      => Yii::t('app', 'Show'),
                    'offText'     => Yii::t('app', 'Hide'),
                ],
            ]);
            echo Html::endTag('div');
            echo Html::endTag('div');
        }
    }

    protected function registerJS()
    {
        $js = <<<JS
$('#grid_config_form').on('submit', function(e){
    e.preventDefault();
});
$(document.body).on('click', '#grid_config_form_submit', function() {
  var form = $('#grid_config_form');
    $('#grid_config').modal('hide');
    $(document.body).removeClass('modal-open');
    $('.modal-backdrop').remove();
  $.post({
    url: $(form).attr('action'),
    data: $(form).serialize(),
    success: function() {
        $.pjax.reload('#{$this->pjaxContainerId}', {timeout : false});
    }
  })
});
JS;
        $this->view->registerJs($js, View::POS_READY);
    }
}