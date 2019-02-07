<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 7/20/2017
 * Time: 9:28 AM
 */

namespace andrej2013\yiiboilerplate\components;

use Endroid\QrCode\QrCode as BaseQrCode;
use yii\base\Object;

class QrCode extends Object
{
    const SVG = 'svg';
    const PNG = 'png';

    public $text;

    public $format = self::SVG;

    public $size = 150;

    /**
     * @var BaseQrCode
     */
    protected $generator;

    /**
     *
     */
    public function init()
    {
        $this->generator = new BaseQrCode(null);
        $this->generator->setMargin(0);
        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * @param      $filename
     * @param null $text
     * @param null $size
     * @param null $format
     */
    public function save($filename, $text = null, $size = null, $format = null)
    {
        $this->generator->setWriterByName($format !== null ? $format : $this->format);
        $this->generator->setSize($size !== null ? $size : $this->size);
        $this->generator->setText($text !== null ? $text : $this->text);

        $this->generator->writeFile($filename);
    }

    /**
     * @param null $text
     * @param null $size
     * @return string
     */
    public function get($text = null, $size = null)
    {
        $this->generator->setText($text !== null ? $text : $this->text);
        $this->generator->setSize($size !== null ? $size : $this->size);
        \Yii::$app->response->headers->set('Content-Type', $this->generator->getContentType());
        return $this->generator->writeDataUri();
    }

    /**
     * @param      $filename
     * @param      $text
     * @param null $size
     * @param null $format
     */
    public function staticSave($filename, $text, $size = null, $format = null)
    {
        $qr = new BaseQrCode($text);
        $qr->setSize($size !== null ? $size : 100);
        $qr->writeFile($filename);
    }

    /**
     * @param $text
     * @param $size
     * @return string
     */
    public static function staticGet($text, $size = null)
    {
        $qr = new BaseQrCode($text);
        $qr->setText($text);
        $qr->setSize($size !== null ? $size : 100);
        $qr->setMargin(0);
        \Yii::$app->response->headers->set('Content-Type', $qr->getContentType());
        return $qr->writeDataUri();
    }

}