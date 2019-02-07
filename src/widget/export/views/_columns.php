<?php
/**
 * @package   yii2-export
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2015 - 2017
 * @version   1.2.7
 * 
 * Column Selector View
 *
 */

$this->context->layout = '@app/views/layouts/box';

$regex = '|(\\'.DIRECTORY_SEPARATOR.'[^\\'.DIRECTORY_SEPARATOR.']*\\'.DIRECTORY_SEPARATOR.'[^\\'.DIRECTORY_SEPARATOR.']*\.php)$|';
preg_match($regex, __FILE__, $matches);
require Yii::getAlias('@vendor/kartik-v/yii2-export/views/'.$matches[1]);
