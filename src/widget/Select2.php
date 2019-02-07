<?php

namespace andrej2013\yiiboilerplate\widget;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use kartik\select2\Select2 as BaseSelect2;

class Select2 extends BaseSelect2
{
    public $is_popup;
    /**
     * Embeds the input group addon
     *
     * @param string $input
     *
     * @return string
     */
    protected function embedAddon($input)
    {
        if (empty($this->addon) || $this->is_popup) {
            return $input;
        }

        $group = ArrayHelper::getValue($this->addon, 'groupOptions', []);
        $size = isset($this->size) ? ' input-group-' . $this->size : '';
        Html::addCssClass($group, 'input-group' . $size);
        $prepend = ArrayHelper::getValue($this->addon, 'prepend', '');
        $append = ArrayHelper::getValue($this->addon, 'append', '');

        if ($this->pluginLoading) {
            Html::addCssClass($group, 'kv-input-group-hide');
            Html::addCssClass($group, 'group-' . $this->options['id']);
        }

        if (is_array($prepend)) {
            $content = ArrayHelper::getValue($prepend, 'content', '');
            if (isset($prepend['asButton']) && $prepend['asButton'] == true) {
                $prepend = Html::tag('div', $content, ['class' => 'input-group-btn']);
            } else {
                $prepend = Html::tag('span', $content, ['class' => 'input-group-addon']);
            }
            Html::addCssClass($group, 'select2-bootstrap-prepend');
        }

        if (is_array($append)) {
            $content = ArrayHelper::getValue($append, 'content', '');
            if (is_array($content)) {
                $oldAppend = $append;
                $append = '';
                foreach ($content as $con) {
                    if (isset($oldAppend['asButton']) && $oldAppend['asButton'] == true) {
                        $append .= Html::tag('div', $con, ['class' => 'input-group-btn']);
                    } else {
                        $append .= Html::tag('span', $con, ['class' => 'input-group-addon']);
                    }
                }
            } else {
                if (isset($append['asButton']) && $append['asButton'] == true) {
                    $append = Html::tag('div', $content, ['class' => 'input-group-btn']);
                } else {
                    $append = Html::tag('span', $content, ['class' => 'input-group-addon']);
                }
            }
            Html::addCssClass($group, 'select2-bootstrap-append');
        }

        $addonText = $prepend . $input . $append;
        $contentBefore = ArrayHelper::getValue($this->addon, 'contentBefore', '');
        $contentAfter = ArrayHelper::getValue($this->addon, 'contentAfter', '');
        return Html::tag('div', $contentBefore . $addonText . $contentAfter, $group);
    }
}
