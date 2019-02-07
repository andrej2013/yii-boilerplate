<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 7/20/2017
 * Time: 11:46 AM
 */

namespace andrej2013\yiiboilerplate\traits;

use andrej2013\yiiboilerplate\components\QrCode;
use yii\base\Exception;

trait QrTrait
{

    /**
     * @param      $attribute
     * @param null $options
     * @return string
     * @throws Exception
     */
    public function renderQrCode($attribute, $options = null)
    {
        if (!$this->hasAttribute($attribute)) {
            throw new Exception("getQrCode: Unknown attribute " . $attribute . " on model " . get_class($this));
        }
        $qrCode = new QrCode();

        if (isset($options['size'])) {
            $qrCode->size = $options['size'];
        }
        if (isset($options['format'])) {
            $qrCode->format = $options['format'];
        }
        return $qrCode->get($this->$attribute);
    }
}