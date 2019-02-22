<?php
/**
 * Created by PhpStorm.
 * User: ntesic
 * Date: 2/20/19
 * Time: 12:21 AM
 */

namespace andrej2013\yiiboilerplate\templates\crud\providers\base;


class Provider extends \schmunk42\giiant\base\Provider
{
    /**
     * @var \andrej2013\yiiboilerplate\templates\crud\Generator
     */
    public $generator;
    
    public $type = null;

    /**
     * @param $attribute
     * @return bool
     */
    public function condition($attribute)
    {
        $column = $this->generator->getColumnByAttribute($attribute);
        if (! $column) {
            return false;
        }
        if ((is_array($this->type) && in_array($this->generator->getAttributeType($attribute), $this->type)) || $this->generator->getAttributeType($attribute) == $this->type) {
            return true;
        }
        return false;
    }
}