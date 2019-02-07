<?php
/**
 * Created by PhpStorm.
 * User: ntesic
 * Date: 2/6/19
 * Time: 8:23 AM
 */

namespace yii\helpers;

use yii\helpers\BaseHtml;

class Html extends BaseHtml
{
    const PRESET_PRIMARY   = 'primary_color';
    const PRESET_SECONDARY = 'secondary_color';
    const PRESET_DANGER    = 'danger_color';

    const TYPE_INFO    = 'info';
    const TYPE_PRIMARY = 'primary';
    const TYPE_SUCCESS = 'success';
    const TYPE_DEFAULT = 'default';
    const TYPE_DANGER  = 'danger';
    const TYPE_WARNING = 'warning';

    public static function tag($name, $content = '', $options = [])
    {
         
        if ($name === 'button' || (
                $name === 'a' && (
                    (is_array($options['class']) && in_array('btn', $options['class'])) || 
                    (is_string($options['class']) && strpos($options['class'], 'btn') !== false)))) {
            if (\Yii::$app->params['style']['flat']) {
                BaseHtml::addCssClass($options, 'btn-flat');
            }
            if (!isset($options['skip']) || (isset($options['skip']) && !$options['skip'])) {
                if (! isset($options['color'])) {
                    BaseHtml::addCssClass($options, 'btn-' . (isset($options['preset']) ? \Yii::$app->params['style'][$options['preset']] : \Yii::$app->params['style']['primary_color']));
                    ArrayHelper::remove($options, 'preset');
                } else if (isset($options['color'])) {
                    BaseHtml::addCssClass($options, 'btn-' . (isset($options['color']) ? $options['color'] : \Yii::$app->params['style']['primary_color']));
                    ArrayHelper::remove($options, 'color');
                } else {
                    BaseHtml::addCssClass($options, 'btn-' . \Yii::$app->params['style']['primary_color']);
                }
                ArrayHelper::remove($options, 'skip');
            }
        }
        return parent::tag($name, $content, $options); // TODO: Change the autogenerated stub
    }
}