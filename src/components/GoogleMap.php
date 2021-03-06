<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 7/21/2017
 * Time: 9:08 AM
 */

namespace andrej2013\yiiboilerplate\components;

use yii\base\Object;
use andrej2013\yiiboilerplate\components\GoogleApiLibrary;
use yii\helpers\FileHelper;

class GoogleMap extends Object
{

    const IFRAME = 'iframe';
    const IMAGE = 'image';
    /**
     * @var
     */
    public $address;
    /**
     * @var
     */

    public $latitude;
    /**
     * @var
     */
    public $longitude;
    /**
     * @var
     */
    public $width;
    /**
     * @var
     */
    public $height;
    /**
     * @var string
     */
    public $size = '150x150';
    /**
     * @var
     */
    public $language;
    /**
     * @var int
     */
    public $zoom = 9;
    /**
     * @var int
     */
    public $scale = 1;
    /**
     * @var string
     */
    public $type = 'terrain';
    /**
     * @var string
     */
    public $webroot = '@webroot/images/google_map';
    /**
     * @var string
     */
    public $imagePath = '/images/google_map';
    /**
     * @var
     */
    public $staticMapApiKey;
    /**
     * @var
     */
    public $geocodeApiKey;
    /**
     * @var string
     */
    public $format = self::IFRAME;
    /**
     * @var bool
     */
    public $useCache = true;
    /**
     * @var
     */
    public $fileName;
    /**
     * @var GoogleApiLibrary
     */
    protected $api;

    public function init()
    {
        $sizeParts = explode('x', $this->size);
        $this->api = new GoogleApiLibrary([
            // API Keys !!!
            'staticmap_api_key' => $this->staticMapApiKey != null ? $this->staticMapApiKey : \Yii::$app->params['googleMap']['staticMapApiKey'],
            'geocode_api_key' => $this->geocodeApiKey != null ? $this->geocodeApiKey : \Yii::$app->params['googleMap']['geocodeApiKey'],

            // Set basePath
            'webroot' => $this->webroot,

            // Image path and map iframe settings
            'map_image_path' => $this->imagePath,
            'map_type' => $this->type,
            'map_size' => ($this->width !== null && $this->height !== null) ? $this->width . 'x' . $this->height : $this->size,
            'map_sensor' => false,
            'map_zoom' => $this->zoom,
            'map_scale' => $this->scale,
            'map_marker_color' => 'red',
            'map_iframe_width' => ($this->width != null ? $this->width : $sizeParts[0]) . 'px', // %, px, em
            'map_iframe_height' => ($this->height != null ? $this->height : $sizeParts[1]) . 'px',  // %, px, em
            'map_language' => $this->language != null ? $this->language : \Yii::$app->language,
        ]);
        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * @return string
     */
    public function getIframe()
    {
        return $this->api->renderMapIframe(!empty($this->address) ? $this->address : null, implode(',', [$this->latitude, $this->longitude]));
    }

    /**
     * @return string
     */
    public function getImage()
    {
        FileHelper::createDirectory(\Yii::getAlias($this->webroot . DIRECTORY_SEPARATOR . $this->imagePath));
        $this->api->map_image_path = $this->imagePath;
        $this->api->webroot = $this->webroot;
        $this->api->fileName = $this->fileName;
        $file = \Yii::getAlias($this->webroot .
            DIRECTORY_SEPARATOR .
            $this->imagePath .
            DIRECTORY_SEPARATOR . $this->fileName . '.png');
        return 'data:image/png;base64,' .
        base64_encode(file_get_contents(\Yii::getAlias($this->webroot .
            DIRECTORY_SEPARATOR .
            $this->imagePath .
            DIRECTORY_SEPARATOR .
            ($this->useCache && file_exists($file) ? ($this->fileName . '.png') : $this->api->createImage(
                !empty($this->address) ? $this->address : null,
                implode(',', [$this->latitude, $this->longitude]))
            )
        )));
    }

    /**
     * @return string
     */
    public function render()
    {
        switch ($this->format) {
            case self::IMAGE:
                return $this->getImage();
                break;
            case self::IFRAME:
            default:
                return $this->getIframe();
        }
    }
}
