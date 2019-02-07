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

use andrej2013\yiiboilerplate\components\GoogleMap;
use yii\base\Exception;

trait GoogleMapTrait
{

    /**
     * @param      $attribute
     * @param null $options
     * @return string
     * @throws Exception
     */
    public function renderGoogleMap($attribute, $options = [])
    {
        if (!$this->hasAttribute($attribute)) {
            throw new Exception("renderGoogleMap: Unknown attribute " . $attribute . " on model " . get_class($this));
        }
        $map = new GoogleMap();
        $parts = explode('|', $this->$attribute);
        $map->longitude = $parts[0];
        $map->latitude = $parts[1];
        $map->address = $parts[2];
        if ($options['format'] == GoogleMap::IMAGE) {
            $ref = new \ReflectionClass($this);
            $options['imagePath'] = $ref->getShortName() . DIRECTORY_SEPARATOR . $this->id;
            $options['fileName'] = $this->id;
        }
        foreach ($options as $key => $value) {
            if ($map->hasProperty($key)) {
                $map->$key = $value;
            }
        }
        return $map->render();
    }
}