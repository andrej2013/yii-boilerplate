<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\helpers;

use Yii;
use yii\base\Arrayable;
use yii\base\InvalidParamException;

/**
 * BaseArrayHelper provides concrete implementation for [[ArrayHelper]].
 *
 * Do not use BaseArrayHelper. Use [[ArrayHelper]] instead.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ArrayHelper extends \yii\helpers\BaseArrayHelper
{
    /**
     * @inheritdoc
     */
    public static function toArray($object, $properties = [], $recursive = true, $expands = [])
    {
        if (is_array($object)) {
            if ($recursive) {
                foreach ($object as $key => $value) {
                    if (is_array($value) || is_object($value)) {
                        if (is_int($key)) {
                            $expand = $expands;
                        } elseif (isset($expands[$key])) {
                            $expand = $expands[$key];
                        } else {
                            $expand = [];
                        }
                        $object[$key] = static::toArray($value, $properties, true, $expand);
                    }
                }
            }

            return $object;
        } elseif (is_object($object)) {
            if (!empty($properties)) {
                $className = get_class($object);
                if (!empty($properties[$className])) {
                    $result = [];
                    foreach ($properties[$className] as $key => $name) {
                        if (is_int($key)) {
                            $result[$name] = $object->$name;
                        } else {
                            $result[$key] = static::getValue($object, $name);
                        }
                    }

                    return $recursive ? static::toArray($result, $properties) : $result;
                }
            }
            if ($object instanceof Arrayable) {
                $result = $object->toArray([], $expands, $recursive);
            } else {
                $result = [];
                foreach ($object as $key => $value) {
                    $result[$key] = $value;
                }
            }

            return $recursive ? static::toArray($result, [], true, $expands) : $result;
        } else {
            return [$object];
        }
    }
}
