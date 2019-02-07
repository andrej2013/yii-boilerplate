<?php

namespace andrej2013\yiiboilerplate\grid;

use kartik\grid\GridView as BaseGridView;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Yii;
use yii\helpers\Html;

/**
 * Class GridView
 * @package andrej2013\yiiboilerplate\grid
 * Todo: move from src/grid to something better?
 */
class GridView extends BaseGridView
{
    /**
     * Render the {export} template in grids.
     * We do this (and rewrite the whole function) because we have custom external calls
     * that need to be handled differently (ex. google spreadsheet)
     * @return string
     */
    public function renderExport()
    {
        if ($this->export === false || !is_array($this->export) ||
            empty($this->exportConfig) || !is_array($this->exportConfig)
        ) {
            return '';
        }
        $title = $this->export['label'];
        $icon = $this->export['icon'];
        $options = $this->export['options'];
        $menuOptions = $this->export['menuOptions'];
        $title = ($icon == '') ? $title : "<i class='{$icon}'></i> {$title}";
        if (!isset($this->_module->downloadAction)) {
            $action = ["/{$this->moduleId}/export/download"];
        } else {
            $action = (array) $this->_module->downloadAction;
        }
        $encoding = ArrayHelper::getValue($this->export, 'encoding', 'utf-8');
        $bom = ArrayHelper::getValue($this->export, 'bom', true);
        $target = ArrayHelper::getValue($this->export, 'target', self::TARGET_POPUP);

        $formOptions = [
            'class' => 'kv-export-form',
            'style' => 'display:none',
            'target' => ($target == self::TARGET_POPUP) ? 'kvDownloadDialog' : $target,
        ];

        $form = Html::beginForm($action, 'post', $formOptions) . "\n" .
            Html::hiddenInput('module_id', $this->moduleId) . "\n" .
            Html::hiddenInput('export_hash') . "\n" .
            Html::hiddenInput('export_filetype') . "\n" .
            Html::hiddenInput('export_filename') . "\n" .
            Html::hiddenInput('export_mime') . "\n" .
            Html::hiddenInput('export_config') . "\n" .
            Html::hiddenInput('export_encoding', $encoding) . "\n" .
            Html::hiddenInput('export_bom', $bom) . "\n" .
            Html::textarea('export_content') . "\n" .
            Html::endForm();
        $items = empty($this->export['header']) ? [] : [$this->export['header']];
        foreach ($this->exportConfig as $format => $setting) {
            // If we are an external provider, handle the item differently with just the
            // label. The rest will be handled by the gridview/export controller.
            // This could probably be handled differently in the future.
            if ($setting['external']) {
                $label = $setting['label'];
                $items[] = $label;
            } else {
                $iconOptions = ArrayHelper::getValue($setting, 'iconOptions', []);
                Html::addCssClass($iconOptions, $iconPrefix . $setting['icon']);
                $label = (empty($setting['icon']) || $setting['icon'] == '') ? $setting['label'] :
                    Html::tag('i', '', $iconOptions) . ' ' . $setting['label'];
                $mime = ArrayHelper::getValue($setting, 'mime', 'text/plain');
                $config = ArrayHelper::getValue($setting, 'config', []);
                if ($format === self::JSON) {
                    unset($config['jsonReplacer']);
                }
                $dataToHash = $this->moduleId . $setting['filename'] . $mime . $encoding . $bom . Json::encode($config);
                $hash = Yii::$app->security->hashData($dataToHash, $this->_module->exportEncryptSalt);
                $items[] = [
                    'label' => $label,
                    'url' => '#',
                    'linkOptions' => [
                        'class' => 'export-' . $format,
                        'data-mime' => $mime,
                        'data-hash' => $hash,
                    ],
                    'options' => $setting['options'],
                ];
            }
        }

        $itemsBefore = ArrayHelper::getValue($this->export, 'itemsBefore', []);
        $itemsAfter = ArrayHelper::getValue($this->export, 'itemsAfter', []);
        $items = ArrayHelper::merge($itemsBefore, $items, $itemsAfter);
        return ButtonDropdown::widget([
            'label' => $title,
            'dropdown' => ['items' => $items, 'encodeLabels' => false, 'options' => $menuOptions],
            'options' => $options,
            'containerOptions' => $this->exportContainer,
            'encodeLabel' => false,
        ]) . $form;
    }

    /**
     * @return mixed
     */
    public function init()
    {
        // Remove empty columns instead of showing empty ones.
        $this->columns = array_filter($this->columns);
        return parent::init(); // TODO: Change the autogenerated stub
    }
}
